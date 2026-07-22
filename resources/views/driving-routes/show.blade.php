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

        /* Arrow Marker Rotation Animation */
        .car-heading-marker {
            transition: transform 300ms ease-out;
        }

        /* Custom Pins */
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

                <!-- Left Column (In-App Google Maps Turn-by-Turn Navigation Engine) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- In-App Google Maps Interactive Navigation Box -->
                    <div id="map-wrapper" class="route-card-glass relative overflow-hidden p-2 transition-all">
                        
                        <!-- Top Google Maps Instruction Upward Banner -->
                        <div id="nav-instruction-banner" class="nav-hud-top absolute top-4 left-4 right-4 z-20 rounded-2xl p-4 sm:p-5 text-white shadow-2xl transition-all">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <!-- Maneuver Icon -->
                                    <div id="nav-maneuver-icon-container" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md">
                                        <svg id="nav-maneuver-icon" class="h-8 w-8 text-teal-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div id="nav-step-distance" class="text-xs font-black uppercase tracking-wider text-teal-200">In 50 meters</div>
                                        <h3 id="nav-step-title" class="text-lg sm:text-xl font-black leading-snug">
                                            {{ $mappedPoints->first()['instruction'] ?? 'Prepare to start test route drive' }}
                                        </h3>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <!-- Voice Toggle -->
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
                        <div id="navigation-map" class="h-[520px] w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 z-10"></div>

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

                            <!-- Start Drive Action Buttons -->
                            <div class="flex items-center gap-2">
                                <button type="button" id="btn-recenter" class="hidden rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition">
                                    🎯 Recenter
                                </button>

                                <button type="button" id="btn-start-drive" class="btn-google-maps flex-1 sm:flex-initial flex items-center justify-center gap-2 rounded-xl py-3 px-6 text-base font-black text-white transition active:scale-95">
                                    <svg class="h-5 w-5 text-teal-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span id="btn-start-drive-text">Start Practice Drive</span>
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
        let voiceEnabled = true;
        let currentStepIdx = 0;
        let watchId = null;
        let simulationInterval = null;

        const btnStart = document.getElementById('btn-start-drive');
        const btnStartText = document.getElementById('btn-start-drive-text');
        const btnVoice = document.getElementById('btn-toggle-voice');
        const voiceIcon = document.getElementById('voice-icon');
        const btnFullscreen = document.getElementById('btn-toggle-fullscreen');
        const mapWrapper = document.getElementById('map-wrapper');
        const stepTitle = document.getElementById('nav-step-title');
        const stepDistance = document.getElementById('nav-step-distance');

        // Speech Synthesis for Voice Guidance
        function speakInstruction(text) {
            if (!voiceEnabled || !('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel(); // stop previous
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            window.speechSynthesis.speak(utterance);
        }

        // Voice Toggle
        if (btnVoice) {
            btnVoice.addEventListener('click', () => {
                voiceEnabled = !voiceEnabled;
                voiceIcon.textContent = voiceEnabled ? '🔊' : '🔇';
            });
        }

        // Fullscreen Toggle
        if (btnFullscreen && mapWrapper) {
            btnFullscreen.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    if (mapWrapper.requestFullscreen) {
                        mapWrapper.requestFullscreen();
                    } else {
                        mapWrapper.classList.toggle('is-fullscreen');
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                    mapWrapper.classList.remove('is-fullscreen');
                }
            });
        }

        // Initialize Map (Google Maps JS API or Leaflet Fallback)
        let map, userMarker, routePolyline;
        const validPoints = pointsData.filter(p => p.lat !== null && p.lng !== null && !isNaN(p.lat) && !isNaN(p.lng));

        function initNavigationEngine() {
            const mapContainer = document.getElementById('navigation-map');
            let center = { lat: 43.6532, lng: -79.3832 };

            if (validPoints.length > 0) {
                center = { lat: validPoints[0].lat, lng: validPoints[0].lng };
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

                // Markers & Polyline
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

                    // Start/End Markers
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

        // Start Practice Drive Action
        if (btnStart) {
            btnStart.addEventListener('click', async () => {
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
                    btnStartText.textContent = 'Pause Drive';

                    // Announce first step
                    if (validPoints.length > 0) {
                        const firstStep = validPoints[0].instruction || 'Practice drive started. Follow highlighted route.';
                        if (stepTitle) stepTitle.textContent = firstStep;
                        speakInstruction(firstStep);
                    }

                    // Start GPS / Simulation
                    startDriveTracking();
                } else {
                    isDriving = false;
                    btnStartText.textContent = 'Resume Drive';
                    if (simulationInterval) clearInterval(simulationInterval);
                    if (watchId) navigator.geolocation.clearWatch(watchId);
                }
            });
        }

        function startDriveTracking() {
            if ('geolocation' in navigator) {
                watchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        const speed = pos.coords.speed ? Math.round(pos.coords.speed * 3.6) : 0;
                        const heading = pos.coords.heading || 0;

                        updateNavPosition(lat, lng, heading, speed);
                    },
                    (err) => {
                        console.warn('GPS position error, starting simulation:', err);
                        startSimulatedDrive();
                    },
                    { enableHighAccuracy: true, maximumAge: 1000, timeout: 5000 }
                );
            } else {
                startSimulatedDrive();
            }
        }

        function startSimulatedDrive() {
            if (validPoints.length < 2) return;

            let currentPointIdx = 0;
            if (simulationInterval) clearInterval(simulationInterval);

            simulationInterval = setInterval(() => {
                if (!isDriving) return;

                const pt = validPoints[currentPointIdx];
                if (pt && pt.lat !== null && pt.lng !== null) {
                    updateNavPosition(pt.lat, pt.lng, 90, 40);

                    if (stepTitle) stepTitle.textContent = pt.instruction;
                    speakInstruction(pt.instruction);

                    currentPointIdx = (currentPointIdx + 1) % validPoints.length;
                }
            }, 5000);
        }

        function updateNavPosition(lat, lng, heading, speed) {
            const speedEl = document.getElementById('nav-speed');
            if (speedEl) speedEl.textContent = speed;

            if (map) {
                if (typeof google !== 'undefined' && google.maps && map instanceof google.maps.Map) {
                    map.panTo({ lat: lat, lng: lng });
                } else if (typeof L !== 'undefined' && map.panTo) {
                    map.panTo([lat, lng]);
                }
            }
        }
    });
</script>
@endpush
