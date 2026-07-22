@extends('layouts.app')

@section('title', $route->title)

@push('styles')
    <!-- Leaflet CSS for fallback map canvas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw;
        }

        .route-detail-page {
            background-color: #f8fafc;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .08), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(20, 184, 166, .07), transparent 30%),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            color: #0f172a;
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
        }

        .route-card-light {
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(16px);
        }

        /* Fullscreen Navigation Canvas */
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
            background: #f8fafc !important;
            padding: 0 !important;
        }

        #map-wrapper:fullscreen #navigation-map,
        #map-wrapper.is-fullscreen #navigation-map {
            height: 100vh !important;
            border-radius: 0 !important;
            border: none !important;
        }

        #map-wrapper:fullscreen #gmaps-bottom-sheet,
        #map-wrapper.is-fullscreen #gmaps-bottom-sheet {
            position: absolute !important;
            bottom: 0.75rem !important;
            left: 0.75rem !important;
            right: 0.75rem !important;
            max-width: 36rem !important;
            margin-left: auto !important;
            margin-right: auto !important;
            z-index: 9999 !important;
            margin-top: 0 !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25) !important;
        }

        /* Top Google Maps Navigation Light Instruction HUD */
        .nav-hud-light-top {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 50%, #115e59 100%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 12px 32px rgba(15, 118, 110, 0.35);
            backdrop-filter: blur(16px);
        }

        /* STRICT PURE WHITE TEXT OVERRIDES */
        #nav-step-title,
        #nav-step-distance,
        #nav-instruction-banner,
        #nav-instruction-banner h3,
        #nav-instruction-banner div,
        #nav-instruction-banner span,
        #gmaps-btn-start-label,
        #btn-gmaps-start,
        #btn-gmaps-start span,
        #btn-gmaps-start svg {
            color: #ffffff !important;
            fill: #ffffff !important;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

    <div class="route-detail-page min-h-screen py-6 sm:py-8 overflow-x-hidden">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8 max-w-full">

            <!-- Breadcrumbs -->
            <nav class="mb-4 flex flex-wrap items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 max-w-full overflow-hidden" aria-label="Breadcrumb">
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
                <span class="text-slate-900 font-bold truncate max-w-[180px] sm:max-w-none">{{ $route->title }}</span>
            </nav>

            <!-- Page Title Header -->
            <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between max-w-full overflow-hidden">
                <div class="min-w-0 max-w-full">
                    <a href="{{ route('driving-routes.index', $route->city_id ? ['city' => $route->city_id] : []) }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-teal-800 hover:text-teal-900 mb-2 transition">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to routes
                    </a>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 max-w-full">
                        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight break-words max-w-full">{{ $route->title }}</h1>
                        <span class="rounded-full bg-teal-100 text-teal-800 px-2.5 py-0.5 sm:px-3 sm:py-1 text-[10px] sm:text-xs font-black uppercase tracking-wider shrink-0">
                            {{ strtoupper($route->package_type) }} Route
                        </span>
                    </div>
                    <p class="mt-1 text-sm sm:text-lg font-bold text-teal-700">{{ $cityName }}, {{ $route->province }}</p>
                    @if($cityAddress)
                        <p class="mt-0.5 text-xs sm:text-sm text-slate-500 max-w-2xl truncate">{{ $cityAddress }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @if(auth()->user()->is_admin)
                        <span class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-black text-blue-700">Admin Preview Mode</span>
                    @else
                        <span class="rounded-xl border border-teal-200 bg-teal-50 px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-black text-teal-800">
                            {{ $remainingStarts }} {{ \Illuminate\Support\Str::plural('start', $remainingStarts) }} left
                        </span>
                    @endif
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-6 lg:gap-8 lg:grid-cols-3 max-w-full">

                <!-- Left Column (Google Maps Interactive Navigation Engine) -->
                <div class="lg:col-span-2 space-y-6 max-w-full min-w-0">

                    <!-- Interactive Map Container -->
                    <div id="map-wrapper" class="route-card-light relative overflow-hidden p-1.5 sm:p-2 transition-all max-w-full">
                        
                        <!-- Top Navigation Upward Instruction Header (Pure White Text) -->
                        <div id="nav-instruction-banner" class="nav-hud-light-top absolute top-2.5 left-2.5 right-2.5 z-30 rounded-2xl p-2.5 sm:p-3.5 !text-white transition-all max-w-3xl mx-auto">
                            <div class="flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <!-- Maneuver Icon -->
                                    <div id="nav-maneuver-icon-container" class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 backdrop-blur-md">
                                        <svg id="nav-maneuver-icon" class="h-5 w-5 sm:h-6 sm:w-6 !text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div id="nav-step-distance" class="text-[9px] sm:text-xs font-black uppercase tracking-wider !text-white truncate">HEAD TO START POINT</div>
                                        <h3 id="nav-step-title" class="text-xs sm:text-base font-black leading-tight !text-white line-clamp-1 sm:line-clamp-2">
                                            📍 Head toward {{ $route->start_label ?: 'Start Point' }}
                                        </h3>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <!-- Voice Toggle -->
                                    <button type="button" id="btn-toggle-voice" class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-xl bg-white/15 hover:bg-white/25 text-white transition text-xs sm:text-base" title="Voice Guidance On/Off">
                                        <span id="voice-icon">🔊</span>
                                    </button>

                                    <!-- Fullscreen Toggle -->
                                    <button type="button" id="btn-toggle-fullscreen" class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-xl bg-white/15 hover:bg-white/25 text-white transition" title="Toggle Fullscreen Preview">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Navigation Map Canvas (Full Size for Laptops) -->
                        <div id="navigation-map" class="h-[380px] sm:h-[500px] lg:h-[640px] w-full max-w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 z-10"></div>

                        <!-- Google Maps Mobile Bottom Sheet Drive Card (Ultra Compact Height) -->
                        <div id="gmaps-bottom-sheet" class="mt-2 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 p-2.5 sm:p-3.5 shadow-xl z-20 max-w-full overflow-hidden">
                            
                            <!-- Top Drag Handle & Title Bar -->
                            <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base sm:text-lg font-black text-slate-900">Drive</h3>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" id="btn-recenter" class="p-1.5 rounded-full hover:bg-slate-100 text-slate-600 transition" title="Recenter Map">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06z" />
                                        </svg>
                                    </button>
                                    <button type="button" id="btn-share-route" class="p-1.5 rounded-full hover:bg-slate-100 text-slate-600 transition" title="Share Route">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Compact Duration & Distance Summary -->
                            <div class="py-1.5 max-w-full flex items-baseline justify-between">
                                <div class="flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-lg sm:text-xl font-black text-emerald-700 tracking-tight">{{ $route->route_duration_minutes ?: 18 }} min</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-600">({{ $route->route_length_km ?: 16 }} km)</span>
                                </div>
                                <span class="text-[10px] font-semibold text-emerald-700">🍃 Practice Route</span>
                            </div>

                            <!-- Google Maps Iconic Action Buttons Row -->
                            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 pt-1 max-w-full">
                                <!-- Big Dark Teal Start Button (STRICT PURE WHITE TEXT) -->
                                <button type="button" id="btn-gmaps-start" class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-full bg-teal-800 hover:bg-teal-900 !text-white font-black px-5 py-2.5 shadow-md shadow-teal-900/20 transition transform active:scale-95">
                                    <svg class="h-4 w-4 !text-white transform rotate-45 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span id="gmaps-btn-start-label" class="text-sm sm:text-base font-black !text-white">Start Navigation</span>
                                </button>

                                <!-- Light Cyan Add Stops / Waypoints Button -->
                                <button type="button" id="btn-gmaps-stops" class="flex-1 sm:flex-initial flex items-center justify-center gap-1 rounded-full bg-cyan-100 hover:bg-cyan-200 text-teal-900 font-bold px-3.5 py-2.5 text-xs transition">
                                    <svg class="h-3.5 w-3.5 text-teal-800 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span>{{ count($mappedPoints) }} Waypoints</span>
                                </button>

                                <!-- Light Cyan Share Button -->
                                <button type="button" id="btn-gmaps-share" class="flex-1 sm:flex-initial flex items-center justify-center gap-1 rounded-full bg-cyan-100 hover:bg-cyan-200 text-teal-900 font-bold px-3.5 py-2.5 text-xs transition">
                                    <svg class="h-3.5 w-3.5 text-teal-800 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    <span>Share</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Stops Breakdown Card -->
                    @if(count($mappedPoints) > 0)
                        <div id="stops-breakdown-card" class="route-card-light p-4 sm:p-8 max-w-full overflow-hidden">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                                <h3 class="text-lg sm:text-xl font-black text-slate-900">Test Waypoints & Maneuvers</h3>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">
                                    {{ count($mappedPoints) }} Total Stops
                                </span>
                            </div>
                            <div class="space-y-3">
                                @foreach($mappedPoints as $idx => $pt)
                                    <div class="flex items-start gap-3 sm:gap-4 rounded-2xl bg-white p-3.5 sm:p-4 border border-slate-200/80 shadow-sm max-w-full">
                                        <div class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white font-black text-xs">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div class="flex-1 min-w-0 max-w-full">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded-md bg-teal-50 border border-teal-200 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-teal-800">
                                                    {{ strtoupper(str_replace('_', ' ', $pt['maneuver'] ?? 'continue')) }}
                                                </span>
                                            </div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800 mt-1 break-words">
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
                <div class="space-y-6 max-w-full min-w-0">

                    <!-- Route Details Sidebar Card -->
                    <div class="route-card-light p-4 sm:p-6 max-w-full overflow-hidden">
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mb-4 pb-3 border-b border-slate-100">Route Information</h3>
                        
                        <dl class="space-y-3 text-xs sm:text-sm">
                            <div class="flex justify-between py-1.5 border-b border-slate-100 gap-2">
                                <dt class="font-bold text-slate-500 shrink-0">Start Location</dt>
                                <dd class="font-black text-slate-900 text-right truncate">{{ $route->start_label ?: $cityName }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100 gap-2">
                                <dt class="font-bold text-slate-500 shrink-0">Destination</dt>
                                <dd class="font-black text-slate-900 text-right truncate">{{ $route->destination_label ?: 'Return to Start' }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100 gap-2">
                                <dt class="font-bold text-slate-500 shrink-0">Package</dt>
                                <dd class="font-black text-slate-900 text-right uppercase">{{ strtoupper($route->package_type) }} Package</dd>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100 gap-2">
                                <dt class="font-bold text-slate-500 shrink-0">City</dt>
                                <dd class="font-black text-slate-900 text-right truncate">{{ $cityName }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100 gap-2">
                                <dt class="font-bold text-slate-500 shrink-0">Province</dt>
                                <dd class="font-black text-slate-900 text-right truncate">{{ $route->province }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100 gap-2">
                                <dt class="font-bold text-slate-500 shrink-0">Starts Included</dt>
                                <dd class="font-black text-slate-900 text-right">{{ $route->access_limit }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5 gap-2">
                                <dt class="font-bold text-slate-500 shrink-0">Price Paid</dt>
                                <dd class="font-black text-teal-700 text-right">${{ number_format($route->price, 2) }}</dd>
                            </div>
                        </dl>

                        @if($route->preview_pdf_path)
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($route->preview_pdf_path) }}" target="_blank" class="w-full flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition">
                                    📄 Preview Route PDF
                                </a>
                            </div>
                        @endif

                        @if($route->description)
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                <span class="text-xs font-black uppercase text-slate-400 block mb-1">Route Notes</span>
                                <p class="text-xs font-semibold text-slate-600 leading-relaxed break-words">{{ $route->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Related Routes Card -->
                    @if($relatedRoutes->count() > 0)
                        <div class="route-card-light p-4 sm:p-6 max-w-full overflow-hidden">
                            <h3 class="text-base sm:text-lg font-black text-slate-900 mb-4 pb-3 border-b border-slate-100">Other {{ $cityName }} Routes</h3>
                            <div class="space-y-3">
                                @foreach($relatedRoutes as $rel)
                                    <a href="{{ route('driving-routes.show', $rel) }}" class="group block rounded-2xl bg-slate-50 p-3.5 sm:p-4 border border-slate-150 transition hover:bg-teal-50/50 hover:border-teal-200">
                                        <h4 class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-teal-800 transition truncate">{{ $rel->title }}</h4>
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

        const btnGmapsStart = document.getElementById('btn-gmaps-start');
        const gmapsBtnLabel = document.getElementById('gmaps-btn-start-label');
        const btnGmapsStops = document.getElementById('btn-gmaps-stops');
        const btnGmapsShare = document.getElementById('btn-gmaps-share');
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
                voiceIcon.textContent = voiceEnabled ? '🔊' : '🔇';
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

        if (btnGmapsStops) {
            btnGmapsStops.addEventListener('click', () => {
                const card = document.getElementById('stops-breakdown-card');
                if (card) card.scrollIntoView({ behavior: 'smooth' });
            });
        }

        if (btnGmapsShare) {
            btnGmapsShare.addEventListener('click', () => {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(window.location.href);
                    alert('Route link copied to clipboard!');
                }
            });
        }

        // Lock Reload & Prevent Accidental Page Exit Confirmation Prompt
        window.addEventListener('beforeunload', (event) => {
            if (!routeAccess.isAdmin) {
                const warningMsg = '⚠️ Reloading or leaving this page will end your active session and consume another route start limit to reopen. Are you sure you want to exit?';
                event.preventDefault();
                event.returnValue = warningMsg;
                return warningMsg;
            }
        });

        // Initialize Google Maps / Leaflet Engine
        let map, userArrowMarker, routePolyline;

        function initNavigationEngine() {
            const mapContainer = document.getElementById('navigation-map');
            let center = { lat: 43.6532, lng: -79.3832 };

            if (startPoint) {
                center = { lat: startPoint.lat, lng: startPoint.lng };
            }

            if (typeof google !== 'undefined' && google.maps) {
                map = new google.maps.Map(mapContainer, {
                    center: center,
                    zoom: 19,
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
                        strokeOpacity: 0.95,
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
                map = L.map('navigation-map').setView([center.lat, center.lng], 19);
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

        if (btnGmapsStart) {
            btnGmapsStart.addEventListener('click', async () => {
                if (!isDriving) {
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
                    if (gmapsBtnLabel) gmapsBtnLabel.textContent = 'Pause Navigation';
                    startRealTimeLocationNavigation();
                } else {
                    isDriving = false;
                    if (gmapsBtnLabel) gmapsBtnLabel.textContent = 'Resume Navigation';
                    if (simulationInterval) clearInterval(simulationInterval);
                    if (watchId) navigator.geolocation.clearWatch(watchId);
                }
            });
        }

        // Real-Time Hardware Magnetic Compass Listener (North/South/East/West Phone Rotation)
        let deviceCompassHeading = 0;

        function initDeviceOrientationCompass() {
            if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
                DeviceOrientationEvent.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        window.addEventListener('deviceorientation', handleOrientationEvent, true);
                    }
                }).catch(console.error);
            } else {
                if ('ondeviceorientationabsolute' in window) {
                    window.addEventListener('deviceorientationabsolute', handleOrientationEvent, true);
                } else if ('ondeviceorientation' in window) {
                    window.addEventListener('deviceorientation', handleOrientationEvent, true);
                }
            }
        }

        function handleOrientationEvent(event) {
            let heading = null;

            if (event.webkitCompassHeading !== undefined && event.webkitCompassHeading !== null) {
                heading = event.webkitCompassHeading;
            } else if (event.alpha !== null && event.alpha !== undefined) {
                heading = (360 - event.alpha) % 360;
            }

            if (heading !== null && !isNaN(heading)) {
                deviceCompassHeading = Math.round(heading);
                applyHeadingRotation(deviceCompassHeading);
            }
        }

        initDeviceOrientationCompass();

        function applyHeadingRotation(headingDeg) {
            if (userArrowMarker && typeof google !== 'undefined' && google.maps) {
                const icon = userArrowMarker.getIcon();
                if (icon) {
                    icon.rotation = headingDeg;
                    userArrowMarker.setIcon(icon);
                }
            }
            if (map && typeof map.setHeading === 'function') {
                map.setHeading(headingDeg);
            }
        }

        if (btnRecenter) {
            btnRecenter.addEventListener('click', () => recenterMap());
        }

        function recenterMap() {
            if (startPoint && map) {
                if (typeof google !== 'undefined' && google.maps && map instanceof google.maps.Map) {
                    map.panTo({ lat: startPoint.lat, lng: startPoint.lng });
                    map.setZoom(19);
                } else if (typeof L !== 'undefined' && map.panTo) {
                    map.setView([startPoint.lat, startPoint.lng], 19);
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
                        const heading = (pos.coords.heading !== null && pos.coords.heading !== undefined && !isNaN(pos.coords.heading)) 
                                        ? pos.coords.heading 
                                        : deviceCompassHeading;

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
            if (!startPoint) return;

            const distToStart = calculateDistanceMeters(userLat, userLng, startPoint.lat, startPoint.lng);

            if (distToStart > 60 && !isAtStartPoint) {
                const roundedDist = Math.round(distToStart);
                if (stepDistance) stepDistance.textContent = `HEAD TO START POINT (${roundedDist > 1000 ? (roundedDist / 1000).toFixed(1) + ' km' : roundedDist + ' m'})`;
                if (stepTitle) stepTitle.textContent = `📍 Drive to ${startPoint.instruction || 'Start Point'}`;

                speakInstruction(`Please drive to the start location: ${startPoint.instruction || 'Test Center'}`);
                updateArrowPosition(userLat, userLng, heading, speed);
                return;
            }

            if (!isAtStartPoint) {
                isAtStartPoint = true;
                if (stepDistance) stepDistance.textContent = 'TEST ROUTE ACTIVE';
                if (stepTitle) stepTitle.textContent = '🚀 Driving Test Started! Follow route guidance.';
                speakInstruction('Arrived at start location. Driving test practice starting now.');
            }

            updateArrowPosition(userLat, userLng, heading, speed);
        }

        function startSimulatedDrive() {
            if (validPoints.length < 2) return;

            let currentPointIdx = 0;
            if (simulationInterval) clearInterval(simulationInterval);

            simulationInterval = setInterval(() => {
                if (!isDriving) return;

                const pt = validPoints[currentPointIdx];
                if (pt && pt.lat !== null && pt.lng !== null) {
                    if (stepDistance) stepDistance.textContent = `STEP ${currentPointIdx + 1} OF ${validPoints.length}`;
                    if (stepTitle) stepTitle.textContent = pt.instruction;

                    speakInstruction(pt.instruction);
                    updateArrowPosition(pt.lat, pt.lng, (currentPointIdx * 45) % 360, 35);

                    currentPointIdx = (currentPointIdx + 1) % validPoints.length;
                }
            }, 5000);
        }

        function updateArrowPosition(lat, lng, heading = 0, speed = 0) {
            const activeHeading = (heading !== undefined && heading !== null && !isNaN(heading) && heading !== 0) 
                                ? heading 
                                : deviceCompassHeading;

            if (map) {
                if (typeof google !== 'undefined' && google.maps && map instanceof google.maps.Map) {
                    map.panTo({ lat: lat, lng: lng });
                    map.setZoom(19);

                    if (map.setHeading && typeof map.setHeading === 'function') {
                        map.setHeading(activeHeading);
                    }

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
                                rotation: activeHeading,
                            },
                        });
                    } else {
                        userArrowMarker.setPosition({ lat: lat, lng: lng });
                        const icon = userArrowMarker.getIcon();
                        if (icon) {
                            icon.rotation = activeHeading;
                            userArrowMarker.setIcon(icon);
                        }
                    }
                } else if (typeof L !== 'undefined' && map.panTo) {
                    map.setView([lat, lng], 19);
                }
            }
        }
    });
</script>
@endpush
