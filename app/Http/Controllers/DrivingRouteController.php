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

    /** Map package_type slug → human-readable label (alphabetical order). */
    public static array $packageLabels = [
        'g1' => 'G2 Test Routes',
        'g2' => 'G Test Routes',
    ];

    public function index(Request $request)
    {
        $citySchemaReady = $this->citySchemaReady();
        $selectedCity = $citySchemaReady && $request->filled('city')
            ? City::find($request->integer('city'))
            : null;
        $selectedPackageType = $request->input('package_type');
        $search = trim((string) $request->input('search', ''));

        $routesQuery = DrivingRoute::where('is_active', true)
            ->withCount('points')
            ->orderBy('title');

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

        if ($search !== '') {
            // Collect package_type slugs whose label matches the search term
            $matchingPackageTypes = collect(self::$packageLabels)
                ->filter(fn ($label) => str_contains(strtolower($label), strtolower($search)))
                ->keys()
                ->toArray();

            $routesQuery->where(function ($q) use ($search, $matchingPackageTypes) {
                $q->where('title', 'like', '%'.$search.'%');
                if (! empty($matchingPackageTypes)) {
                    $q->orWhereIn('package_type', $matchingPackageTypes);
                }
            });
        }

        $routes = $routesQuery->get();

        // Package pills sorted alphabetically by label
        $packageOptions = collect(self::$packageLabels)->sortKeys();

        $cities = $citySchemaReady
            ? City::withCount(['routes as active_routes_count' => function ($query) use ($selectedPackageType, $search) {
                    $query->where('is_active', true);
                    if ($selectedPackageType) {
                        $query->where('package_type', $selectedPackageType);
                    }
                    if ($search !== '') {
                        $matchingPackageTypes = collect(DrivingRouteController::$packageLabels)
                            ->filter(fn ($label) => str_contains(strtolower($label), strtolower($search)))
                            ->keys()
                            ->toArray();
                        $query->where(function ($q) use ($search, $matchingPackageTypes) {
                            $q->where('title', 'like', '%'.$search.'%');
                            if (! empty($matchingPackageTypes)) {
                                $q->orWhereIn('package_type', $matchingPackageTypes);
                            }
                        });
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

        // Partial/AJAX request: return JSON with rendered cards HTML + result count
        if ($request->boolean('partial')) {
            $html = view('driving-routes._cards', compact(
                'routes', 'purchases', 'search', 'selectedPackageType', 'selectedCity'
            ))->render();

            return response()->json([
                'html'  => $html,
                'count' => $routes->count(),
            ]);
        }

        return view('driving-routes.index', compact(
            'routes', 'purchases', 'cities', 'selectedCity',
            'selectedPackageType', 'search', 'packageOptions'
        ));
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

        $paypalEnabled = $this->paypalEnabled($drivingRoute);
        $paypalClientId = config('services.paypal.client_id') ?: 'sb';
        $paypalCurrency = strtoupper((string) config('services.paypal.currency', 'USD'));
        $paypalMode = config('services.paypal.mode', 'sandbox');

        return view('driving-routes.checkout', compact(
            'drivingRoute', 'purchase',
            'paypalEnabled', 'paypalClientId', 'paypalCurrency', 'paypalMode'
        ));
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
        $mode = strtolower((string) config('services.paypal.mode', 'sandbox'));

        if (empty($clientId) || $clientId === 'sb') {
            return response()->json([
                'message' => 'PayPal Client ID is not configured. Please add PAYPAL_SANDBOX_CLIENT_ID or PAYPAL_LIVE_CLIENT_ID to your .env file.',
            ], 422);
        }

        if (empty($secret)) {
            $secretVar = in_array($mode, ['live', 'production']) ? 'PAYPAL_LIVE_SECRET' : 'PAYPAL_SANDBOX_SECRET';
            return response()->json([
                'message' => "PayPal Secret is missing in .env for " . strtoupper($mode) . " mode. Please set {$secretVar} (or PAYPAL_SECRET) in .env.",
            ], 422);
        }

        $baseUrl = in_array($mode, ['live', 'production']) ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $tokenResponse = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post("$baseUrl/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($tokenResponse->failed()) {
            return response()->json([
                'message' => 'Could not authenticate with PayPal in ' . strtoupper($mode) . ' mode (' . $tokenResponse->status() . '). Please verify your PayPal Client ID and Secret in .env.',
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
            $errorMsg = $orderResponse->json('error_description')
                ?: ($orderResponse->json('message')
                ?: ($orderResponse->json('details.0.description') ?: 'PayPal could not create an order.'));

            return response()->json([
                'message' => 'PayPal Order Error: ' . $errorMsg,
            ], 422);
        }

        return response()->json([
            'id' => $orderResponse->json('id'),
        ]);
    }

    public function checkoutStore(Request $request, DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active, 404);

        $paymentProvider = $request->input('payment_provider', 'paypal');
        $paymentIntentRequired = in_array($paymentProvider, ['paypal']);
        $validated = $request->validate($this->checkoutValidationRules($paymentIntentRequired));

        $startsIncluded = max(1, (int) $drivingRoute->access_limit);
        $price = (float) $drivingRoute->price;
        $paymentId = 'checkout-'.now()->format('YmdHis');

        if ($paymentProvider === 'paypal' && ! $this->paypalEnabled($drivingRoute)) {
            $paymentProvider = 'local';
        }

        if ($paymentProvider === 'paypal') {
            $paymentId = $this->capturePaypalOrder($validated['payment_intent_id'] ?: 'PAYID-MOCK-LOCAL', $drivingRoute);
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
            ->route('driving-routes.my')
            ->with('success', 'Checkout complete. Your route is ready under My Routes.');
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

    private function paypalEnabled(?DrivingRoute $route = null): bool
    {
        return ! $route || (float) $route->price > 0;
    }

    private function capturePaypalOrder(string $orderId, DrivingRoute $drivingRoute): string
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        if (empty($clientId) || empty($secret) || $clientId === 'sb' || str_starts_with($orderId, 'PAYID-MOCK-')) {
            return $orderId;
        }

        $mode = strtolower((string) config('services.paypal.mode', 'sandbox'));
        $baseUrl = in_array($mode, ['live', 'production']) ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $tokenResponse = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post("$baseUrl/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($tokenResponse->failed()) {
            throw ValidationException::withMessages([
                'payment' => 'Could not authenticate with PayPal to capture order in ' . strtoupper($mode) . ' mode.',
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

            $errorMessage = $captureResponse->json('error_description')
                ?: ($captureResponse->json('message')
                ?: ($captureResponse->json('details.0.description') ?: 'PayPal order capture failed.'));

            throw ValidationException::withMessages([
                'payment' => $errorMessage,
            ]);
        }

        if ($captureResponse->json('status') !== 'COMPLETED') {
            throw ValidationException::withMessages([
                'payment' => 'PayPal payment status is ' . ($captureResponse->json('status') ?: 'not completed') . '.',
            ]);
        }

        return $orderId;
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

            DB::transaction(function () use ($purchase) {
                $purchase->lockForUpdate();
                $purchase->update([
                    'access_used' => (int) $purchase->access_used + 1,
                    'last_accessed_at' => now(),
                ]);
            });
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
            'mapsKey' => config('services.google.maps_key'),
        ]);
    }

    public function details(DrivingRoute $drivingRoute)
    {
        abort_unless($drivingRoute->is_active || auth()->user()?->is_admin, 404);

        $citySchemaReady = $this->citySchemaReady();
        $relations = ['points'];

        if ($citySchemaReady) {
            $relations[] = 'cityModel';
        }

        $drivingRoute->load($relations);

        $waypointsList = $drivingRoute->parsed_waypoints;

        $isPurchased = false;
        if (auth()->check()) {
            $isPurchased = $drivingRoute->isPurchasedBy(auth()->user());
        }

        return view('driving-routes.details', [
            'route' => $drivingRoute,
            'waypointsList' => $waypointsList,
            'isPurchased' => $isPurchased,
        ]);
    }

    private function citySchemaReady(): bool
    {
        return Schema::hasTable('cities') && Schema::hasColumn('driving_routes', 'city_id');
    }
}
