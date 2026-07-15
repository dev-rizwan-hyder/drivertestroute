<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\DrivingRoute;
use App\Models\RoutePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class DrivingRouteController extends Controller
{
    public function home()
    {
        $citySchemaReady = $this->citySchemaReady();
        $featuredRoutesQuery = DrivingRoute::where('is_active', true)
            ->withCount('points')
            ->latest();

        if ($citySchemaReady) {
            $featuredRoutesQuery->with('cityModel');
        }

        $featuredRoutes = $featuredRoutesQuery->take(6)->get();

        $cities = $citySchemaReady
            ? City::with(['routes' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('name')
                ->get()
            : collect();

        $stats = [
            'routes' => DrivingRoute::where('is_active', true)->count(),
            'cities' => $citySchemaReady
                ? City::whereHas('routes', fn ($query) => $query->where('is_active', true))->count()
                : DrivingRoute::where('is_active', true)->whereNotNull('city')->distinct('city')->count('city'),
            'starts' => (int) RoutePurchase::where('payment_status', 'paid')->sum('access_used'),
        ];

        return view('home', compact('featuredRoutes', 'stats', 'cities'));
    }

    public function index(Request $request)
    {
        $citySchemaReady = $this->citySchemaReady();
        $selectedCity = $citySchemaReady && $request->filled('city')
            ? City::find($request->integer('city'))
            : null;
        $selectedPackageType = $request->input('package_type');

        $routesQuery = DrivingRoute::where('is_active', true)
            ->withCount('points')
            ->latest();

        if ($citySchemaReady) {
            $routesQuery->with('cityModel');
        }

        if ($selectedPackageType) {
            $routesQuery->where('package_type', $selectedPackageType);
        }

        if ($selectedCity) {
            $routesQuery->where(function ($query) use ($selectedCity) {
                $query->where('city_id', $selectedCity->id)
                    ->orWhere(function ($legacyQuery) use ($selectedCity) {
                        $legacyQuery->whereNull('city_id')
                            ->where('city', $selectedCity->name);
                    });
            });
        }

        $routes = $routesQuery->get();

        $cities = $citySchemaReady
            ? City::withCount(['routes as active_routes_count' => function ($query) use ($selectedPackageType) {
                    $query->where('is_active', true);
                    if ($selectedPackageType) {
                        $query->where('package_type', $selectedPackageType);
                    }
                }])
                ->orderBy('name')
                ->get()
            : collect();

        $purchases = auth()->check()
            ? RoutePurchase::where('user_id', auth()->id())
                ->where('payment_status', 'paid')
                ->get()
                ->keyBy('driving_route_id')
            : collect();

        return view('driving-routes.index', compact('routes', 'purchases', 'cities', 'selectedCity', 'selectedPackageType'));
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
        $stripeKey = config('services.stripe.key') ?: 'pk_test_placeholder';
        $stripeCurrency = strtoupper((string) config('services.stripe.currency', 'usd'));

        $paypalEnabled = $this->paypalEnabled($drivingRoute);
        $paypalClientId = config('services.paypal.client_id') ?: 'sb';
        $paypalCurrency = strtoupper((string) config('services.paypal.currency', 'USD'));
        $paypalMode = config('services.paypal.mode', 'sandbox');

        $squareEnabled = $this->squareEnabled($drivingRoute);
        $squareAppId = config('services.square.application_id') ?: 'sandbox-sq-app-id-placeholder';
        $squareLocationId = config('services.square.location_id') ?: 'sandbox-sq-location-id-placeholder';
        $squareEnv = config('services.square.environment', 'sandbox');

        return view('driving-routes.checkout', compact(
            'drivingRoute', 'purchase', 'stripeCurrency', 'stripeEnabled', 'stripeKey',
            'paypalEnabled', 'paypalClientId', 'paypalCurrency', 'paypalMode',
            'squareEnabled', 'squareAppId', 'squareLocationId', 'squareEnv'
        ));
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

        if (empty(config('services.stripe.secret')) || config('services.stripe.secret') === 'sk_test_placeholder') {
            return response()->json([
                'client_secret' => 'pi_mock_secret_' . bin2hex(random_bytes(16)),
                'payment_intent_id' => 'pi_mock_' . bin2hex(random_bytes(12)),
            ]);
        }

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

    public function createPaypalOrder(Request $request, DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active, 404);

        if (! $this->paypalEnabled($drivingRoute)) {
            return response()->json([
                'message' => 'PayPal is not configured for this checkout.',
            ], 422);
        }

        $validated = $request->validate($this->checkoutValidationRules(false));

        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        if (empty($clientId) || empty($secret) || $clientId === 'sb') {
            return response()->json([
                'id' => 'PAYID-MOCK-' . strtoupper(bin2hex(random_bytes(8))),
            ]);
        }

        $mode = config('services.paypal.mode', 'sandbox');
        $baseUrl = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $tokenResponse = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post("$baseUrl/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($tokenResponse->failed()) {
            return response()->json([
                'message' => 'Could not authenticate with PayPal.',
            ], 422);
        }

        $accessToken = $tokenResponse->json('access_token');

        $orderResponse = Http::withToken($accessToken)
            ->post("$baseUrl/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => config('services.paypal.currency', 'USD'),
                        'value' => number_format((float) $drivingRoute->price, 2, '.', ''),
                    ],
                    'description' => 'Driver Test Route: '.$drivingRoute->title,
                ]],
            ]);

        if ($orderResponse->failed()) {
            return response()->json([
                'message' => $orderResponse->json('error_description') ?: 'PayPal could not create an order.',
            ], 422);
        }

        return response()->json([
            'id' => $orderResponse->json('id'),
        ]);
    }

    public function checkoutStore(Request $request, DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active, 404);

        $paymentProvider = $request->input('payment_provider', 'local');
        $paymentIntentRequired = in_array($paymentProvider, ['stripe', 'paypal', 'square']);
        $validated = $request->validate($this->checkoutValidationRules($paymentIntentRequired));

        $startsIncluded = max(1, (int) $drivingRoute->access_limit);
        $price = (float) $drivingRoute->price;
        $paymentId = 'checkout-'.now()->format('YmdHis');

        if ($paymentProvider === 'stripe' && ! $this->stripeEnabled($drivingRoute)) {
            $paymentProvider = 'local';
        }
        if ($paymentProvider === 'paypal' && ! $this->paypalEnabled($drivingRoute)) {
            $paymentProvider = 'local';
        }
        if ($paymentProvider === 'square' && ! $this->squareEnabled($drivingRoute)) {
            $paymentProvider = 'local';
        }

        if ($paymentProvider === 'stripe') {
            if (empty(config('services.stripe.secret')) || config('services.stripe.secret') === 'sk_test_placeholder') {
                $paymentId = $validated['payment_intent_id'] ?: 'pi_mock_' . bin2hex(random_bytes(12));
            } else {
                $intent = $this->validatedStripePaymentIntent($validated['payment_intent_id'], $drivingRoute);
                $paymentId = $intent['id'];
            }
        } elseif ($paymentProvider === 'paypal') {
            $paymentId = $this->capturePaypalOrder($validated['payment_intent_id'], $drivingRoute);
        } elseif ($paymentProvider === 'square') {
            $paymentId = $this->processSquarePayment($validated['payment_intent_id'], $drivingRoute);
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
        return ! $route || (float) $route->price > 0;
    }

    private function paypalEnabled(?DrivingRoute $route = null): bool
    {
        return ! $route || (float) $route->price > 0;
    }

    private function squareEnabled(?DrivingRoute $route = null): bool
    {
        return ! $route || (float) $route->price > 0;
    }

    private function capturePaypalOrder(string $orderId, DrivingRoute $drivingRoute): string
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        if (empty($clientId) || empty($secret) || $clientId === 'sb') {
            return $orderId;
        }

        $mode = config('services.paypal.mode', 'sandbox');
        $baseUrl = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $tokenResponse = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post("$baseUrl/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($tokenResponse->failed()) {
            throw ValidationException::withMessages([
                'payment' => 'Could not authenticate with PayPal to capture order.',
            ]);
        }

        $accessToken = $tokenResponse->json('access_token');

        $captureResponse = Http::withToken($accessToken)
            ->post("$baseUrl/v2/checkout/orders/$orderId/capture");

        if ($captureResponse->failed()) {
            $orderResponse = Http::withToken($accessToken)
                ->get("$baseUrl/v2/checkout/orders/$orderId");

            if ($orderResponse->successful() && $orderResponse->json('status') === 'COMPLETED') {
                return $orderId;
            }

            throw ValidationException::withMessages([
                'payment' => $captureResponse->json('error_description') ?: 'PayPal order capture failed.',
            ]);
        }

        if ($captureResponse->json('status') !== 'COMPLETED') {
            throw ValidationException::withMessages([
                'payment' => 'PayPal payment status is not completed.',
            ]);
        }

        return $orderId;
    }

    private function processSquarePayment(string $token, DrivingRoute $drivingRoute): string
    {
        $accessToken = config('services.square.access_token');

        if (empty($accessToken) || $accessToken === 'sandbox-sq-access-token-placeholder') {
            return 'sq_payment_mock_' . bin2hex(random_bytes(8));
        }

        $env = config('services.square.environment', 'sandbox');
        $baseUrl = $env === 'production' ? 'https://connect.squareup.com' : 'https://connect.squareupsandbox.com';

        $response = Http::withToken($accessToken)
            ->post("$baseUrl/v2/payments", [
                'source_id' => $token,
                'idempotency_key' => uniqid('sq_', true),
                'amount_money' => [
                    'amount' => (int) round((float) $drivingRoute->price * 100),
                    'currency' => config('services.square.currency', 'USD'),
                ],
                'location_id' => config('services.square.location_id'),
                'note' => 'Driver Test Route: '.$drivingRoute->title,
            ]);

        if ($response->failed()) {
            $errorMsg = $response->json('errors.0.detail') ?: 'Square payment processing failed.';
            throw ValidationException::withMessages([
                'payment' => $errorMsg,
            ]);
        }

        $paymentStatus = $response->json('payment.status');
        if (! in_array($paymentStatus, ['APPROVED', 'COMPLETED'])) {
            throw ValidationException::withMessages([
                'payment' => 'Square payment status is not approved or completed.',
            ]);
        }

        return $response->json('payment.id');
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

        $citySchemaReady = $this->citySchemaReady();
        $relations = ['points'];

        if ($citySchemaReady) {
            $relations[] = 'cityModel';
        }

        $drivingRoute->load($relations);

        $relatedRoutes = DrivingRoute::query()
            ->where('is_active', true)
            ->whereKeyNot($drivingRoute->id);

        if ($citySchemaReady) {
            $relatedRoutes->with('cityModel');
        }

        if ($citySchemaReady && $drivingRoute->city_id) {
            $relatedRoutes->where('city_id', $drivingRoute->city_id);
        } else {
            $relatedRoutes->where('city', $drivingRoute->city);
        }

        $relatedRoutes = $relatedRoutes->latest()->take(3)->get();

        return view('driving-routes.show', [
            'route' => $drivingRoute,
            'points' => $drivingRoute->points,
            'purchase' => $purchase,
            'remainingStarts' => $remainingStarts,
            'relatedRoutes' => $relatedRoutes,
        ]);
    }

    private function citySchemaReady(): bool
    {
        return Schema::hasTable('cities') && Schema::hasColumn('driving_routes', 'city_id');
    }
}
