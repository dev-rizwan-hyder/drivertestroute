@extends('layouts.app')

@section('title', $route->title)

@push('styles')
    <!-- Leaflet CSS for guaranteed 100% interactive route map without API key errors -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        .route-detail-page {
            background-color: #f8f9fa;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .09), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .07), transparent 30%),
                linear-gradient(180deg, rgba(248, 249, 250, .9), rgba(241, 243, 245, .94) 48%, rgba(248, 249, 250, .96));
            color: #212529;
        }

        .route-card-glass {
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(16px);
        }

        .btn-google-maps {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 50%, #115e59 100%);
            box-shadow: 0 12px 28px -4px rgba(15, 118, 110, 0.35);
        }

        .btn-google-maps:hover {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 50%, #134e4a 100%);
            box-shadow: 0 16px 32px -4px rgba(15, 118, 110, 0.45);
        }

        /* Leaflet Custom Marker Icons */
        .custom-map-pin {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            color: #ffffff;
            font-weight: 900;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.35);
            border: 2.5px solid #ffffff;
        }
        .pin-start { background: linear-gradient(135deg, #10b981, #059669); }
        .pin-waypoint { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .pin-end { background: linear-gradient(135deg, #ef4444, #dc2626); }
    </style>
@endpush

@section('content')
    @php
        $routeCity = $route->relationLoaded('cityModel') ? $route->cityModel : null;
        $cityName = $routeCity?->name ?? $route->city;
        $cityAddress = $routeCity?->address;
        $googleMapsUrl = $route->google_maps_url;

        $mappedPoints = $points->map(function($p, $idx) {
            return [
                'id' => $p->id ?? ($idx + 1),
                'sort_order' => $p->sort_order ?? ($idx + 1),
                'lat' => $p->lat !== null ? (float) $p->lat : null,
                'lng' => $p->lng !== null ? (float) $p->lng : null,
                'instruction' => $p->instruction ?: 'Turn / Maneuver',
                'maneuver' => $p->maneuver ?: 'continue',
                'distance_km' => $p->distance_km !== null ? (float) $p->distance_km : null,
            ];
        });
    @endphp

    <div class="route-detail-page min-h-screen py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-500" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-teal-700">Home</a>
                <span>/</span>
                <a href="{{ route('driving-routes.index') }}" class="transition hover:text-teal-700">Routes</a>
                @if($route->city_id)
                    <span>/</span>
                    <a href="{{ route('driving-routes.index', ['city' => $route->city_id]) }}" class="transition hover:text-teal-700">{{ $cityName }}</a>
                @elseif($cityName)
                    <span>/</span>
                    <span>{{ $cityName }}</span>
                @endif
                <span>/</span>
                <span class="text-slate-900 font-bold">{{ $route->title }}</span>
            </nav>

            <!-- Page Title Header -->
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <a href="{{ route('driving-routes.index', $route->city_id ? ['city' => $route->city_id] : []) }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-800 hover:text-teal-900 mb-3 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to routes
                    </a>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">{{ $route->title }}</h1>
                        <span class="rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-xs font-black uppercase tracking-wider">
                            {{ strtoupper($route->package_type) }} Route
                        </span>
                    </div>
                    <p class="mt-2 text-lg font-bold text-teal-700">{{ $cityName }}, {{ $route->province }}</p>
                    @if($cityAddress)
                        <p class="mt-1 text-sm text-slate-500 max-w-2xl">{{ $cityAddress }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    @if(auth()->user()->is_admin)
                        <span class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-black text-blue-700">Admin Preview</span>
                    @else
                        <span class="rounded-xl border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-black text-teal-800">
                            {{ $remainingStarts }} {{ \Illuminate\Support\Str::plural('start', $remainingStarts) }} left
                        </span>
                    @endif
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-8 lg:grid-cols-3">

                <!-- Left Column (Interactive Map & Points) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Google Maps Launch CTA Hero Card -->
                    <div class="route-card-glass p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider text-teal-700 block mb-1">Practice Navigation</span>
                                <h2 class="text-2xl font-black text-slate-900">Start Navigation in Google Maps</h2>
                                <p class="mt-1 text-sm text-slate-600">Launch voice-guided turn-by-turn navigation directly in your Google Maps app.</p>
                            </div>

                            @if($route->preview_pdf_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($route->preview_pdf_path) }}" target="_blank" class="shrink-0 flex items-center gap-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 shadow-sm transition active:scale-95">
                                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span>Preview PDF</span>
                                </a>
                            @endif
                        </div>

                        <!-- Stats Strip -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6">
                            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 text-center">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Est. Duration</span>
                                <span class="text-xl font-black text-slate-900 mt-0.5 block">{{ $route->route_duration_minutes ?: '15-20' }} mins</span>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 text-center">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Route Distance</span>
                                <span class="text-xl font-black text-slate-900 mt-0.5 block">{{ $route->route_length_km ?: '8.5' }} km</span>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 text-center">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Waypoints</span>
                                <span class="text-xl font-black text-slate-900 mt-0.5 block">{{ count($mappedPoints) }} stops</span>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 text-center">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Starts Left</span>
                                <span class="text-xl font-black text-teal-700 mt-0.5 block">{{ $remainingStarts ?? 'Unlimited' }}</span>
                            </div>
                        </div>

                        <!-- Big Navigation CTA Button -->
                        <div class="pt-2">
                            <a href="{{ $route->google_maps_url }}" id="btn-open-google-maps" target="_blank" rel="noopener noreferrer" class="btn-google-maps w-full flex items-center justify-center gap-3 rounded-2xl py-4 sm:py-4.5 px-6 text-lg font-black text-white transition transform active:scale-98">
                                <svg class="h-6 w-6 text-teal-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span id="btn-maps-text">🗺️ Start Route in Google Maps</span>
                            </a>
                        </div>
                    </div>

                    <!-- Interactive Multi-Marker Route Map -->
                    <div class="route-card-glass p-4 overflow-hidden">
                        <div class="flex items-center justify-between pb-3 px-2">
                            <span class="text-xs font-black uppercase tracking-wider text-slate-500">Interactive Test Route Map</span>
                            <span class="text-xs font-bold text-teal-700">📍 All {{ count($mappedPoints) }} Waypoints Marked</span>
                        </div>
                        <div id="interactive-map" class="h-[480px] w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 z-0"></div>
                    </div>

                    <!-- Stops Breakdown Card -->
                    @if(count($mappedPoints) > 0)
                        <div class="route-card-glass p-6 sm:p-8">
                            <h3 class="text-xl font-black text-slate-900 mb-4 pb-3 border-b border-slate-100">Test Waypoints & Maneuvers</h3>
                            <div class="space-y-3">
                                @foreach($mappedPoints as $idx => $pt)
                                    <div class="flex items-start gap-4 rounded-2xl bg-white p-4 border border-slate-200/80 shadow-sm">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white font-black text-xs">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded-md bg-teal-50 border border-teal-200 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-teal-800">
                                                    {{ strtoupper(str_replace('_', ' ', $pt['maneuver'])) }}
                                                </span>
                                            </div>
                                            <p class="text-sm font-bold text-slate-800 mt-1">
                                                {{ $pt['instruction'] }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Right Column (Sidebar Information) -->
                <div class="space-y-6">

                    <!-- Route Details Sidebar Card -->
                    <div class="route-card-glass p-6">
                        <h3 class="text-lg font-black text-slate-900 mb-4 pb-3 border-b border-slate-100">Route Information</h3>
                        
                        <dl class="space-y-3.5 text-sm">
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <dt class="font-bold text-slate-500">Start Location</dt>
                                <dd class="font-black text-slate-900">{{ $route->start_label ?: $cityName }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <dt class="font-bold text-slate-500">Destination</dt>
                                <dd class="font-black text-slate-900">{{ $route->destination_label ?: 'Return to Start' }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <dt class="font-bold text-slate-500">Package</dt>
                                <dd class="font-black text-slate-900 uppercase">{{ strtoupper($route->package_type) }} Package</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <dt class="font-bold text-slate-500">City</dt>
                                <dd class="font-black text-slate-900">{{ $cityName }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <dt class="font-bold text-slate-500">Province</dt>
                                <dd class="font-black text-slate-900">{{ $route->province }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <dt class="font-bold text-slate-500">Starts Included</dt>
                                <dd class="font-black text-slate-900">{{ $route->access_limit }}</dd>
                            </div>
                            <div class="flex justify-between py-2">
                                <dt class="font-bold text-slate-500">Price Paid</dt>
                                <dd class="font-black text-teal-700">${{ number_format($route->price, 2) }}</dd>
                            </div>
                        </dl>

                        @if($route->description)
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                <span class="text-xs font-black uppercase text-slate-400 block mb-1">Route Notes</span>
                                <p class="text-xs font-semibold text-slate-600 leading-relaxed">{{ $route->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Related Routes Card -->
                    @if($relatedRoutes->count() > 0)
                        <div class="route-card-glass p-6">
                            <h3 class="text-lg font-black text-slate-900 mb-4 pb-3 border-b border-slate-100">Other {{ $cityName }} Routes</h3>
                            <div class="space-y-3">
                                @foreach($relatedRoutes as $rel)
                                    <a href="{{ route('driving-routes.show', $rel) }}" class="group block rounded-2xl bg-slate-50 p-4 border border-slate-150 transition hover:bg-teal-50/50 hover:border-teal-200">
                                        <h4 class="font-bold text-sm text-slate-900 group-hover:text-teal-800 transition">{{ $rel->title }}</h4>
                                        <div class="mt-1 flex items-center justify-between text-xs font-semibold text-slate-500">
                                            <span>{{ $rel->route_duration_minutes ?: '15-20' }} mins</span>
                                            <span class="font-bold text-teal-700">${{ number_format($rel->price, 2) }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
<!-- Leaflet JS for guaranteed 100% route rendering and marker pins -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnMaps = document.getElementById('btn-open-google-maps');
        const btnText = document.getElementById('btn-maps-text');
        const pointsData = @json($mappedPoints);

        const routeAccess = {
            isAdmin: @json(auth()->user()->is_admin),
            remainingStarts: @json($remainingStarts),
            mapsUrl: @json($route->google_maps_url),
            startUrl: @json(route('driving-routes.start', $route)),
            csrfToken: @json(csrf_token()),
        };

        // Open in Google Maps Handler
        if (btnMaps) {
            btnMaps.addEventListener('click', async () => {
                if (btnText) btnText.textContent = 'Opening Google Maps...';

                if (!routeAccess.isAdmin) {
                    try {
                        await fetch(routeAccess.startUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': routeAccess.csrfToken,
                            },
                        });
                    } catch (e) {
                        console.warn('Start access check completed:', e);
                    }
                }

                window.open(routeAccess.mapsUrl, '_blank');

                setTimeout(() => {
                    if (btnText) btnText.textContent = '🗺️ Open Navigation in Google Maps App';
                }, 1500);
            });
        }

        // Initialize Leaflet Interactive Route Map with All Markers
        const mapContainer = document.getElementById('interactive-map');
        if (mapContainer && typeof L !== 'undefined') {
            const validPoints = pointsData.filter(p => p.lat !== null && p.lng !== null && !isNaN(p.lat) && !isNaN(p.lng));

            let initialCenter = [43.6532, -79.3832]; // Default Ontario center
            if (validPoints.length > 0) {
                initialCenter = [validPoints[0].lat, validPoints[0].lng];
            } else if (@json($route->start_lat) && @json($route->start_lng)) {
                initialCenter = [@json((float)$route->start_lat), @json((float)$route->start_lng)];
            }

            const map = L.map('interactive-map').setView(initialCenter, 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const bounds = [];

            if (validPoints.length > 0) {
                const latLngs = [];

                validPoints.forEach((pt, index) => {
                    const latLng = [pt.lat, pt.lng];
                    latLngs.push(latLng);
                    bounds.push(latLng);

                    let pinClass = 'pin-waypoint';
                    let label = index + 1;

                    if (index === 0) {
                        pinClass = 'pin-start';
                        label = 'S';
                    } else if (index === validPoints.length - 1) {
                        pinClass = 'pin-end';
                        label = 'D';
                    }

                    const customIcon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div class="custom-map-pin ${pinClass}">${label}</div>`,
                        iconSize: [34, 34],
                        iconAnchor: [17, 17],
                    });

                    L.marker(latLng, { icon: customIcon })
                        .addTo(map)
                        .bindPopup(`<b>Stop ${index + 1}: ${pt.instruction}</b>`);
                });

                // Polyline connecting all points
                L.polyline(latLngs, {
                    color: '#0284c7',
                    weight: 5,
                    opacity: 0.85,
                    lineJoin: 'round'
                }).addTo(map);

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            }
        }
    });
</script>
@endpush
