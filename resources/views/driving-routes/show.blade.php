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
            bottom: 1.25rem !important;
            left: 1rem !important;
            right: 1rem !important;
            max-width: 44rem !important;
            margin-left: auto !important;
            margin-right: auto !important;
            z-index: 9999 !important;
            margin-top: 0 !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35) !important;
            background: rgba(255, 255, 255, 0.96) !important;
            backdrop-filter: blur(16px) !important;
            border-radius: 1.5rem !important;
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
                            {{ $route->package_type === 'g1' ? 'G2 Test Routes' : 'G Test Routes' }}
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

                                    <!-- Screen Keep-Alive Toggle -->
                                    <button type="button" id="btn-toggle-screen-keep-alive" class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-xl bg-white/15 hover:bg-white/25 text-white transition text-base" title="Keep Screen On During Navigation">
                                        <span id="screen-keep-alive-icon" class="animate-pulse">🔋</span>
                                    </button>

                                    <!-- Screen On Indicator -->
                                    <div class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-xl bg-white/15 text-white transition text-base" title="Screen Stay-On: Mobile devices will not timeout while viewing">
                                        <span id="screen-on-indicator" class="animate-pulse">📱</span>
                                    </div>

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

                        <!-- Google Maps Mobile Bottom Sheet Drive Card (Ultra Compact & Highly Responsive) -->
                        <div id="gmaps-bottom-sheet" class="mt-2.5 rounded-2xl sm:rounded-3xl bg-white/95 backdrop-blur-md border border-slate-200/90 p-3 sm:p-4 shadow-xl z-20 max-w-full overflow-hidden transition-all duration-300">
                            
                            <!-- Top Drag Handle & Title Bar -->
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Drive Test Practice</h3>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" id="btn-recenter" class="p-2 rounded-xl hover:bg-slate-100 text-slate-700 transition active:scale-95" title="Recenter Map">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06z" />
                                        </svg>
                                    </button>
                                    <button type="button" id="btn-share-route" class="p-2 rounded-xl hover:bg-slate-100 text-slate-700 transition active:scale-95" title="Share Route">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Compact Duration & Distance Summary -->
                            <div class="py-2 max-w-full flex items-center justify-between">
                                <div class="flex items-baseline gap-2 flex-wrap">
                                    <span class="text-xl sm:text-2xl font-black text-teal-800 tracking-tight leading-none">{{ $route->route_duration_minutes ?: 18 }} min</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-500">({{ $route->route_length_km ?: 16 }} km)</span>
                                </div>
                                <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-black text-emerald-800 uppercase tracking-wider">
                                    🍃 Practice Route
                                </span>
                            </div>

                            <!-- Responsive Action Buttons Grid/Flex -->
                            <div class="grid grid-cols-2 md:flex md:items-center gap-2 sm:gap-2.5 pt-1.5 max-w-full">
                                <!-- Big Dark Teal Start Button (STRICT PURE WHITE TEXT) -->
                                <button type="button" id="btn-gmaps-start" class="col-span-1 md:flex-1 min-h-[44px] sm:min-h-[46px] flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-teal-800 to-emerald-800 hover:from-teal-900 hover:to-emerald-900 !text-white font-black px-4 sm:px-5 py-2.5 shadow-md shadow-teal-900/20 transition transform active:scale-95">
                                    <svg class="h-4 w-4 !text-white transform rotate-45 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span id="gmaps-btn-start-label" class="text-xs sm:text-sm font-black !text-white whitespace-nowrap">Start Live GPS</span>
                                </button>

                                <!-- Vivid Emerald Demo Simulation Button -->
                                <button type="button" id="btn-demo-nav" class="col-span-1 md:flex-1 min-h-[44px] sm:min-h-[46px] flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 !text-white font-black px-4 sm:px-5 py-2.5 shadow-md shadow-emerald-900/20 transition transform active:scale-95">
                                    <svg class="h-4 w-4 !text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span id="demo-btn-label" class="text-xs sm:text-sm font-black !text-white whitespace-nowrap">Demo Drive</span>
                                </button>

                                <!-- Light Cyan Waypoints Button -->
                                <button type="button" id="btn-gmaps-stops" class="col-span-1 md:shrink-0 min-h-[44px] sm:min-h-[46px] flex items-center justify-center gap-1.5 rounded-2xl bg-cyan-50 hover:bg-cyan-100 border border-cyan-200/80 text-teal-900 font-bold px-3.5 sm:px-4 py-2.5 text-xs sm:text-sm transition transform active:scale-95 whitespace-nowrap">
                                    <svg class="h-4 w-4 text-teal-800 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span>{{ count($mappedPoints) }} Waypoints</span>
                                </button>

                                <!-- Light Cyan Share Button -->
                                <button type="button" id="btn-gmaps-share" class="col-span-1 md:shrink-0 min-h-[44px] sm:min-h-[46px] flex items-center justify-center gap-1.5 rounded-2xl bg-cyan-50 hover:bg-cyan-100 border border-cyan-200/80 text-teal-900 font-bold px-3.5 sm:px-4 py-2.5 text-xs sm:text-sm transition transform active:scale-95 whitespace-nowrap">
                                    <svg class="h-4 w-4 text-teal-800 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
                                <dd class="font-black text-slate-900 text-right uppercase">{{ $route->package_type === 'g1' ? 'G2 Test Routes' : 'G Test Routes' }}</dd>
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

                        <!-- Examiner Sheets Download Section -->
                        @php
                            $canDownloadExaminer = !auth()->user()->is_admin && $remainingStarts > 0;
                            $showExaminerSection = $canDownloadExaminer || auth()->user()->is_admin;
                        @endphp

                        @if($showExaminerSection)
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                <span class="text-xs font-black uppercase text-slate-400 block mb-3">📋 Examiner Sheet</span>
                                <a href="{{ route('download.examiner-sheet', $route->package_type) }}" target="_blank" class="w-full flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 py-2.5 px-3 text-xs sm:text-sm font-bold text-blue-700 shadow-sm transition">
                                    📋 Download {{ strtoupper($route->package_type) }} Examiner Sheet
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
@php
    $effectiveMapsKey = $mapsKey ?? config('services.google.maps_key');
@endphp
<script>
    window.googleMapsLoaded = false;
    window.initGoogleMapsCallback = function() {
        window.googleMapsLoaded = true;
        if (typeof window.triggerInitNavEngine === 'function') {
            window.triggerInitNavEngine();
        }
    };
</script>
@if($effectiveMapsKey)
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $effectiveMapsKey }}&libraries=places,geometry&callback=initGoogleMapsCallback&loading=async" async defer></script>
@endif
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // ===== SCREEN WAKE LOCK (PREVENT MOBILE SCREEN TIMEOUT) =====
    let wakeLock = null;

    async function requestWakeLock() {
        try {
            if ('wakeLock' in navigator) {
                wakeLock = await navigator.wakeLock.request('screen');
                console.log('✅ Wake Lock acquired - screen will stay on during navigation');
                
                // Re-acquire wake lock if the page regains focus after sleep
                document.addEventListener('visibilitychange', async () => {
                    if (!document.hidden && !wakeLock) {
                        try {
                            wakeLock = await navigator.wakeLock.request('screen');
                            console.log('✅ Wake Lock re-acquired after visibility change');
                        } catch (err) {
                            console.warn('⚠️ Failed to re-acquire wake lock:', err);
                        }
                    }
                });
                
                return true;
            } else {
                console.log('⚠️ Wake Lock API not supported - using fallback methods');
                return false;
            }
        } catch (err) {
            console.warn('⚠️ Wake Lock request failed:', err);
            return false;
        }
    }

    // Aggressive fallback: Continuous screen activity
    let screenKeepAliveInterval = null;
    
    function startScreenKeepAlive() {
        if (screenKeepAliveInterval) clearInterval(screenKeepAliveInterval);
        
        // Simulate user activity every 10 seconds
        screenKeepAliveInterval = setInterval(() => {
            // Create and trigger a minimal user interaction
            try {
                // Trigger visibility change detection
                if (document.hidden === false) {
                    // Create a temporary invisible element to trigger reflow
                    const el = document.createElement('div');
                    el.style.display = 'none';
                    document.body.appendChild(el);
                    document.body.removeChild(el);
                    
                    // Dispatch a custom activity event
                    window.dispatchEvent(new Event('userActivity'));
                }
            } catch (e) {
                // Silent fallback
            }
        }, 10000); // Every 10 seconds
        
        console.log('📱 Screen keep-alive activity started');
    }

    // Alternative fallback: Silent video loop (more reliable on iOS)
    function enableFallbackScreenKeepAlive() {
        try {
            // Create a minimal video element
            let videoEl = document.getElementById('screen-keep-alive-video');
            if (!videoEl) {
                videoEl = document.createElement('video');
                videoEl.id = 'screen-keep-alive-video';
                videoEl.style.display = 'none';
                videoEl.style.position = 'fixed';
                videoEl.style.width = '1px';
                videoEl.style.height = '1px';
                videoEl.style.pointerEvents = 'none';
                videoEl.style.opacity = '0';
                videoEl.loop = true;
                videoEl.muted = true;
                videoEl.volume = 0;
                videoEl.autoplay = true;
                videoEl.playsInline = true;
                
                // Use a data URL with a minimal MP4 video
                const base64Mp4 = 'AAAAIGZ0eXBpc29tAAACAGlzb21pc28yYXZjMW1wNDEAAAAIZnJlZQAACJYbYnJ1dAAAABRnYXAAAAABgAAAQBYBf//3//t3gAAABxmd2R0bAABAAEAAQABAHhkbWRhAACAgNiQCAAQBv';
                videoEl.src = 'data:video/mp4;base64,' + base64Mp4;
                
                document.body.appendChild(videoEl);
                
                // Force play with user gesture fallback
                videoEl.play().then(() => {
                    console.log('✅ Fallback video playback started');
                }).catch(err => {
                    console.warn('⚠️ Video playback requires user interaction:', err);
                });
            }
        } catch (e) {
            console.warn('⚠️ Video fallback error:', e);
        }
    }

    function releaseWakeLock() {
        if (wakeLock) {
            wakeLock.release().then(() => {
                wakeLock = null;
                console.log('✅ Wake Lock released');
            }).catch(err => {
                console.warn('⚠️ Error releasing wake lock:', err);
            });
        }
        
        if (screenKeepAliveInterval) {
            clearInterval(screenKeepAliveInterval);
            screenKeepAliveInterval = null;
            console.log('✅ Screen keep-alive activity stopped');
        }
        
        const videoEl = document.getElementById('screen-keep-alive-video');
        if (videoEl) {
            videoEl.pause();
            videoEl.remove();
            console.log('✅ Fallback video removed');
        }
    }

    // Initialize on page load
    async function initScreenKeepAlive() {
        const hasWakeLock = await requestWakeLock();
        
        // Always use fallback methods as backup
        startScreenKeepAlive();
        enableFallbackScreenKeepAlive();
        
        console.log('📱 Screen keep-alive initialized (Wake Lock: ' + (hasWakeLock ? 'supported' : 'fallback') + ')');
    }

    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        releaseWakeLock();
    });

    // Start keep-alive when page is interactive
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScreenKeepAlive);
    } else {
        initScreenKeepAlive();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const pointsData = @json($mappedPoints);
        const googleMapsUrl = @json($route->google_maps_url);
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
        const btnScreenKeepAlive = document.getElementById('btn-toggle-screen-keep-alive');
        const screenKeepAliveIcon = document.getElementById('screen-keep-alive-icon');
        const btnFullscreen = document.getElementById('btn-toggle-fullscreen');
        const mapWrapper = document.getElementById('map-wrapper');
        const stepTitle = document.getElementById('nav-step-title');
        const stepDistance = document.getElementById('nav-step-distance');

        let screenKeepAliveEnabled = true;

        if (btnScreenKeepAlive) {
            btnScreenKeepAlive.addEventListener('click', () => {
                screenKeepAliveEnabled = !screenKeepAliveEnabled;
                
                if (screenKeepAliveEnabled) {
                    // Re-enable all keep-alive methods
                    if (wakeLock === null) {
                        requestWakeLock().then(success => {
                            if (success) {
                                console.log('✅ Wake Lock re-enabled');
                            }
                        });
                    }
                    startScreenKeepAlive();
                    enableFallbackScreenKeepAlive();
                    screenKeepAliveIcon.textContent = '🔋';
                    screenKeepAliveIcon.classList.add('animate-pulse');
                } else {
                    // Disable keep-alive
                    releaseWakeLock();
                    screenKeepAliveIcon.textContent = '🔋';
                    screenKeepAliveIcon.classList.remove('animate-pulse');
                }
            });
        }

        const validPoints = pointsData.filter(p => p.lat !== null && p.lng !== null && !isNaN(p.lat) && !isNaN(p.lng));
        const startPoint = validPoints.length > 0 ? validPoints[0] : null;

        function parseGoogleMapsUrlStops(url) {
            if (!url) return null;

            try {
                const urlObj = new URL(url);
                const params = urlObj.searchParams;

                if (params.has('origin') && params.has('destination')) {
                    const originStr = decodeURIComponent(params.get('origin'));
                    const destStr = decodeURIComponent(params.get('destination'));
                    const waypointsStr = params.get('waypoints') ? decodeURIComponent(params.get('waypoints')) : '';

                    const parseCoordOrQuery = (str) => {
                        const coordMatch = str.trim().match(/^(-?\d+\.\d+),\s*(-?\d+\.\d+)$/);
                        if (coordMatch) {
                            return { lat: parseFloat(coordMatch[1]), lng: parseFloat(coordMatch[2]) };
                        }
                        return str.trim();
                    };

                    const origin = parseCoordOrQuery(originStr);
                    const destination = parseCoordOrQuery(destStr);
                    const waypointsList = [];

                    if (waypointsStr) {
                        const wayStops = waypointsStr.split('|');
                        wayStops.forEach(st => {
                            if (st.trim()) {
                                waypointsList.push({
                                    location: parseCoordOrQuery(st),
                                    stopover: false,
                                });
                            }
                        });
                    }

                    return { origin, destination, waypointsList };
                }
            } catch (e) {
                // Fallback for path-based URL
            }

            if (url.includes('/maps/dir/')) {
                const match = url.match(/\/maps\/dir\/([^\?]+)/);
                if (match && match[1]) {
                    const rawPath = match[1].split('/@')[0];
                    const parts = rawPath.split('/').filter(p => p && !p.startsWith('data=') && !p.startsWith('am=') && !p.startsWith('entry='));

                    const stops = [];
                    parts.forEach((p) => {
                        const decoded = decodeURIComponent(p);
                        const coordMatch = decoded.match(/^(-?\d+\.\d+),\s*(-?\d+\.\d+)$/);
                        if (coordMatch) {
                            stops.push({ lat: parseFloat(coordMatch[1]), lng: parseFloat(coordMatch[2]) });
                        } else {
                            stops.push(decoded.replace(/\+/g, ' '));
                        }
                    });

                    if (stops.length >= 2) {
                        const origin = stops[0];
                        const destination = stops[stops.length - 1];
                        const waypointsList = stops.slice(1, -1).map(st => ({
                            location: st,
                            stopover: false,
                        }));
                        return { origin, destination, waypointsList };
                    }
                }
            }

            return null;
        }

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
        let map, userArrowMarker, routePolyline, directionsService, directionsRenderer;

        function initNavigationEngine() {
            const mapContainer = document.getElementById('navigation-map');
            if (!mapContainer) return;

            let center = { lat: 43.6532, lng: -79.3832 };

            if (startPoint) {
                center = { lat: startPoint.lat, lng: startPoint.lng };
            }

            if (typeof google !== 'undefined' && google.maps && typeof google.maps.Map === 'function') {
                try {
                    map = new google.maps.Map(mapContainer, {
                        center: center,
                        zoom: 15,
                        mapTypeId: 'roadmap',
                        disableDefaultUI: false,
                    });

                    if (validPoints.length > 0) {
                        const bounds = new google.maps.LatLngBounds();
                        validPoints.forEach((pt, idx) => {
                            const pos = { lat: pt.lat, lng: pt.lng };
                            bounds.extend(pos);
                            new google.maps.Marker({
                                position: pos,
                                map: map,
                                label: {
                                    text: idx === 0 ? 'S' : (idx === validPoints.length - 1 ? 'D' : `${idx + 1}`),
                                    color: '#ffffff',
                                    fontWeight: 'bold'
                                },
                            });
                        });
                        map.fitBounds(bounds);
                    }

                    directionsService = new google.maps.DirectionsService();
                    directionsRenderer = new google.maps.DirectionsRenderer({
                        map: map,
                        suppressMarkers: true,
                        polylineOptions: {
                            strokeColor: '#0284c7',
                            strokeOpacity: 0.95,
                            strokeWeight: 6,
                        }
                    });

                    let routeInfo = null;
                    try {
                        routeInfo = parseGoogleMapsUrlStops(googleMapsUrl);
                    } catch (e) {
                        console.warn('URL parsing error:', e);
                    }

                    let origin = null;
                    let destination = null;
                    let waypointsList = [];

                    if (routeInfo) {
                        origin = routeInfo.origin;
                        destination = routeInfo.destination;
                        waypointsList = routeInfo.waypointsList || [];
                    } else if (validPoints.length >= 2) {
                        origin = { lat: validPoints[0].lat, lng: validPoints[0].lng };
                        destination = { lat: validPoints[validPoints.length - 1].lat, lng: validPoints[validPoints.length - 1].lng };

                        let intermediate = validPoints.slice(1, -1);
                        waypointsList = intermediate.map(pt => ({
                            location: { lat: pt.lat, lng: pt.lng },
                            stopover: false,
                        }));
                    }

                    if (waypointsList.length > 23) {
                        const stepRatio = waypointsList.length / 23;
                        const sampled = [];
                        for (let i = 0; i < 23; i++) {
                            sampled.push(waypointsList[Math.floor(i * stepRatio)]);
                        }
                        waypointsList = sampled;
                    }

                    if (origin && destination) {
                        directionsService.route({
                            origin: origin,
                            destination: destination,
                            waypoints: waypointsList,
                            travelMode: google.maps.TravelMode.DRIVING,
                        }, (result, status) => {
                            if (status === 'OK' && result.routes && result.routes[0]) {
                                directionsRenderer.setDirections(result);
                                if (result.routes[0].bounds) {
                                    map.fitBounds(result.routes[0].bounds);
                                }

                                activeRoadPath = [];
                                const route = result.routes[0];
                                if (route.legs) {
                                    route.legs.forEach(leg => {
                                        if (leg.steps) {
                                            leg.steps.forEach(step => {
                                                if (step.path) {
                                                    step.path.forEach(pt => {
                                                        activeRoadPath.push({ lat: pt.lat(), lng: pt.lng() });
                                                    });
                                                }
                                            });
                                        }
                                    });
                                }

                                if (activeRoadPath.length === 0 && route.overview_path) {
                                    activeRoadPath = route.overview_path.map(pt => ({ lat: pt.lat(), lng: pt.lng() }));
                                }
                            } else {
                                console.warn('DirectionsService status:', status, '- drawing fallback polyline');
                                drawFallbackPolyline();
                            }
                        });
                    } else {
                        drawFallbackPolyline();
                    }
                } catch (err) {
                    console.error('Google Maps initialization exception:', err);
                    drawFallbackPolyline();
                }

                function drawFallbackPolyline() {
                    if (!map) return;
                    const latLngs = validPoints.map(p => ({ lat: p.lat, lng: p.lng }));
                    if (latLngs.length > 0) {
                        if (!routePolyline) {
                            routePolyline = new google.maps.Polyline({
                                path: latLngs,
                                geodesic: true,
                                strokeColor: '#0284c7',
                                strokeOpacity: 0.95,
                                strokeWeight: 6,
                                map: map,
                            });
                        }
                        const bounds = new google.maps.LatLngBounds();
                        latLngs.forEach(pt => bounds.extend(pt));
                        map.fitBounds(bounds);
                    }
                }
            } else if (typeof L !== 'undefined') {
                map = L.map('navigation-map').setView([center.lat, center.lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                setTimeout(() => {
                    if (map && map.invalidateSize) {
                        map.invalidateSize();
                    }
                }, 250);

                if (validPoints.length > 0) {
                    const coords = validPoints.map(p => [p.lat, p.lng]);
                    L.polyline(coords, { color: '#0284c7', weight: 6 }).addTo(map);

                    const bounds = L.latLngBounds(coords);
                    map.fitBounds(bounds);

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

        let isMapInitialized = false;

        window.triggerInitNavEngine = function() {
            if (isMapInitialized) return;

            const isGoogleAvailable = (typeof google !== 'undefined' && google.maps && typeof google.maps.Map === 'function');
            
            if (isGoogleAvailable) {
                isMapInitialized = true;
                initNavigationEngine();
            } else if (!@json((bool)$effectiveMapsKey)) {
                isMapInitialized = true;
                initNavigationEngine();
            }
        };

        if (window.googleMapsLoaded || (typeof google !== 'undefined' && google.maps && typeof google.maps.Map === 'function')) {
            window.triggerInitNavEngine();
        } else if (@json((bool)$effectiveMapsKey)) {
            let checkAttempts = 0;
            const googleCheckInterval = setInterval(() => {
                checkAttempts++;
                if (typeof google !== 'undefined' && google.maps && typeof google.maps.Map === 'function') {
                    clearInterval(googleCheckInterval);
                    window.triggerInitNavEngine();
                } else if (checkAttempts >= 20) { // 6 seconds wait
                    clearInterval(googleCheckInterval);
                    if (!isMapInitialized) {
                        isMapInitialized = true;
                        initNavigationEngine();
                    }
                }
            }, 300);
        } else {
            isMapInitialized = true;
            initNavigationEngine();
        }

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

        const btnDemoNav = document.getElementById('btn-demo-nav');
        const demoBtnLabel = document.getElementById('demo-btn-label');
        let activeRoadPath = [];
        let demoSubStepTimer = null;
        let demoRoadPathIndex = 0;

        function calculateBearing(lat1, lon1, lat2, lon2) {
            const toRad = x => x * Math.PI / 180;
            const toDeg = x => x * 180 / Math.PI;

            const dLon = toRad(lon2 - lon1);
            const y = Math.sin(dLon) * Math.cos(toRad(lat2));
            const x = Math.cos(toRad(lat1)) * Math.sin(toRad(lat2)) -
                      Math.sin(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.cos(dLon);

            const brng = toDeg(Math.atan2(y, x));
            return (brng + 360) % 360;
        }

        function snapToRoadPath(userLat, userLng) {
            if (!activeRoadPath || activeRoadPath.length === 0) {
                return { lat: userLat, lng: userLng };
            }

            let minDist = Infinity;
            let closestPt = { lat: userLat, lng: userLng };

            for (let i = 0; i < activeRoadPath.length; i++) {
                const pt = activeRoadPath[i];
                const dist = calculateDistanceMeters(userLat, userLng, pt.lat, pt.lng);
                if (dist < minDist) {
                    minDist = dist;
                    closestPt = pt;
                }
            }

            if (minDist <= 35) {
                return closestPt;
            }

            return { lat: userLat, lng: userLng };
        }

        let demoAnimationReq = null;
        let isDemoDriving = false;

        function stopDemoDrive() {
            isDemoDriving = false;
            if (demoAnimationReq) {
                cancelAnimationFrame(demoAnimationReq);
                demoAnimationReq = null;
            }
            if (demoBtnLabel) demoBtnLabel.textContent = 'Demo Drive';
            if (btnDemoNav) {
                btnDemoNav.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                btnDemoNav.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
            }
            if (stepTitle) stepTitle.textContent = `📍 Head toward ${startPoint ? (startPoint.instruction || 'Start Point') : 'Start Point'}`;
            if (stepDistance) stepDistance.textContent = 'HEAD TO START POINT';
        }

        function startDemoDrive() {
            const pathPoints = activeRoadPath.length > 1 ? activeRoadPath : validPoints;

            if (!pathPoints || pathPoints.length < 2) {
                alert('No valid road path available for demo navigation.');
                return;
            }

            if (isDriving) {
                isDriving = false;
                if (gmapsBtnLabel) gmapsBtnLabel.textContent = 'Start Live GPS';
                if (watchId) navigator.geolocation.clearWatch(watchId);
            }

            isDemoDriving = true;
            if (demoBtnLabel) demoBtnLabel.textContent = 'Pause Demo';
            if (btnDemoNav) {
                btnDemoNav.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                btnDemoNav.classList.add('bg-amber-600', 'hover:bg-amber-700');
            }

            currentNavStepIdx = 0;
            let segIdx = 0;
            let segStartTime = null;
            let lastPanTime = 0;

            // Realistic driving test speed: ~40 km/h = ~11 meters per second
            const SIMULATED_SPEED_MPS = 11;

            const announceStepIfNeeded = (currentLat, currentLng) => {
                if (!validPoints || validPoints.length === 0) return;
                if (currentNavStepIdx >= validPoints.length) return;

                const targetPt = validPoints[currentNavStepIdx];
                const distToTarget = calculateDistanceMeters(currentLat, currentLng, targetPt.lat, targetPt.lng);
                const distFormatted = distToTarget > 1000 
                    ? (distToTarget / 1000).toFixed(1) + ' km' 
                    : Math.round(distToTarget) + ' meters';

                if (stepDistance) {
                    stepDistance.textContent = `DEMO SIMULATION • STEP ${currentNavStepIdx + 1} OF ${validPoints.length} (${distFormatted})`;
                }

                const instructionText = targetPt.instruction || `Waypoint ${currentNavStepIdx + 1}`;
                if (stepTitle && stepTitle.textContent !== instructionText) {
                    stepTitle.textContent = instructionText;
                    speakInstruction(`In ${distFormatted}, ${instructionText}`);
                }

                if (distToTarget <= 30 && currentNavStepIdx < validPoints.length - 1) {
                    currentNavStepIdx++;
                }
            };

            if (demoAnimationReq) cancelAnimationFrame(demoAnimationReq);

            const startPos = pathPoints[0];
            const nextPos = pathPoints[1];
            const initialBearing = calculateBearing(startPos.lat, startPos.lng, nextPos.lat, nextPos.lng);
            updateArrowPosition(startPos.lat, startPos.lng, initialBearing, 35, true);
            announceStepIfNeeded(startPos.lat, startPos.lng);

            function animateFrame(timestamp) {
                if (!isDemoDriving) return;

                if (segIdx >= pathPoints.length - 1) {
                    const finalPos = pathPoints[pathPoints.length - 1];
                    updateArrowPosition(finalPos.lat, finalPos.lng, 0, 0, true);
                    if (stepTitle) stepTitle.textContent = '🎉 Demo Navigation Completed! Destination Reached.';
                    if (stepDistance) stepDistance.textContent = 'DEMO ROUTE COMPLETE';
                    speakInstruction('Demo navigation complete. You have reached your destination.');
                    stopDemoDrive();
                    return;
                }

                const startPt = pathPoints[segIdx];
                const endPt = pathPoints[segIdx + 1];
                const segDist = calculateDistanceMeters(startPt.lat, startPt.lng, endPt.lat, endPt.lng);

                // Minimum 250ms per road segment
                const segDurationMs = Math.max(250, (segDist / SIMULATED_SPEED_MPS) * 1000);

                if (!segStartTime) segStartTime = timestamp;
                const elapsed = timestamp - segStartTime;
                const progress = Math.min(1, elapsed / segDurationMs);

                const currentLat = startPt.lat + (endPt.lat - startPt.lat) * progress;
                const currentLng = startPt.lng + (endPt.lng - startPt.lng) * progress;
                const bearing = calculateBearing(startPt.lat, startPt.lng, endPt.lat, endPt.lng);

                // Throttle map panning to every 1200ms to prevent map rendering lag
                const shouldPan = (timestamp - lastPanTime > 1200);
                if (shouldPan) lastPanTime = timestamp;

                updateArrowPosition(currentLat, currentLng, bearing, 35, shouldPan);
                announceStepIfNeeded(currentLat, currentLng);

                if (progress >= 1) {
                    segIdx++;
                    segStartTime = timestamp;
                }

                demoAnimationReq = requestAnimationFrame(animateFrame);
            }

            demoAnimationReq = requestAnimationFrame(animateFrame);
        }

        if (btnDemoNav) {
            btnDemoNav.addEventListener('click', () => {
                if (!isDemoDriving) {
                    startDemoDrive();
                } else {
                    stopDemoDrive();
                }
            });
        }

        if (btnGmapsStart) {
            btnGmapsStart.addEventListener('click', async () => {
                if (!isDriving) {
                    if (isDemoDriving) {
                        stopDemoDrive();
                    }

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
                    if (gmapsBtnLabel) gmapsBtnLabel.textContent = 'Pause Live GPS';
                    startRealTimeLocationNavigation();
                } else {
                    isDriving = false;
                    if (gmapsBtnLabel) gmapsBtnLabel.textContent = 'Start Live GPS';
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

        let currentNavStepIdx = 0;

        function updateRealTimeTurnInstructions(userLat, userLng) {
            if (!validPoints || validPoints.length === 0) return;

            const targetPt = validPoints[currentNavStepIdx];
            if (!targetPt) return;

            const distToTarget = calculateDistanceMeters(userLat, userLng, targetPt.lat, targetPt.lng);
            const distFormatted = distToTarget > 1000 
                                ? (distToTarget / 1000).toFixed(1) + ' km' 
                                : Math.round(distToTarget) + ' meters';

            if (stepDistance) {
                stepDistance.textContent = `IN ${distFormatted.toUpperCase()} • STEP ${currentNavStepIdx + 1} OF ${validPoints.length}`;
            }

            const instructionText = targetPt.instruction || `Proceed along route to Waypoint ${currentNavStepIdx + 1}`;
            if (stepTitle && stepTitle.textContent !== instructionText) {
                stepTitle.textContent = instructionText;
                speakInstruction(`In ${distFormatted}, ${instructionText}`);
            }

            // Arrived at current waypoint -> advance to next turn instruction!
            if (distToTarget <= 30 && currentNavStepIdx < validPoints.length - 1) {
                currentNavStepIdx++;
                const nextPt = validPoints[currentNavStepIdx];
                const nextInstruction = nextPt.instruction || `Waypoint ${currentNavStepIdx + 1}`;
                if (stepTitle) stepTitle.textContent = nextInstruction;
                speakInstruction(nextInstruction);
            }
        }

        function handleUserLocationUpdate(userLat, userLng, heading, speed) {
            if (!startPoint) return;

            // Snap live location to active road path line if driver is within 35m
            const snappedPos = snapToRoadPath(userLat, userLng);
            const activeLat = snappedPos.lat;
            const activeLng = snappedPos.lng;

            const distToStart = calculateDistanceMeters(activeLat, activeLng, startPoint.lat, startPoint.lng);

            if (distToStart > 60 && !isAtStartPoint) {
                const roundedDist = Math.round(distToStart);
                if (stepDistance) stepDistance.textContent = `HEAD TO START POINT (${roundedDist > 1000 ? (roundedDist / 1000).toFixed(1) + ' km' : roundedDist + ' m'})`;
                if (stepTitle) stepTitle.textContent = `📍 Drive to ${startPoint.instruction || 'Start Point'}`;

                speakInstruction(`Please drive to the start location: ${startPoint.instruction || 'Test Center'}`);
                updateArrowPosition(activeLat, activeLng, heading, speed);
                return;
            }

            if (!isAtStartPoint) {
                isAtStartPoint = true;
                currentNavStepIdx = 0;
                speakInstruction('Arrived at start location. Driving test practice starting now.');
            }

            // Dynamically update turn-by-turn instructions as vehicle moves along map
            updateRealTimeTurnInstructions(activeLat, activeLng);
            updateArrowPosition(activeLat, activeLng, heading, speed);
        }

        function startSimulatedDrive() {
            if (validPoints.length < 2) return;

            let currentPointIdx = 0;
            if (simulationInterval) clearInterval(simulationInterval);

            simulationInterval = setInterval(() => {
                if (!isDriving) return;

                const pt = validPoints[currentPointIdx];
                if (pt && pt.lat !== null && pt.lng !== null) {
                    if (stepDistance) stepDistance.textContent = `IN 50 METERS • STEP ${currentPointIdx + 1} OF ${validPoints.length}`;
                    if (stepTitle) stepTitle.textContent = pt.instruction;

                    speakInstruction(pt.instruction);
                    updateArrowPosition(pt.lat, pt.lng, (currentPointIdx * 45) % 360, 35);

                    currentPointIdx = (currentPointIdx + 1) % validPoints.length;
                }
            }, 5000);
        }

        function updateArrowPosition(lat, lng, heading = 0, speed = 0, doPan = true) {
            const activeHeading = (heading !== undefined && heading !== null && !isNaN(heading)) 
                                ? heading 
                                : deviceCompassHeading;

            if (map) {
                if (typeof google !== 'undefined' && google.maps && map instanceof google.maps.Map) {
                    if (doPan) {
                        map.panTo({ lat: lat, lng: lng });
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
                    if (doPan) map.setView([lat, lng], 17);
                }
            }
        }
    });
</script>
@endpush
