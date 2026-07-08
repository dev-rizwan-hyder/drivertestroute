@extends('layouts.app')

@section('title', $route->title)

@push('styles')
    <style>
        .route-detail-page {
            background:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .18), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .12), transparent 30%),
                linear-gradient(180deg, #0a0e1a, #0d1117 48%, #0a0e1a);
            color: #f8fafc;
        }

        .route-detail-glass {
            border: 1px solid rgba(59, 130, 246, .22);
            border-radius: .5rem;
            background:
                linear-gradient(180deg, rgba(56, 189, 248, .075), rgba(15, 23, 42, .18)),
                rgba(17, 24, 39, .68);
            box-shadow: 0 22px 58px rgba(2, 6, 23, .34), inset 0 1px 0 rgba(255, 255, 255, .1);
            backdrop-filter: blur(16px);
        }

        .route-detail-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #fff 0%, #bfdbfe 26%, #38bdf8 56%, #cffafe 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .route-detail-button {
            display: inline-flex;
            min-height: 2.75rem;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            padding: .75rem 1rem;
            font-weight: 900;
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), box-shadow 200ms ease-out, border-color 200ms ease-out, background 200ms ease-out;
        }

        .route-detail-button:hover {
            transform: translateY(-1px) scale(1.02);
        }

        .route-detail-button-primary {
            color: #fff;
            background: linear-gradient(135deg, #1e3a8a, #2563eb 52%, #06b6d4);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .24), inset 0 1px 0 rgba(255, 255, 255, .14);
        }

        .route-detail-button-secondary {
            border: 1px solid rgba(59, 130, 246, .34);
            color: #bfdbfe;
            background: rgba(15, 23, 42, .52);
        }

        .route-detail-page #active-instruction {
            border-color: rgba(255, 255, 255, .1) !important;
            background: rgba(10, 14, 26, .94) !important;
        }

        .route-detail-page #active-instruction-title,
        .route-detail-page #directions-list p,
        .route-detail-page #directions-list div {
            color: inherit;
        }
    </style>
@endpush

@section('content')
    @php
        $routeCity = $route->relationLoaded('cityModel') ? $route->cityModel : null;
        $cityName = $routeCity?->name ?? $route->city;
        $cityAddress = $routeCity?->address;
        $startQuery = trim(implode(', ', array_filter([
            $route->start_label,
            $cityName,
            $route->province,
        ])));
        $destinationQuery = trim(implode(', ', array_filter([
            $route->destination_label,
            $cityName,
            $route->province,
        ])));
        $hasStart = ($route->start_lat !== null && $route->start_lng !== null) || $startQuery !== '';
        $hasDestination = ($route->end_lat !== null && $route->end_lng !== null) || $destinationQuery !== '';
        $hasRouteStops = $hasStart && $hasDestination;
    @endphp

    <div class="route-detail-page">
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm font-bold text-slate-400" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-cyan-200">Home</a>
                <span>/</span>
                <a href="{{ route('driving-routes.index') }}" class="transition hover:text-cyan-200">Routes</a>
                @if($route->city_id)
                    <span>/</span>
                    <a href="{{ route('driving-routes.index', ['city' => $route->city_id]) }}" class="transition hover:text-cyan-200">{{ $cityName }}</a>
                @elseif($cityName)
                    <span>/</span>
                    <span>{{ $cityName }}</span>
                @endif
                <span>/</span>
                <span class="text-white">{{ $route->title }}</span>
            </nav>

            <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <a href="{{ route('driving-routes.index', $route->city_id ? ['city' => $route->city_id] : []) }}" class="route-detail-button route-detail-button-secondary">
                        Back to routes
                    </a>
                    <h1 class="mt-5 text-4xl font-black text-white sm:text-5xl">{{ $route->title }}</h1>
                    <p class="route-detail-gradient-text mt-3 text-xl font-black">{{ $cityName }}, {{ $route->province }}</p>
                    @if($cityAddress)
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">{{ $cityAddress }}</p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if(auth()->user()->is_admin)
                        <span class="rounded-md border border-blue-500/20 bg-white/[.06] px-3 py-2 text-sm font-black text-cyan-100">Admin preview</span>
                    @else
                        <span class="rounded-md border border-blue-500/20 bg-white/[.06] px-3 py-2 text-sm font-black text-cyan-100">
                            {{ $remainingStarts }} map {{ \Illuminate\Support\Str::plural('start', $remainingStarts) }} left
                        </span>
                    @endif

                    @if($route->preview_pdf_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($route->preview_pdf_path) }}" target="_blank" class="route-detail-button route-detail-button-secondary">
                            Preview PDF
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_390px]">
                <div class="route-detail-glass overflow-hidden">
                @if(config('services.google.maps_key') && $hasRouteStops)
                    <div id="map" class="h-[68vh] min-h-[460px] w-full"></div>
                    <div id="active-instruction" class="border-t border-white/10 px-5 py-4">
                        <div class="text-xs font-black uppercase text-slate-500">Drive Guidance</div>
                        <div id="active-instruction-title" class="mt-1 text-xl font-black text-white">Route loading...</div>
                        <div id="active-instruction-detail" class="mt-1 text-sm text-slate-400">Google will calculate the best path from start to midpoint and back to start.</div>
                    </div>
                @elseif(! $hasRouteStops)
                    <div class="grid h-[70vh] min-h-[460px] place-items-center bg-[#0a0e1a] p-6 text-center">
                        <div>
                            <h2 class="text-lg font-black text-white">Start and midpoint needed</h2>
                            <p class="mt-2 max-w-md text-sm text-slate-400">Add a start point and midpoint/end point in the admin panel. Google will calculate the return route automatically.</p>
                        </div>
                    </div>
                @else
                    <div class="relative grid h-[70vh] min-h-[460px] place-items-center overflow-hidden bg-[#0a0e1a] p-6 text-center">
                        <svg class="absolute inset-0 h-full w-full opacity-80" viewBox="0 0 720 520" fill="none" aria-hidden="true">
                            <path d="M0 104H720M0 208H720M0 312H720M0 416H720M120 0V520M240 0V520M360 0V520M480 0V520M600 0V520" stroke="rgba(148,163,184,.14)" />
                            <path d="M74 420 C166 240 260 326 350 174 C438 26 544 164 646 86" stroke="url(#routePlaceholder)" stroke-width="10" stroke-linecap="round" />
                            <circle cx="74" cy="420" r="13" fill="#38bdf8" />
                            <circle cx="646" cy="86" r="13" fill="#2563eb" />
                            <defs>
                                <linearGradient id="routePlaceholder" x1="74" x2="646" y1="420" y2="86">
                                    <stop stop-color="#1e3a8a" />
                                    <stop offset=".55" stop-color="#2563eb" />
                                    <stop offset="1" stop-color="#06b6d4" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="relative">
                            <h2 class="text-lg font-black text-white">Google Maps key needed</h2>
                            <p class="mt-2 max-w-md text-sm text-slate-400">Set GOOGLE_MAPS_KEY in the environment to render the live route map.</p>
                        </div>
                    </div>
                @endif
            </div>

                <aside class="route-detail-glass overflow-hidden">
                <div class="border-b border-white/10 p-5">
                    <h2 class="text-lg font-black text-white">Details</h2>
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        @if(auth()->user()->is_admin)
                            <div class="rounded-md bg-white/[.06] p-3">
                                <dt class="font-bold text-slate-500">Mode</dt>
                                <dd class="mt-1 font-black text-white">Admin preview</dd>
                            </div>
                            <div class="rounded-md bg-white/[.06] p-3">
                                <dt class="font-bold text-slate-500">Starts Used</dt>
                                <dd class="mt-1 font-black text-white">Not counted</dd>
                            </div>
                        @else
                            <div class="rounded-md bg-white/[.06] p-3">
                                <dt class="font-bold text-slate-500">Starts Left</dt>
                                <dd id="remaining-starts" class="mt-1 font-black text-white">{{ $remainingStarts }}</dd>
                            </div>
                            <div class="rounded-md bg-white/[.06] p-3">
                                <dt class="font-bold text-slate-500">Starts Used</dt>
                                <dd class="mt-1 font-black text-white">{{ $purchase->access_used }} / {{ $purchase->access_limit }}</dd>
                            </div>
                        @endif
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Schedule</dt>
                            <dd class="mt-1 font-black text-white">{{ $route->route_duration_minutes ? $route->route_duration_minutes.' mins' : 'Ready' }}</dd>
                        </div>
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Pricing</dt>
                            <dd class="mt-1 font-black text-white">${{ number_format((float) $route->price, 2) }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="border-b border-white/10 p-5">
                    <h2 class="text-lg font-black text-white">Stops</h2>
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Start</dt>
                            <dd class="mt-1 font-black text-white">{{ $route->start_label ?: 'Start point' }}</dd>
                        </div>
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Midpoint</dt>
                            <dd class="mt-1 font-black text-white">{{ $route->destination_label ?: 'Midpoint' }}</dd>
                        </div>
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Finish</dt>
                            <dd class="mt-1 font-black text-white">Back to start</dd>
                        </div>
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Length</dt>
                            <dd class="mt-1 font-black text-white">{{ $route->route_length_km ? $route->route_length_km.' km' : 'Route' }}</dd>
                        </div>
                    </dl>
                    @if($route->description)
                        <p class="mt-4 text-sm leading-6 text-slate-400">{{ $route->description }}</p>
                    @endif
                </div>

                <ol id="directions-list" class="max-h-[60vh] divide-y divide-white/10 overflow-y-auto">
                    <li class="p-5 text-sm text-slate-400">Directions will appear after Google calculates the route.</li>
                </ol>
            </aside>
            </div>

            @if($relatedRoutes->isNotEmpty())
                <section class="mt-12">
                    <div class="mb-5 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-sm font-black uppercase text-cyan-200">Related routes</p>
                            <h2 class="mt-2 text-2xl font-black text-white">More in {{ $cityName }}</h2>
                        </div>
                        <a href="{{ route('driving-routes.index', $route->city_id ? ['city' => $route->city_id] : []) }}" class="route-detail-button route-detail-button-secondary">View all</a>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach($relatedRoutes as $relatedRoute)
                            <a href="{{ route('driving-routes.show', $relatedRoute) }}" class="route-detail-glass p-5 transition hover:-translate-y-1 hover:border-cyan-300/40">
                                <h3 class="font-black text-white">{{ $relatedRoute->title }}</h3>
                                @php($relatedCity = $relatedRoute->relationLoaded('cityModel') ? $relatedRoute->cityModel : null)
                                <p class="mt-2 text-sm text-slate-400">{{ $relatedCity?->name ?? $relatedRoute->city }}, {{ $relatedRoute->province }}</p>
                                <p class="mt-4 text-sm font-black text-cyan-100">${{ number_format((float) $relatedRoute->price, 2) }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </section>
    </div>

    @if(config('services.google.maps_key') && $hasRouteStops)
        <script>
            const routeData = {
                start: {
                    lat: @json($route->start_lat === null ? null : (float) $route->start_lat),
                    lng: @json($route->start_lng === null ? null : (float) $route->start_lng),
                    query: @json($startQuery),
                    label: @json($route->start_label ?: 'Start point')
                },
                midpoint: {
                    lat: @json($route->end_lat === null ? null : (float) $route->end_lat),
                    lng: @json($route->end_lng === null ? null : (float) $route->end_lng),
                    query: @json($destinationQuery),
                    label: @json($route->destination_label ?: 'Midpoint')
                }
            };

            const routeAccess = {
                isAdmin: @json(auth()->user()->is_admin),
                remainingStarts: @json($remainingStarts),
                startUrl: @json(route('driving-routes.start', $route)),
                csrfToken: @json(csrf_token()),
            };

            let map;
            let directionsRenderer;
            let routeStartPosition = null;
            let routeMidpointPosition = null;
            let vehicleMarker = null;
            let currentLocationMarker = null;
            let currentAccuracyCircle = null;
            let watchId = null;
            let driveStarted = false;
            let hasReachedStart = false;
            let lastVehiclePosition = null;
            let lastVehicleHeading = 0;
            let latestCurrentPosition = null;
            let resolvedStartLocation = null;
            let resolvedMidpointLocation = null;
            let directionSteps = [];
            let currentStepIndex = 0;
            let startRouteButton = null;
            let locateButton = null;
            let routeStatus = null;
            let accessConsumedForCurrentDrive = false;
            const startDistanceThresholdMeters = 60;

            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: initialCenter(),
                    zoom: 14,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                    gestureHandling: 'greedy',
                    styles: [
                        { featureType: 'poi.business', stylers: [{ visibility: 'off' }] },
                        { featureType: 'transit', stylers: [{ visibility: 'off' }] },
                    ],
                });

                addRouteControls();
                resolveStopsAndCalculateRoute();
            }

            function initialCenter() {
                if (hasCoordinates(routeData.start)) {
                    return toPosition(routeData.start);
                }

                if (hasCoordinates(routeData.midpoint)) {
                    return toPosition(routeData.midpoint);
                }

                return { lat: 24.8916, lng: 67.1546 };
            }

            async function resolveStopsAndCalculateRoute() {
                setActiveInstruction('Finding route stops...', 'Google is locating the start and midpoint.');

                try {
                    resolvedStartLocation = await resolveStop(routeData.start);
                    resolvedMidpointLocation = await resolveStop(routeData.midpoint);
                    calculateRoundTrip();
                } catch (status) {
                    showRouteError(status, 'Google could not find one of these route stops. Use a real place name like "Star Gate Karachi" or add latitude/longitude in admin.');
                }
            }

            function calculateRoundTrip() {
                setActiveInstruction('Calculating route...', 'Google is choosing the best driving path.');

                const directionsService = new google.maps.DirectionsService();

                Promise.all([
                    requestDirections(directionsService, resolvedStartLocation, resolvedMidpointLocation),
                    requestDirections(directionsService, resolvedMidpointLocation, resolvedStartLocation),
                ])
                    .then(renderDirections)
                    .catch((status) => showRouteError(status, 'Google could not calculate a driving route for these stops.'));
            }

            function requestDirections(directionsService, origin, destination) {
                return new Promise((resolve, reject) => {
                    directionsService.route({
                        origin,
                        destination,
                        optimizeWaypoints: false,
                        provideRouteAlternatives: false,
                        travelMode: google.maps.TravelMode.DRIVING,
                    }, (result, status) => {
                        if (status === 'OK' && result) {
                            resolve(result);
                            return;
                        }

                        reject(status);
                    });
                });
            }

            function resolveStop(stop) {
                if (hasCoordinates(stop)) {
                    return Promise.resolve(toPosition(stop));
                }

                return new Promise((resolve, reject) => {
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        address: stop.query || stop.label,
                        componentRestrictions: routeData.start.query.includes('Karachi') || routeData.midpoint.query.includes('Karachi')
                            ? { country: 'PK' }
                            : undefined,
                    }, (results, status) => {
                        if (status === 'OK' && results?.[0]) {
                            resolve(results[0].geometry.location);
                            return;
                        }

                        reject(status);
                    });
                });
            }

            function showRouteError(status, message) {
                const help = {
                    REQUEST_DENIED: 'Google rejected the request. Enable Maps JavaScript API, Directions API, Geocoding API, and billing for this key.',
                    ZERO_RESULTS: 'Google found no drivable route between these stops. Use clearer places or coordinates.',
                    NOT_FOUND: 'Google could not find the start or midpoint place name.',
                    OVER_QUERY_LIMIT: 'Google quota is exceeded for this API key.',
                }[status] ?? message;

                setActiveInstruction('Route not available', `${help} Status: ${status}`);
                document.getElementById('directions-list').innerHTML = `<li class="p-5 text-sm text-red-700">${help}<br><span class="mt-2 block text-xs text-red-500">Google status: ${status}</span></li>`;
                routeStatus.textContent = `Route failed: ${status}`;
            }

            function renderDirections(results) {
                const bounds = new google.maps.LatLngBounds();

                results.forEach((result) => {
                    directionsRenderer = new google.maps.DirectionsRenderer({
                        map,
                        suppressMarkers: true,
                        preserveViewport: true,
                        polylineOptions: {
                            strokeColor: '#047857',
                            strokeOpacity: 0.95,
                            strokeWeight: 7,
                        },
                    });
                    directionsRenderer.setDirections(result);

                    result.routes[0].overview_path.forEach((point) => bounds.extend(point));
                });

                const outboundRoute = results[0].routes[0];
                routeStartPosition = latLngToPosition(outboundRoute.legs[0].start_location);
                routeMidpointPosition = latLngToPosition(outboundRoute.legs[0].end_location);

                new google.maps.Marker({
                    position: routeStartPosition,
                    map,
                    title: routeData.start.label,
                    icon: endpointIcon('#047857', 'S'),
                });

                new google.maps.Marker({
                    position: routeMidpointPosition,
                    map,
                    title: routeData.midpoint.label,
                    icon: endpointIcon('#dc2626', 'M'),
                });

                directionSteps = results.flatMap((result, routeIndex) => flattenDirectionSteps(result.routes[0], routeIndex));
                renderDirectionList(directionSteps);
                initializeVehicle();
                map.fitBounds(bounds, 72);
                setActiveInstruction('Route ready', 'Use location, go to the start point, then start the drive.');
            }

            function flattenDirectionSteps(googleRoute, routeIndex) {
                return googleRoute.legs.flatMap((leg) => {
                    return leg.steps.map((step) => ({
                        legIndex: routeIndex,
                        html: step.instructions,
                        text: stripHtml(step.instructions),
                        distanceText: step.distance?.text ?? '',
                        distanceMeters: step.distance?.value ?? 0,
                        durationText: step.duration?.text ?? '',
                        start: latLngToPosition(step.start_location),
                        end: latLngToPosition(step.end_location),
                    }));
                });
            }

            function renderDirectionList(steps) {
                const list = document.getElementById('directions-list');

                if (steps.length === 0) {
                    list.innerHTML = '<li class="p-5 text-sm text-stone-600">No directions were returned for this route.</li>';
                    return;
                }

                list.innerHTML = steps.map((step, index) => `
                    <li class="p-5 ${index === currentStepIndex ? 'bg-emerald-50' : ''}" data-step-index="${index}">
                        <div class="flex gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-emerald-700 text-sm font-bold text-white">${index + 1}</div>
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-normal text-stone-500">${step.legIndex === 0 ? 'To midpoint' : 'Back to start'}</div>
                                <p class="mt-1 font-semibold text-stone-950">${step.html}</p>
                                <p class="mt-2 text-sm text-stone-500">${step.distanceText}${step.durationText ? ' / ' + step.durationText : ''}</p>
                            </div>
                        </div>
                    </li>
                `).join('');
            }

            function highlightDirectionStep(index) {
                document.querySelectorAll('[data-step-index]').forEach((item) => {
                    item.classList.toggle('bg-emerald-50', Number(item.dataset.stepIndex) === index);
                });
            }

            function addRouteControls() {
                const wrapper = document.createElement('div');
                wrapper.className = 'm-3 rounded-lg bg-white p-3 shadow-lg ring-1 ring-stone-200';

                startRouteButton = document.createElement('button');
                startRouteButton.type = 'button';
                startRouteButton.disabled = true;
                startRouteButton.className = 'rounded-md bg-stone-300 px-4 py-2 text-sm font-semibold text-stone-600';
                startRouteButton.textContent = 'Start drive';

                locateButton = document.createElement('button');
                locateButton.type = 'button';
                locateButton.className = 'ml-2 rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100';
                locateButton.textContent = 'Use location';

                routeStatus = document.createElement('div');
                routeStatus.className = 'mt-2 max-w-64 text-xs font-medium text-stone-600';
                routeStatus.textContent = routeAccess.isAdmin
                    ? 'Route is loading for admin preview.'
                    : `Route is loading. ${routeAccess.remainingStarts} map starts available.`;

                startRouteButton.addEventListener('click', startLiveRoute);
                locateButton.addEventListener('click', beginLocationWatch);

                wrapper.appendChild(startRouteButton);
                wrapper.appendChild(locateButton);
                wrapper.appendChild(routeStatus);
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(wrapper);
            }

            function initializeVehicle() {
                if (!routeStartPosition) {
                    return;
                }

                const heading = directionSteps.length > 0 ? bearing(directionSteps[0].start, directionSteps[0].end) ?? 0 : 0;
                vehicleMarker = new google.maps.Marker({
                    position: routeStartPosition,
                    map,
                    title: 'Vehicle',
                    icon: vehicleIcon(heading),
                    zIndex: 1000,
                });

                lastVehiclePosition = routeStartPosition;
                lastVehicleHeading = heading;
            }

            async function startLiveRoute() {
                if (!hasReachedStart) {
                    beginLocationWatch();
                    routeStatus.textContent = 'Go to the start point first. The drive button activates when you arrive.';
                    return;
                }

                if (!accessConsumedForCurrentDrive) {
                    startRouteButton.disabled = true;
                    startRouteButton.textContent = 'Starting...';
                    startRouteButton.className = 'rounded-md bg-stone-900 px-4 py-2 text-sm font-semibold text-white';

                    const accessGranted = await consumeMapStart();

                    if (!accessGranted) {
                        return;
                    }
                }

                driveStarted = true;
                startRouteButton.disabled = true;
                startRouteButton.textContent = 'Driving...';
                startRouteButton.className = 'rounded-md bg-stone-900 px-4 py-2 text-sm font-semibold text-white';
                removeCurrentLocationPreview();

                if (latestCurrentPosition) {
                    moveVehicle(latestCurrentPosition);
                    map.panTo(latestCurrentPosition);
                    map.setZoom(Math.max(map.getZoom(), 16));
                }

                updateActiveDrivingInstruction(latestCurrentPosition ?? routeStartPosition);
            }

            async function consumeMapStart() {
                if (routeAccess.isAdmin) {
                    accessConsumedForCurrentDrive = true;
                    return true;
                }

                try {
                    const response = await fetch(routeAccess.startUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': routeAccess.csrfToken,
                        },
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const message = data.message || 'No map starts remaining. Buy this route again to continue.';
                        routeStatus.textContent = message;
                        setActiveInstruction('Map start unavailable', message);
                        startRouteButton.disabled = true;
                        startRouteButton.textContent = 'No starts left';
                        startRouteButton.className = 'rounded-md bg-stone-300 px-4 py-2 text-sm font-semibold text-stone-600';
                        return false;
                    }

                    routeAccess.remainingStarts = data.remaining_starts;
                    accessConsumedForCurrentDrive = true;

                    const remainingStartsElement = document.getElementById('remaining-starts');
                    if (remainingStartsElement && data.remaining_starts !== null) {
                        remainingStartsElement.textContent = data.remaining_starts;
                    }

                    return true;
                } catch (error) {
                    routeStatus.textContent = 'Could not verify paid access. Please try again.';
                    setActiveInstruction('Access check failed', 'Your map start was not used. Check your connection and try again.');
                    startRouteButton.disabled = false;
                    startRouteButton.textContent = 'Start drive';
                    startRouteButton.className = 'rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800';
                    return false;
                }
            }

            function beginLocationWatch() {
                if (watchId !== null) {
                    routeStatus.textContent = driveStarted
                        ? 'Drive started. Live tracking is active.'
                        : hasReachedStart
                            ? 'You are on start point of route. Start the drive.'
                            : 'Live location is already active.';
                    return;
                }

                if (!navigator.geolocation) {
                    routeStatus.textContent = 'Location is not supported by this browser.';
                    return;
                }

                locateButton.disabled = true;
                locateButton.textContent = 'Locating...';
                locateButton.className = 'ml-2 rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-500';
                routeStatus.textContent = 'Allow location permission to show your current location.';

                watchId = navigator.geolocation.watchPosition(
                    handleLocationUpdate,
                    (error) => {
                        locateButton.disabled = false;
                        locateButton.textContent = 'Use location';
                        locateButton.className = 'ml-2 rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100';
                        routeStatus.textContent = error.code === error.PERMISSION_DENIED
                            ? 'Location permission was denied.'
                            : 'Could not get your live location.';
                        watchId = null;
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                        timeout: 10000,
                    },
                );
            }

            function handleLocationUpdate(position) {
                const currentPosition = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                latestCurrentPosition = currentPosition;

                if (!driveStarted) {
                    updateCurrentLocationMarker(currentPosition, position.coords.accuracy);
                    updateStartProximity(currentPosition, position.coords.accuracy);
                    return;
                }

                moveVehicle(currentPosition, position.coords.heading);
                updateActiveDrivingInstruction(currentPosition);
                map.panTo(currentPosition);

                if (map.getZoom() < 16) {
                    map.setZoom(16);
                }
            }

            function updateCurrentLocationMarker(position, accuracy) {
                if (!currentLocationMarker) {
                    currentLocationMarker = new google.maps.Marker({
                        position,
                        map,
                        title: 'Your current location',
                        icon: currentLocationIcon(),
                        zIndex: 999,
                    });
                } else {
                    currentLocationMarker.setPosition(position);
                }

                if (!currentAccuracyCircle) {
                    currentAccuracyCircle = new google.maps.Circle({
                        map,
                        center: position,
                        radius: accuracy,
                        strokeColor: '#2563eb',
                        strokeOpacity: 0.18,
                        strokeWeight: 1,
                        fillColor: '#2563eb',
                        fillOpacity: 0.08,
                    });
                    return;
                }

                currentAccuracyCircle.setCenter(position);
                currentAccuracyCircle.setRadius(accuracy);
            }

            function updateStartProximity(position, accuracy) {
                if (!routeStartPosition) {
                    return;
                }

                const distance = distanceMeters(position, routeStartPosition);
                hasReachedStart = distance <= startDistanceThresholdMeters;
                locateButton.disabled = false;
                locateButton.textContent = 'Location on';
                locateButton.className = 'ml-2 rounded-md border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-800';

                if (hasReachedStart) {
                    enableStartDrive();
                    routeStatus.textContent = 'You are on start point of route. Start the drive.';
                    setActiveInstruction('You are on the start point', 'Start the drive when ready.');
                    return;
                }

                disableStartDrive();
                const accuracyText = Number.isFinite(accuracy) ? ` Accuracy ${Math.round(accuracy)} m.` : '';
                routeStatus.textContent = `${formatDistance(distance)} from start point.${accuracyText}`;
                setActiveInstruction('Go to start point', `${formatDistance(distance)} away from route start.`);
            }

            function updateActiveDrivingInstruction(position) {
                if (!position || directionSteps.length === 0) {
                    return;
                }

                while (currentStepIndex < directionSteps.length - 1 && distanceMeters(position, directionSteps[currentStepIndex].end) < 35) {
                    currentStepIndex += 1;
                }

                const step = directionSteps[currentStepIndex];
                const nextStep = directionSteps[currentStepIndex + 1] ?? null;
                const distance = distanceMeters(position, step.end);

                if (currentStepIndex === directionSteps.length - 1 && distance < 35) {
                    setActiveInstruction('Route complete', 'You are back at the start point.');
                    routeStatus.textContent = 'Route complete.';
                    highlightDirectionStep(currentStepIndex);
                    return;
                }

                const guidance = nextInstructionText(step, nextStep, distance);
                setActiveInstruction(guidance, `${step.distanceText}${step.durationText ? ' / ' + step.durationText : ''}`);
                highlightDirectionStep(currentStepIndex);
                routeStatus.textContent = 'Drive started. Live tracking is active.';
            }

            function nextInstructionText(step, nextStep, distance) {
                const distanceText = formatDistance(distance);

                if (nextStep && distance > 35) {
                    return `${nextStep.text || 'Continue'} after ${distanceText}`;
                }

                if (nextStep) {
                    return nextStep.text || 'Continue';
                }

                const text = step.text || 'Continue';

                if (/^(head|continue|keep|merge|go straight|drive)/i.test(text)) {
                    return `Drive straight for ${distanceText}`;
                }

                return `${text} after ${distanceText}`;
            }

            function moveVehicle(position, reportedHeading = null) {
                const heading = Number.isFinite(reportedHeading)
                    ? reportedHeading
                    : bearing(lastVehiclePosition, position) ?? lastVehicleHeading;

                animateVehicle(position, heading);
            }

            function enableStartDrive() {
                startRouteButton.disabled = false;
                startRouteButton.className = 'rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800';
            }

            function disableStartDrive() {
                startRouteButton.disabled = true;
                startRouteButton.className = 'rounded-md bg-stone-300 px-4 py-2 text-sm font-semibold text-stone-600';
            }

            function removeCurrentLocationPreview() {
                if (currentLocationMarker) {
                    currentLocationMarker.setMap(null);
                    currentLocationMarker = null;
                }

                if (currentAccuracyCircle) {
                    currentAccuracyCircle.setMap(null);
                    currentAccuracyCircle = null;
                }
            }

            function hasCoordinates(position) {
                return Number.isFinite(position.lat) && Number.isFinite(position.lng);
            }

            function toPosition(position) {
                return {
                    lat: Number(position.lat),
                    lng: Number(position.lng),
                };
            }

            function latLngToPosition(latLng) {
                return {
                    lat: latLng.lat(),
                    lng: latLng.lng(),
                };
            }

            function setActiveInstruction(title, detail) {
                document.getElementById('active-instruction-title').textContent = title;
                document.getElementById('active-instruction-detail').textContent = detail;
            }

            function stripHtml(html) {
                const element = document.createElement('div');
                element.innerHTML = html;
                return element.textContent || element.innerText || '';
            }

            function endpointIcon(color, label) {
                const svg = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="56" viewBox="0 0 44 56">
                        <path fill="${color}" d="M22 0C9.9 0 0 9.8 0 21.9C0 38.5 22 56 22 56S44 38.5 44 21.9C44 9.8 34.1 0 22 0Z"/>
                        <circle cx="22" cy="22" r="13" fill="white"/>
                        <text x="22" y="27" text-anchor="middle" font-size="15" font-family="Arial, sans-serif" font-weight="700" fill="${color}">${label}</text>
                    </svg>
                `;

                return {
                    url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`,
                    scaledSize: new google.maps.Size(36, 46),
                    anchor: new google.maps.Point(18, 46),
                };
            }

            function currentLocationIcon() {
                const svg = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15" fill="#2563eb" fill-opacity="0.18"/>
                        <circle cx="18" cy="18" r="8" fill="#2563eb" stroke="white" stroke-width="4"/>
                    </svg>
                `;

                return {
                    url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`,
                    scaledSize: new google.maps.Size(36, 36),
                    anchor: new google.maps.Point(18, 18),
                };
            }

            function vehicleIcon(rotation = 0) {
                return {
                    path: 'M 0 -18 L 9 11 L 0 6 L -9 11 Z',
                    fillColor: '#111827',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2.5,
                    scale: 1.25,
                    rotation,
                    anchor: new google.maps.Point(0, 0),
                };
            }

            function animateVehicle(nextPosition, heading) {
                if (!vehicleMarker) {
                    return;
                }

                const start = lastVehiclePosition;

                if (!start) {
                    vehicleMarker.setPosition(nextPosition);
                    vehicleMarker.setIcon(vehicleIcon(heading));
                    lastVehiclePosition = nextPosition;
                    lastVehicleHeading = heading;
                    return;
                }

                const startedAt = performance.now();
                const duration = 700;

                function step(now) {
                    const progress = Math.min((now - startedAt) / duration, 1);
                    const position = {
                        lat: start.lat + ((nextPosition.lat - start.lat) * progress),
                        lng: start.lng + ((nextPosition.lng - start.lng) * progress),
                    };

                    vehicleMarker.setPosition(position);
                    vehicleMarker.setIcon(vehicleIcon(heading));

                    if (progress < 1) {
                        requestAnimationFrame(step);
                        return;
                    }

                    lastVehiclePosition = nextPosition;
                    lastVehicleHeading = heading;
                }

                requestAnimationFrame(step);
            }

            function bearing(from, to) {
                if (!from || !to) {
                    return null;
                }

                const fromLat = degreesToRadians(from.lat);
                const toLat = degreesToRadians(to.lat);
                const deltaLng = degreesToRadians(to.lng - from.lng);
                const y = Math.sin(deltaLng) * Math.cos(toLat);
                const x = Math.cos(fromLat) * Math.sin(toLat)
                    - Math.sin(fromLat) * Math.cos(toLat) * Math.cos(deltaLng);

                return (radiansToDegrees(Math.atan2(y, x)) + 360) % 360;
            }

            function distanceMeters(from, to) {
                const earthRadiusMeters = 6371000;
                const fromLat = degreesToRadians(from.lat);
                const toLat = degreesToRadians(to.lat);
                const deltaLat = degreesToRadians(to.lat - from.lat);
                const deltaLng = degreesToRadians(to.lng - from.lng);
                const a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2)
                    + Math.cos(fromLat) * Math.cos(toLat)
                    * Math.sin(deltaLng / 2) * Math.sin(deltaLng / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                return earthRadiusMeters * c;
            }

            function formatDistance(distance) {
                if (distance < 1000) {
                    return `${Math.max(0, Math.round(distance))} m`;
                }

                return `${(distance / 1000).toFixed(1)} km`;
            }

            function degreesToRadians(degrees) {
                return degrees * Math.PI / 180;
            }

            function radiansToDegrees(radians) {
                return radians * 180 / Math.PI;
            }
        </script>

        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&loading=async&callback=initMap"></script>
    @endif
@endsection
