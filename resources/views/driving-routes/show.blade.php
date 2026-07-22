@extends('layouts.app')

@section('title', $route->title)

@push('styles')
    <!-- Leaflet CSS for fallback map canvas -->
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

        /* Fullscreen Navigation Wrapper */
        #map-wrapper:fullscreen,
        #map-wrapper.is-fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 99999 !important;
            border-radius: 0 !important;
            margin: 0 !important;
        }

        /* Top Google Maps Navigation Instruction HUD */
        .nav-hud-top {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 50%, #134e4a 100%);
            box-shadow: 0 12px 32px rgba(15, 118, 110, 0.35);
        }

        /* Custom Pins & Floating Navigation Arrow */
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

        .nav-arrow-icon {
            transition: transform 250ms cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
@endpush

@section('content')
    @php
        $routeCity = $route->relationLoaded('cityModel') ? $route->cityModel : null;
        $cityName = $routeCity?->name ?? $route->city;
        $cityAddress = $routeCity?->address;
        $mappedPoints = collect($route->parsed_waypoints);
        $mapsKey = config('services.google.maps_key');
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

                <!-- Left Column (Google Maps Style Real-Time Navigation Engine) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Google Maps Real-Time Interactive Navigation Box -->
                    <div id="map-wrapper" class="route-card-glass relative overflow-hidden p-2 transition-all">
                        
                        <!-- Top Upward Navigation Instruction Banner -->
                        <div id="nav-instruction-banner" class="nav-hud-top absolute top-4 left-4 right-4 z-20 rounded-2xl p-4 sm:p-5 text-white shadow-2xl transition-all">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <!-- Maneuver Icon Container -->
                                    <div id="nav-maneuver-icon-container" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md">
                                        <svg id="nav-maneuver-icon" class="h-8 w-8 text-teal-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div id="nav-step-distance" class="text-xs font-black uppercase tracking-wider text-teal-200">Head to Start Point</div>
                                        <h3 id="nav-step-title" class="text-lg sm:text-xl font-black leading-snug">
                                            📍 Head toward {{ $route->start_label ?: 'Start Point' }}
                                        </h3>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <!-- Voice Guidance Toggle -->
                                    <button type="button" id="btn-toggle-voice" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 hover:bg-white/25 text-white transition" title="Voice Guidance On/Off">
                                        <span id="voice-icon" class="text-lg">🔊</span>
                                    </button>

                                    <!-- Fullscreen Toggle -->
                                    <button type="button" id="btn-toggle-fullscreen" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 hover:bg-white/25 text-white transition" title="Toggle Fullscreen Preview">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Map Element -->
                        <div id="navigation-map" class="h-[540px] w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 z-10"></div>

                        <!-- Google Maps Floating Action Button (Start / Recenter) -->
                        <button type="button" id="btn-floating-start" class="absolute bottom-20 right-6 z-30 flex items-center gap-3 rounded-full bg-gradient-to-r from-blue-600 to-teal-600 hover:from-blue-700 hover:to-teal-700 text-white font-black px-6 py-4 shadow-2xl transition transform hover:scale-105 active:scale-95 border-2 border-white">
                            <svg class="h-6 w-6 text-white transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span id="floating-btn-label">Start Navigation</span>
                        </button>

                        <!-- Bottom Controls HUD Bar -->
                        <div class="mt-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 p-4 rounded-2xl bg-white/95 border border-slate-200/80 shadow-md z-20">
                            
                            <!-- Drive Speed & ETA -->
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white font-black text-sm">
                                    <span id="nav-speed">0</span>
                                    <span class="text-[9px] font-normal block ml-0.5">km/h</span>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-500">
                                        Remaining: <span id="nav-rem-dist" class="text-slate-900 font-black">{{ $route->route_length_km ?: '8.5' }} km</span>
                                    </div>
                                    <div class="text-sm font-black text-teal-800">
                                        Est. Duration: <span id="nav-rem-time">{{ $route->route_duration_minutes ?: '15-20' }} mins</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation Controls -->
                            <div class="flex items-center gap-2">
                                <button type="button" id="btn-recenter" class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition">
                                    🎯 Recenter Map
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Stops Breakdown Card -->
                    @if(count($mappedPoints) > 0)
                        <div class="route-card-glass p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                                <h3 class="text-xl font-black text-slate-900">Test Waypoints & Maneuvers</h3>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">
                                    {{ count($mappedPoints) }} Total Stops
                                </span>
                            </div>
                            <div class="space-y-3">
                                @foreach($mappedPoints as $idx => $pt)
                                    <div class="flex items-start gap-4 rounded-2xl bg-white p-4 border border-slate-200/80 shadow-sm">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white font-black text-xs">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded-md bg-teal-50 border border-teal-200 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-teal-800">
                                                    {{ strtoupper(str_replace('_', ' ', $pt['maneuver'] ?? 'continue')) }}
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

                        @if($route->preview_pdf_path)
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($route->preview_pdf_path) }}" target="_blank" class="w-full flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 py-3 text-sm font-bold text-slate-700 shadow-sm transition">
                                    📄 Preview Route PDF
                                </a>
                            </div>
                        @endif

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
@if($mapsKey)
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $mapsKey }}&libraries=places,geometry"></script>
@endif
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pointsData = @json($mappedPoints);
        const routeAccess = {
            isAdmin: @json(auth()->user()->is_admin),
            remainingStarts: @json($remainingStarts),
            startUrl: @json(route('driving-routes.start', $route)),
            csrfToken: @json(csrf_token()),
        };

        let isDriving = false;
        let isAtStartPoint = false;
        let voiceEnabled = true;
        let watchId = null;
        let simulationInterval = null;
        let currentHeading = 0;

        const btnFloatingStart = document.getElementById('btn-floating-start');
        const floatingBtnLabel = document.getElementById('floating-btn-label');
        const btnRecenter = document.getElementById('btn-recenter');
        const btnVoice = document.getElementById('btn-toggle-voice');
        const voiceIcon = document.getElementById('voice-icon');
        const btnFullscreen = document.getElementById('btn-toggle-fullscreen');
        const mapWrapper = document.getElementById('map-wrapper');
        const stepTitle = document.getElementById('nav-step-title');
        const stepDistance = document.getElementById('nav-step-distance');

        const validPoints = pointsData.filter(p => p.lat !== null && p.lng !== null && !isNaN(p.lat) && !isNaN(p.lng));
        const startPoint = validPoints.length > 0 ? validPoints[0] : null;

        function speakInstruction(text) {
            if (!voiceEnabled || !('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            window.speechSynthesis.speak(utterance);
        }

        if (btnVoice) {
            btnVoice.addEventListener('click', () => {
                voiceEnabled = !voiceEnabled;
                voiceIcon.textContent = voiceEnabled ? '🔊' : '%EF%BF%BD';
            });
        }

        if (btnFullscreen && mapWrapper) {
            btnFullscreen.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    if (mapWrapper.requestFullscreen) mapWrapper.requestFullscreen();
                    else mapWrapper.classList.toggle('is-fullscreen');
                } else {
                    if (document.exitFullscreen) document.exitFullscreen();
                    mapWrapper.classList.remove('is-fullscreen');
                }
            });
        }

        // Initialize Google Maps / Leaflet Engine
        let map, userArrowMarker, routePolyline, approachPolyline;

        function initNavigationEngine() {
            const mapContainer = document.getElementById('navigation-map');
            let center = { lat: 43.6532, lng: -79.3832 };

            if (startPoint) {
                center = { lat: startPoint.lat, lng: startPoint.lng };
            }

            if (typeof google !== 'undefined' && google.maps) {
                // Google Maps JS API Engine
                map = new google.maps.Map(mapContainer, {
                    center: center,
                    zoom: 16,
                    mapTypeId: 'roadmap',
                    disableDefaultUI: false,
                    heading: 0,
                    tilt: 45,
                });

                const latLngs = validPoints.map(p => ({ lat: p.lat, lng: p.lng }));
                if (latLngs.length > 0) {
                    routePolyline = new google.maps.Polyline({
                        path: latLngs,
                        geodesic: true,
                        strokeColor: '#0284c7',
                        strokeOpacity: 0.9,
                        strokeWeight: 6,
                        map: map,
                    });

                    validPoints.forEach((pt, idx) => {
                        new google.maps.Marker({
                            position: { lat: pt.lat, lng: pt.lng },
                            map: map,
                            label: {
                                text: idx === 0 ? 'S' : (idx === validPoints.length - 1 ? 'D' : `${idx + 1}`),
                                color: '#ffffff',
                                fontWeight: 'bold'
                            },
                        });
                    });
                }
            } else if (typeof L !== 'undefined') {
                // Leaflet Fallback Engine
                map = L.map('navigation-map').setView([center.lat, center.lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                if (validPoints.length > 0) {
                    const coords = validPoints.map(p => [p.lat, p.lng]);
                    L.polyline(coords, { color: '#0284c7', weight: 6 }).addTo(map);

                    validPoints.forEach((pt, idx) => {
                        const customIcon = L.divIcon({
                            className: 'custom-div-icon',
                            html: `<div class="custom-map-pin ${idx === 0 ? 'pin-start' : (idx === validPoints.length - 1 ? 'pin-end' : 'pin-waypoint')}">${idx === 0 ? 'S' : (idx === validPoints.length - 1 ? 'D' : idx + 1)}</div>`,
                            iconSize: [34, 34],
                            iconAnchor: [17, 17]
                        });
                        L.marker([pt.lat, pt.lng], { icon: customIcon }).addTo(map);
                    });
                }
            }
        }

        initNavigationEngine();

        // Calculate Haversine Distance in meters
        function calculateDistanceMeters(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const phi1 = lat1 * Math.PI / 180;
            const phi2 = lat2 * Math.PI / 180;
            const deltaPhi = (lat2 - lat1) * Math.PI / 180;
            const deltaLambda = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
                      Math.cos(phi1) * Math.cos(phi2) *
                      Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        }

        // Floating Action Button Click (Start Navigation / Recenter)
        if (btnFloatingStart) {
            btnFloatingStart.addEventListener('click', async () => {
                if (!isDriving) {
                    // Consume access limit
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
                            console.warn('Start access recorded:', e);
                        }
                    }

                    isDriving = true;
                    if (floatingBtnLabel) floatingBtnLabel.textContent = 'Re-center';

                    // Start GPS / Location Tracking
                    startRealTimeLocationNavigation();
                } else {
                    recenterMap();
                }
            });
        }

        if (btnRecenter) {
            btnRecenter.addEventListener('click', () => recenterMap());
        }

        function recenterMap() {
            if (startPoint && map) {
                if (typeof google !== 'undefined' && google.maps && map instanceof google.maps.Map) {
                    map.panTo({ lat: startPoint.lat, lng: startPoint.lng });
                    map.setZoom(17);
                } else if (typeof L !== 'undefined' && map.panTo) {
                    map.panTo([startPoint.lat, startPoint.lng]);
                }
            }
        }

        function startRealTimeLocationNavigation() {
            if ('geolocation' in navigator) {
                watchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        const userLat = pos.coords.latitude;
                        const userLng = pos.coords.longitude;
                        const speed = pos.coords.speed ? Math.round(pos.coords.speed * 3.6) : 0;
                        const heading = pos.coords.heading || 0;

                        handleUserLocationUpdate(userLat, userLng, heading, speed);
                    },
                    (err) => {
                        console.warn('GPS location unavailable, launching simulation mode:', err);
                        startSimulatedDrive();
                    },
                    { enableHighAccuracy: true, maximumAge: 1000, timeout: 5000 }
                );
            } else {
                startSimulatedDrive();
            }
        }

        function handleUserLocationUpdate(userLat, userLng, heading, speed) {
            const speedEl = document.getElementById('nav-speed');
            if (speedEl) speedEl.textContent = speed;

            if (!startPoint) return;

            const distToStart = calculateDistanceMeters(userLat, userLng, startPoint.lat, startPoint.lng);

            // Phase 1: First reach starting point
            if (distToStart > 60 && !isAtStartPoint) {
                const roundedDist = Math.round(distToStart);
                if (stepDistance) stepDistance.textContent = `Head to Start Point (${roundedDist > 1000 ? (roundedDist / 1000).toFixed(1) + ' km' : roundedDist + ' m'})`;
                if (stepTitle) stepTitle.textContent = `📍 Drive to ${startPoint.instruction || 'Start Point'}`;

                speakInstruction(`Please drive to the start location: ${startPoint.instruction || 'Test Center'}`);
                updateArrowPosition(userLat, userLng, heading);
                return;
            }

            // Phase 2: Reached Start Point -> Start Test Drive
            if (!isAtStartPoint) {
                isAtStartPoint = true;
                if (stepDistance) stepDistance.textContent = 'Test Route Active';
                if (stepTitle) stepTitle.textContent = '🚀 Driving Test Started! Follow route guidance.';
                speakInstruction('Arrived at start location. Driving test practice starting now.');
            }

            updateArrowPosition(userLat, userLng, heading);
        }

        function startSimulatedDrive() {
            if (validPoints.length < 2) return;

            let currentPointIdx = 0;
            if (simulationInterval) clearInterval(simulationInterval);

            simulationInterval = setInterval(() => {
                if (!isDriving) return;

                const pt = validPoints[currentPointIdx];
                if (pt && pt.lat !== null && pt.lng !== null) {
                    if (stepDistance) stepDistance.textContent = `Step ${currentPointIdx + 1} of ${validPoints.length}`;
                    if (stepTitle) stepTitle.textContent = pt.instruction;

                    speakInstruction(pt.instruction);
                    updateArrowPosition(pt.lat, pt.lng, 90, 35);

                    currentPointIdx = (currentPointIdx + 1) % validPoints.length;
                }
            }, 5000);
        }

        function updateArrowPosition(lat, lng, heading = 0, speed = 0) {
            const speedEl = document.getElementById('nav-speed');
            if (speedEl) speedEl.textContent = speed;

            if (map) {
                if (typeof google !== 'undefined' && google.maps && map instanceof google.maps.Map) {
                    map.panTo({ lat: lat, lng: lng });

                    // Create or move navigation arrow marker
                    if (!userArrowMarker) {
                        userArrowMarker = new google.maps.Marker({
                            position: { lat: lat, lng: lng },
                            map: map,
                            icon: {
                                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                                scale: 6,
                                fillColor: '#0284c7',
                                fillOpacity: 1,
                                strokeColor: '#ffffff',
                                strokeWeight: 2,
                                rotation: heading,
                            },
                        });
                    } else {
                        userArrowMarker.setPosition({ lat: lat, lng: lng });
                        const icon = userArrowMarker.getIcon();
                        if (icon) {
                            icon.rotation = heading;
                            userArrowMarker.setIcon(icon);
                        }
                    }
                } else if (typeof L !== 'undefined' && map.panTo) {
                    map.panTo([lat, lng]);
                }
            }
        }
    });
</script>
@endpush
