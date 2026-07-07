<?php

namespace App\Http\Controllers;

use App\Models\DrivingRoute;
use App\Models\RoutePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class DrivingRouteController extends Controller
{
    public function home()
    {
        $featuredRoutes = DrivingRoute::where('is_active', true)
            ->withCount('points')
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'routes' => DrivingRoute::where('is_active', true)->count(),
            'cities' => DrivingRoute::where('is_active', true)->distinct('city')->count('city'),
            'starts' => (int) RoutePurchase::where('payment_status', 'paid')->sum('access_used'),
        ];

        return view('home', compact('featuredRoutes', 'stats'));
    }

    public function index()
    {
        $routes = DrivingRoute::where('is_active', true)
            ->withCount('points')
            ->latest()
            ->get();

        $purchases = auth()->check()
            ? RoutePurchase::where('user_id', auth()->id())
                ->where('payment_status', 'paid')
                ->get()
                ->keyBy('driving_route_id')
            : collect();

        return view('driving-routes.index', compact('routes', 'purchases'));
    }

    public function myRoutes()
    {
        $purchases = RoutePurchase::with(['route.points'])
            ->where('user_id', auth()->id())
            ->where('payment_status', 'paid')
            ->latest('purchased_at')
            ->get()
            ->filter(fn (RoutePurchase $purchase) => $purchase->route !== null);

        return view('driving-routes.my-routes', compact('purchases'));
    }

    public function buy(DrivingRoute $drivingRoute)
    {
        return redirect()->route('driving-routes.checkout', $drivingRoute);
    }

    public function checkout(DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active, 404);

        $purchase = $drivingRoute->activePurchaseFor(auth()->user());
        $stripeEnabled = $this->stripeEnabled($drivingRoute);
        $stripeKey = config('services.stripe.key');
        $stripeCurrency = strtoupper((string) config('services.stripe.currency', 'usd'));

        return view('driving-routes.checkout', compact('drivingRoute', 'purchase', 'stripeCurrency', 'stripeEnabled', 'stripeKey'));
    }

    public function paymentIntent(Request $request, DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active, 404);

        if (! $this->stripeEnabled($drivingRoute)) {
            return response()->json([
                'message' => 'Stripe is not configured for this checkout.',
            ], 422);
        }

        $validated = $request->validate($this->checkoutValidationRules(false));
        $amount = $this->stripeAmount((float) $drivingRoute->price);
        $currency = strtolower((string) config('services.stripe.currency', 'usd'));

        $response = Http::asForm()
            ->withToken((string) config('services.stripe.secret'))
            ->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_types' => ['card'],
                'description' => 'Driver Test Route: '.$drivingRoute->title,
                'receipt_email' => $validated['billing_email'],
                'metadata' => [
                    'user_id' => (string) auth()->id(),
                    'driving_route_id' => (string) $drivingRoute->id,
                    'student_name' => $validated['student_name'],
                    'student_email' => $validated['student_email'],
                ],
            ]);

        if ($response->failed()) {
            return response()->json([
                'message' => $response->json('error.message') ?: 'Stripe could not create a payment.',
            ], 422);
        }

        return response()->json([
            'client_secret' => $response->json('client_secret'),
            'payment_intent_id' => $response->json('id'),
        ]);
    }

    public function checkoutStore(Request $request, DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active, 404);

        $stripeEnabled = $this->stripeEnabled($drivingRoute);
        $validated = $request->validate($this->checkoutValidationRules($stripeEnabled));

        $startsIncluded = max(1, (int) $drivingRoute->access_limit);
        $price = (float) $drivingRoute->price;
        $paymentId = 'checkout-'.now()->format('YmdHis');
        $paymentProvider = 'local';

        if ($stripeEnabled) {
            $intent = $this->validatedStripePaymentIntent($validated['payment_intent_id'], $drivingRoute);
            $paymentId = $intent['id'];
            $paymentProvider = 'stripe';
        }

        DB::transaction(function () use ($drivingRoute, $paymentId, $paymentProvider, $startsIncluded, $price, $validated) {
            $purchase = RoutePurchase::where('user_id', auth()->id())
                ->where('driving_route_id', $drivingRoute->id)
                ->lockForUpdate()
                ->first();

            $purchaseData = [
                'payment_status' => 'paid',
                'payment_provider' => $paymentProvider,
                'payment_id' => $paymentId,
                'student_name' => $validated['student_name'],
                'student_email' => $validated['student_email'],
                'student_phone' => $validated['student_phone'],
                'student_city' => $validated['student_city'] ?? null,
                'student_test_date' => $validated['student_test_date'] ?? null,
                'student_notes' => $validated['student_notes'] ?? null,
                'billing_name' => $validated['billing_name'],
                'billing_email' => $validated['billing_email'],
            ];

            if (! $purchase) {
                RoutePurchase::create($purchaseData + [
                    'user_id' => auth()->id(),
                    'driving_route_id' => $drivingRoute->id,
                    'amount_paid' => $price,
                    'access_limit' => $startsIncluded,
                    'access_used' => 0,
                    'purchased_at' => now(),
                ]);

                return;
            }

            if ($purchase->payment_id === $paymentId) {
                $purchase->update($purchaseData);

                return;
            }

            $purchase->update([
                ...$purchaseData,
                'amount_paid' => (float) $purchase->amount_paid + $price,
                'access_limit' => (int) $purchase->access_limit + $startsIncluded,
                'purchased_at' => $purchase->purchased_at ?? now(),
            ]);
        });

        return redirect()
            ->route('driving-routes.show', $drivingRoute)
            ->with('success', 'Checkout complete. Your route map is unlocked.');
    }

    private function checkoutValidationRules(bool $paymentIntentRequired): array
    {
        return [
            'student_name' => ['required', 'string', 'max:255'],
            'student_email' => ['required', 'email', 'max:255'],
            'student_phone' => ['required', 'string', 'max:30'],
            'student_city' => ['nullable', 'string', 'max:120'],
            'student_test_date' => ['nullable', 'date'],
            'student_notes' => ['nullable', 'string', 'max:1000'],
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_email' => ['required', 'email', 'max:255'],
            'payment_intent_id' => [$paymentIntentRequired ? 'required' : 'nullable', 'string', 'max:255'],
            'terms' => ['accepted'],
        ];
    }

    private function stripeEnabled(?DrivingRoute $route = null): bool
    {
        return filled(config('services.stripe.key'))
            && filled(config('services.stripe.secret'))
            && (! $route || (float) $route->price > 0);
    }

    private function stripeAmount(float $price): int
    {
        return max(1, (int) round($price * 100));
    }

    private function validatedStripePaymentIntent(string $paymentIntentId, DrivingRoute $drivingRoute): array
    {
        $response = Http::withToken((string) config('services.stripe.secret'))
            ->get('https://api.stripe.com/v1/payment_intents/'.$paymentIntentId);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'payment' => $response->json('error.message') ?: 'Stripe payment verification failed.',
            ]);
        }

        $intent = $response->json();
        $currency = strtolower((string) config('services.stripe.currency', 'usd'));

        if (($intent['status'] ?? null) !== 'succeeded') {
            throw ValidationException::withMessages([
                'payment' => 'Card payment was not completed.',
            ]);
        }

        if ((int) ($intent['amount'] ?? 0) !== $this->stripeAmount((float) $drivingRoute->price) || ($intent['currency'] ?? null) !== $currency) {
            throw ValidationException::withMessages([
                'payment' => 'Stripe payment amount does not match this route.',
            ]);
        }

        if (($intent['metadata']['user_id'] ?? null) !== (string) auth()->id() || ($intent['metadata']['driving_route_id'] ?? null) !== (string) $drivingRoute->id) {
            throw ValidationException::withMessages([
                'payment' => 'Stripe payment does not match this checkout session.',
            ]);
        }

        return $intent;
    }

    public function start(DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active || auth()->user()->is_admin, 404);

        if (auth()->user()->is_admin) {
            return response()->json([
                'remaining_starts' => null,
                'message' => 'Admin preview does not use route starts.',
            ]);
        }

        $purchase = DB::transaction(function () use ($drivingRoute) {
            $purchase = RoutePurchase::where('user_id', auth()->id())
                ->where('driving_route_id', $drivingRoute->id)
                ->where('payment_status', 'paid')
                ->lockForUpdate()
                ->first();

            abort_if(! $purchase, 403, 'Please buy this route first.');
            abort_if(! $purchase->hasRemainingStarts(), 402, 'No map starts remaining. Buy this route again to continue.');

            $purchase->update([
                'access_used' => (int) $purchase->access_used + 1,
                'last_accessed_at' => now(),
            ]);

            return $purchase;
        });

        return response()->json([
            'remaining_starts' => $purchase->remainingStarts(),
            'access_used' => $purchase->access_used,
        ]);
    }

    public function show(DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active || auth()->user()->is_admin, 404);

        $purchase = null;
        $remainingStarts = null;

        if (! auth()->user()->is_admin) {
            $purchase = $drivingRoute->activePurchaseFor(auth()->user());

            if (! $purchase) {
                return redirect()
                    ->route('driving-routes.checkout', $drivingRoute)
                    ->with('error', 'Please buy this route first to access the map.');
            }

            if (! $purchase->hasRemainingStarts()) {
                return redirect()
                    ->route('driving-routes.checkout', $drivingRoute)
                    ->with('error', 'No map starts remaining. Buy this route again to continue.');
            }

            $remainingStarts = $purchase->remainingStarts();
        }

        if (auth()->user()->is_admin) {
            $remainingStarts = null;
        }

        $drivingRoute->load('points');

        return view('driving-routes.show', [
            'route' => $drivingRoute,
            'points' => $drivingRoute->points,
            'purchase' => $purchase,
            'remainingStarts' => $remainingStarts,
        ]);
    }
}
