@extends('layouts.app')

@section('title', $route->title)

@push('styles')
    <style>
        .route-detail-page {
            background-color: #f8f9fa;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .09), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .07), transparent 30%),
                linear-gradient(180deg, rgba(248, 249, 250, .9), rgba(241, 243, 245, .94) 48%, rgba(248, 249, 250, .96)),
                var(--public-image-route);
            background-position: center, center, center, center top;
            background-repeat: no-repeat;
            background-size: auto, auto, auto, cover;
            color: #212529;
        }

        .route-detail-glass {
            border: 1px solid rgba(203, 213, 225, .9);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
        }

        .route-detail-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #1e40af 0%, #2563eb 44%, #0891b2 100%);
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
            background: linear-gradient(135deg, #1e40af, #2563eb 52%, #0891b2);
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
        }

        .route-detail-button-secondary {
            border: 1px solid rgba(37, 99, 235, .24);
            color: #1d4ed8;
            background: #ffffff;
        }

        .route-detail-page #active-instruction {
            border-color: #e0e0e0 !important;
            background: #f8f9fa !important;
        }

        .route-detail-page #active-instruction-title,
        .route-detail-page #directions-list p,
        .route-detail-page #directions-list div {
            color: inherit;
        }

        .route-detail-map-placeholder {
            background-color: #f1f3f5;
            background-image:
                linear-gradient(135deg, rgba(248, 249, 250, .64), rgba(255, 255, 255, .34), rgba(241, 243, 245, .7)),
                var(--public-image-route);
            background-position: center, center;
            background-repeat: no-repeat;
            background-size: auto, cover;
        }

        /* --- Navigation HUD Custom Styles --- */
        #nav-hud-overlay * {
            text-shadow: none;
        }
        .text-teal-150 {
            color: #b2dfdb;
        }
        .hud-slide-down {
            transform: translateY(0) !important;
            opacity: 1 !important;
        }
        .hud-slide-up {
            transform: translateY(0) !important;
            opacity: 1 !important;
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

        $mappedPoints = $points->map(function($p) {
            return [
                'lat' => $p->lat === null ? null : (float) $p->lat,
                'lng' => $p->lng === null ? null : (float) $p->lng,
                'instruction' => $p->instruction,
                'maneuver' => $p->maneuver,
                'distance_km' => $p->distance_km === null ? null : (float) $p->distance_km,
                'duration' => $p->duration,
                'sort_order' => $p->sort_order,
            ];
        });
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
                <div class="route-detail-glass overflow-hidden relative">
                @if(config('services.google.maps_key') && $hasRouteStops)
                    <!-- Navigation HUD Overlays -->
                    <!-- Navigation HUD Overlays -->
                    <div id="nav-hud-overlay" class="absolute inset-0 pointer-events-none z-10 flex flex-col justify-between p-4">
                        <!-- Preview Mode Search Header -->
                        <div id="preview-search-header" class="w-full max-w-md mx-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/50 p-4 pointer-events-auto transition-all duration-350 transform translate-y-0 opacity-100 flex flex-col gap-2 shrink-0">
                            <div class="flex items-center gap-3.5">
                                <div class="flex flex-col items-center gap-1.5 pt-1 shrink-0">
                                    <span class="inline-block h-3 w-3 rounded-full border-2 border-white bg-blue-600 shadow-sm ring-4 ring-blue-100"></span>
                                    <span class="w-0.5 h-6 border-l border-dashed border-slate-300"></span>
                                    <svg class="h-4.5 w-4.5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 flex flex-col gap-2 min-w-0">
                                    <div class="border-b border-slate-100 pb-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Start location</span>
                                        <span id="preview-start-loc-label" class="font-bold text-sm text-slate-800 block truncate">Your Location</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Destination</span>
                                        <span class="font-bold text-sm text-slate-800 block truncate">{{ $route->destination_label ?: 'Midpoint' }}</span>
                                    </div>
                                </div>
                                <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-slate-700 transition active:scale-95">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Top Instruction Banner -->
                        <div id="hud-top-banner" class="w-full flex flex-col gap-2 pointer-events-auto transform -translate-y-24 opacity-0 transition-all duration-500 ease-out">
                            <div class="flex items-center gap-4 rounded-2xl bg-[#005c53] px-5 py-4 shadow-2xl border border-[#004841] text-white">
                                <!-- Action Direction Icon -->
                                <div id="hud-maneuver-icon" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/20 text-white">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                    </svg>
                                </div>
                                <!-- Text Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 id="hud-main-instruction" class="text-lg sm:text-xl font-black leading-tight truncate">Head north</h3>
                                    <p id="hud-sub-instruction" class="text-xs sm:text-sm font-bold text-teal-200 leading-tight mt-0.5 truncate">Ottawa Street North</p>
                                </div>
                                <!-- Mic / Audio Indicator -->
                                <button id="hud-mic-btn" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition active:scale-95">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/>
                                        <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- Next instruction hint -->
                            <div id="hud-next-step" class="self-start flex items-center gap-2 rounded-xl bg-[#004841]/95 px-3 py-1.5 border border-[#003933] shadow-lg backdrop-blur-md">
                                <span class="text-[10px] font-black uppercase tracking-wider text-teal-300">Then</span>
                                <span id="hud-next-step-text" class="text-xs font-bold text-white truncate max-w-[200px]">Turn right</span>
                            </div>
                        </div>

                        <!-- Side Floaters Column -->
                        <div class="absolute right-4 top-[100px] flex flex-col gap-3 pointer-events-auto">
                            <!-- Compass -->
                            <button id="btn-hud-compass" class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-700 shadow-xl border border-slate-100/80 hover:bg-slate-50 transition active:scale-90" title="Reset Map Angle">
                                <svg class="h-6 w-6 text-red-500 transform transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z" />
                                </svg>
                            </button>
                            <!-- Search -->
                            <button class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-700 shadow-xl border border-slate-100/80 hover:bg-slate-50 transition active:scale-90">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            <!-- Audio Speaker Toggle -->
                            <button id="btn-hud-audio" class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-700 shadow-xl border border-slate-100/80 hover:bg-slate-50 transition active:scale-90" title="Toggle Voice Guidance">
                                <svg id="hud-audio-svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                </svg>
                            </button>
                            <!-- Report Button -->
                            <button id="btn-hud-report" class="flex items-center gap-2 rounded-full bg-white px-4 py-3 text-slate-700 shadow-xl border border-slate-100/80 hover:bg-slate-50 transition active:scale-90">
                                <svg class="h-5 w-5 text-amber-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="text-sm font-black text-slate-800">Report</span>
                            </button>
                        </div>

                        <!-- Speedometer bottom left -->
                        <div class="absolute left-4 bottom-[100px] pointer-events-auto">
                            <div class="flex h-16 w-16 flex-col items-center justify-center rounded-full border-4 border-slate-100/90 bg-white shadow-2xl">
                                <span id="hud-speed-val" class="text-lg font-black text-slate-900 leading-none">--</span>
                                <span class="text-[9px] font-extrabold text-slate-400 mt-0.5">km/h</span>
                            </div>
                        </div>

                        <!-- Bottom HUD Panel (Navigation mode) -->
                        <div id="hud-bottom-sheet" class="w-full bg-white rounded-3xl p-5 shadow-2xl border border-slate-100 pointer-events-auto mt-auto transform translate-y-36 opacity-0 transition-all duration-500 ease-out hidden">
                            <div class="flex items-center justify-between gap-4">
                                <!-- Exit Drive Button -->
                                <button type="button" id="btn-hud-exit" class="flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition active:scale-90" title="Exit Navigation">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <!-- Stats Details -->
                                <div class="text-center">
                                    <div class="flex items-baseline justify-center gap-0.5">
                                        <span id="hud-duration-val" class="text-3xl font-black text-orange-600">--</span>
                                        <span class="text-lg font-extrabold text-orange-600">min</span>
                                    </div>
                                    <div class="mt-1 text-xs sm:text-sm font-black text-slate-500 flex items-center justify-center gap-2">
                                        <span id="hud-distance-val">-- km</span>
                                        <span class="text-slate-300">•</span>
                                        <span id="hud-eta-val">--:--</span>
                                    </div>
                                </div>
                                <!-- Directions drawer toggle -->
                                <button type="button" id="btn-hud-list" class="flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition active:scale-90" title="Toggle Directions List">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Preview Mode Bottom Sheet -->
                        <div id="preview-bottom-sheet" class="w-full bg-white rounded-3xl p-5 shadow-2xl border border-slate-150 pointer-events-auto mt-auto transform translate-y-0 opacity-100 transition-all duration-350 flex flex-col gap-4">
                            <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto shrink-0"></div>
                            
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                                        <span>Drive</span>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100 shrink-0">Best Route</span>
                                    </h3>
                                    <div class="mt-1 flex items-baseline gap-1.5">
                                        <span id="preview-duration" class="text-2xl font-black text-emerald-600">-- min</span>
                                        <span id="preview-distance" class="text-sm font-extrabold text-slate-500">(-- km)</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-500 mt-1">Fastest route, standard traffic</p>
                                </div>
                                <!-- Voice guidance toggle in preview -->
                                <button type="button" id="btn-preview-audio" class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 hover:text-slate-800 transition active:scale-95" title="Toggle Voice Guidance">
                                    <svg id="preview-audio-svg" class="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" id="btn-preview-start" class="flex-1 flex items-center justify-center gap-2 rounded-2xl bg-teal-800 hover:bg-teal-900 py-3.5 font-bold text-white shadow-lg transition active:scale-97">
                                    <svg class="h-5 w-5 transform rotate-45" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z" />
                                    </svg>
                                    <span id="btn-preview-start-text">Start drive</span>
                                </button>

                                <button type="button" id="btn-preview-steps" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition active:scale-95" title="Toggle Directions List">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="map" class="h-[70vh] min-h-[500px] w-full z-0"></div>

                    <!-- GPS Simulation Dialog -->
                    <div id="sim-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 opacity-0 pointer-events-none transition-opacity duration-300 backdrop-blur-sm">
                        <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 transform scale-95 transition-transform duration-300">
                            <h3 class="text-lg font-black text-slate-900">Start Navigation</h3>
                            <p class="mt-2 text-sm text-slate-500 leading-relaxed">You are currently away from the start point of this driving route. Would you like to simulate the drive as a demo, or start active GPS navigation anyway?</p>
                            
                            <div class="mt-6 flex flex-col gap-2.5">
                                <button type="button" id="btn-sim-demo" class="w-full rounded-2xl bg-teal-800 hover:bg-teal-900 py-3 font-bold text-white shadow-lg transition active:scale-97">
                                    Simulate Drive (Demo)
                                </button>
                                <button type="button" id="btn-sim-gps" class="w-full rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 py-3 font-bold text-slate-700 transition active:scale-97">
                                    Start GPS Navigation Anyway
                                </button>
                                <button type="button" id="btn-sim-cancel" class="w-full rounded-2xl py-2 font-bold text-slate-400 hover:text-slate-600 transition">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Legacy Active Instruction (will hide when HUD is active) -->
                    <div id="active-instruction" class="border-t border-slate-200 px-5 py-4 bg-white/95 hidden">
                        <div class="text-xs font-black uppercase text-slate-500">Drive Guidance</div>
                        <div id="active-instruction-title" class="mt-1 text-xl font-black text-slate-900">Route loading...</div>
                        <div id="active-instruction-detail" class="mt-1 text-sm text-slate-600">Google will calculate the best path from start to midpoint and back to start.</div>
                    </div>
                @elseif(! $hasRouteStops)
                    <div class="route-detail-map-placeholder grid h-[70vh] min-h-[500px] place-items-center p-6 text-center">
                        <div>
                            <h2 class="text-lg font-black text-white">Start and midpoint needed</h2>
                            <p class="mt-2 max-w-md text-sm text-slate-400">Add a start point and midpoint/end point in the admin panel. Google will calculate the return route automatically.</p>
                        </div>
                    </div>
                @else
                    <div class="route-detail-map-placeholder relative grid h-[70vh] min-h-[500px] place-items-center overflow-hidden p-6 text-center">
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

            const manualPoints = @json($mappedPoints);

            const pointsWithCoords = manualPoints.filter(p => p.lat !== null && p.lng !== null && Number.isFinite(p.lat) && Number.isFinite(p.lng));

            // Chunk points into segments of at most 20 points
            const segments = [];
            const chunkSize = 20;
            for (let i = 0; i < pointsWithCoords.length - 1; i += chunkSize - 1) {
                const chunk = pointsWithCoords.slice(i, i + chunkSize);
                if (chunk.length >= 2) {
                    segments.push(chunk);
                }
            }

            // Fill in missing coordinates in manualPoints for navigation/list safety
            let lastValidCoord = { lat: routeData.start.lat, lng: routeData.start.lng };
            for (const p of pointsWithCoords) {
                lastValidCoord = { lat: p.lat, lng: p.lng };
                break;
            }
            manualPoints.forEach(p => {
                if (p.lat === null || p.lng === null || !Number.isFinite(p.lat) || !Number.isFinite(p.lng)) {
                    p.lat = lastValidCoord.lat;
                    p.lng = lastValidCoord.lng;
                } else {
                    lastValidCoord = { lat: p.lat, lng: p.lng };
                }
            });

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
            let accessConsumedForCurrentDrive = true;
            const startDistanceThresholdMeters = 60;
            let routePathPoints = [];
            let simIntervalId = null;
            let simIndex = 0;
            let simulatedSpeed = 40;

            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: initialCenter(),
                    zoom: 14,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                    rotateControl: true,
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
                console.log("resolveStopsAndCalculateRoute: manual points count:", pointsWithCoords.length);
                if (pointsWithCoords.length >= 2) {
                    console.log("resolveStopsAndCalculateRoute: Using manual route points:", pointsWithCoords);
                    calculateManualRoute();
                    return;
                }

                setActiveInstruction('Finding route stops...', 'Google is locating the start and midpoint.');
                console.log("resolveStopsAndCalculateRoute: Resolving start stop:", routeData.start);

                try {
                    resolvedStartLocation = await resolveStop(routeData.start);
                    console.log("resolveStopsAndCalculateRoute: Resolved start location successfully:", resolvedStartLocation);
                    console.log("resolveStopsAndCalculateRoute: Resolving midpoint stop:", routeData.midpoint);
                    resolvedMidpointLocation = await resolveStop(routeData.midpoint);
                    console.log("resolveStopsAndCalculateRoute: Resolved midpoint location successfully:", resolvedMidpointLocation);
                    calculateRoundTrip();
                } catch (status) {
                    console.error("resolveStopsAndCalculateRoute caught error status:", status);
                    showRouteError(status, 'Google could not find one of these route stops. Use a real place name like "Star Gate Karachi" or add latitude/longitude in admin.');
                }
            }

            function calculateRoundTrip() {
                console.log("calculateRoundTrip: Starting route calculation between:", resolvedStartLocation, "and", resolvedMidpointLocation);
                setActiveInstruction('Calculating route...', 'Google is choosing the best driving path.');

                const directionsService = new google.maps.DirectionsService();

                Promise.all([
                    requestDirections(directionsService, resolvedStartLocation, resolvedMidpointLocation),
                    requestDirections(directionsService, resolvedMidpointLocation, resolvedStartLocation),
                ])
                    .then(results => {
                        console.log("calculateRoundTrip: Both direction results received successfully", results);
                        renderDirections(results);
                    })
                    .catch((status) => {
                        console.error("calculateRoundTrip Directions Service failed, falling back to straight-line route.", status);
                        renderFallbackRoundTripRoute();
                    });
            }

            async function calculateManualRoute() {
                setActiveInstruction('Calculating route...', 'Google is choosing the best driving path.');

                const directionsService = new google.maps.DirectionsService();

                const promises = segments.map(segment => {
                    const origin = { lat: segment[0].lat, lng: segment[0].lng };
                    const destination = { lat: segment[segment.length - 1].lat, lng: segment[segment.length - 1].lng };
                    const waypoints = segment.slice(1, -1).map(p => ({
                        location: { lat: p.lat, lng: p.lng },
                        stopover: false
                    }));

                    return requestDirections(directionsService, origin, destination, waypoints);
                });

                try {
                    const results = await Promise.all(promises);
                    renderManualDirections(results);
                } catch (status) {
                    console.error("calculateManualRoute: Directions service failed, falling back to database points path.", status);
                    renderFallbackManualRoute();
                }
            }

            function requestDirections(directionsService, origin, destination, waypoints = []) {
                return new Promise((resolve, reject) => {
                    directionsService.route({
                        origin,
                        destination,
                        waypoints,
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
                    console.log("resolveStop: stop has coordinates in DB:", stop);
                    return Promise.resolve(toPosition(stop));
                }

                return new Promise((resolve, reject) => {
                    const geocoder = new google.maps.Geocoder();
                    const queryStr = stop.query || stop.label;
                    console.log("resolveStop: Geocoding address query:", queryStr);
                    geocoder.geocode({
                        address: queryStr,
                        componentRestrictions: (routeData.start.query && routeData.start.query.includes('Karachi')) || (routeData.midpoint.query && routeData.midpoint.query.includes('Karachi'))
                            ? { country: 'PK' }
                            : undefined,
                    }, (results, status) => {
                        console.log("resolveStop: Geocoder status response:", status, "results:", results);
                        if (status === 'OK' && results?.[0]) {
                            const pos = latLngToPosition(results[0].geometry.location);
                            console.log("resolveStop: Geocoded successfully to coords:", pos);
                            resolve(pos);
                            return;
                        }

                        reject(status);
                    });
                });
            }

            function showRouteError(status, message) {
                console.error("showRouteError: status =", status, "message =", message);
                const help = {
                    REQUEST_DENIED: 'Google rejected the request. Enable Maps JavaScript API, Directions API, Geocoding API, and billing for this key.',
                    ZERO_RESULTS: 'Google found no drivable route between these stops. Use clearer places or coordinates.',
                    NOT_FOUND: 'Google could not find the start or midpoint place name.',
                    OVER_QUERY_LIMIT: 'Google quota is exceeded for this API key.',
                }[status] ?? message;

                setActiveInstruction('Route not available', `${help} Status: ${status}`);
                const listEl = document.getElementById('directions-list');
                if (listEl) {
                    listEl.innerHTML = `<li class="p-5 text-sm text-red-700">${help}<br><span class="mt-2 block text-xs text-red-500">Google status: ${status}</span></li>`;
                } else {
                    console.error("showRouteError: directions-list element not found in DOM.");
                }
                if (routeStatus) {
                    routeStatus.textContent = `Route failed: ${status}`;
                } else {
                    console.warn("showRouteError: routeStatus is not initialized/found.");
                }
            }

            function renderDirections(results) {
                const bounds = new google.maps.LatLngBounds();

                routePathPoints = [];
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

                    result.routes[0].overview_path.forEach((point) => {
                        bounds.extend(point);
                        routePathPoints.push(latLngToPosition(point));
                    });
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

                // Calculate total duration and distance
                let totalRouteDuration = 0;
                let totalRouteDistance = 0;
                results.forEach((res) => {
                    const leg = res.routes?.[0]?.legs?.[0];
                    if (leg) {
                        totalRouteDuration += leg.duration?.value ?? 0;
                        totalRouteDistance += leg.distance?.value ?? 0;
                    }
                });

                // Update bottom sheet values initially
                const durationVal = Math.max(1, Math.round(totalRouteDuration / 60));
                document.getElementById('hud-duration-val').textContent = durationVal;
                document.getElementById('hud-distance-val').textContent = (totalRouteDistance / 1000).toFixed(1) + ' km';
                updateETA(totalRouteDuration);

                // Update preview sheet values
                const previewDur = document.getElementById('preview-duration');
                const previewDist = document.getElementById('preview-distance');
                if (previewDur) previewDur.textContent = durationVal + ' min';
                if (previewDist) previewDist.textContent = `(${(totalRouteDistance / 1000).toFixed(1)} km)`;

                setActiveInstruction('Route ready', 'Use location, go to the start point, then start the drive.');
            }

            function renderFallbackRoundTripRoute() {
                console.log("renderFallbackRoundTripRoute: Rendering straight line round trip.");
                const bounds = new google.maps.LatLngBounds();
                routePathPoints = [];

                const points = [resolvedStartLocation, resolvedMidpointLocation, resolvedStartLocation];
                points.forEach(pos => {
                    bounds.extend(pos);
                    routePathPoints.push(pos);
                });

                const fallbackPath = new google.maps.Polyline({
                    path: routePathPoints,
                    geodesic: true,
                    strokeColor: '#047857',
                    strokeOpacity: 0.85,
                    strokeWeight: 6,
                    map: map
                });

                routeStartPosition = resolvedStartLocation;
                routeMidpointPosition = resolvedMidpointLocation;

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

                // Set up basic 2-step navigation directions
                const distToMidpoint = distanceMeters(resolvedStartLocation, resolvedMidpointLocation);
                directionSteps = [
                    {
                        legIndex: 0,
                        html: `Drive from start to midpoint: ${routeData.midpoint.label}`,
                        text: `Drive from start to midpoint: ${routeData.midpoint.label}`,
                        distanceText: formatDistance(distToMidpoint),
                        distanceMeters: distToMidpoint,
                        durationText: '',
                        start: resolvedStartLocation,
                        end: resolvedMidpointLocation,
                    },
                    {
                        legIndex: 1,
                        html: `Return to start: ${routeData.start.label}`,
                        text: `Return to start: ${routeData.start.label}`,
                        distanceText: formatDistance(distToMidpoint),
                        distanceMeters: distToMidpoint,
                        durationText: '',
                        start: resolvedMidpointLocation,
                        end: resolvedStartLocation,
                    }
                ];

                renderDirectionList(directionSteps);
                initializeVehicle();
                map.fitBounds(bounds, 72);

                const totalRouteDistance = distToMidpoint * 2;
                const totalRouteDuration = Math.round((totalRouteDistance / 1000) * 90); // 90 seconds per km

                const durationVal = Math.max(1, Math.round(totalRouteDuration / 60));
                document.getElementById('hud-duration-val').textContent = durationVal;
                document.getElementById('hud-distance-val').textContent = (totalRouteDistance / 1000).toFixed(1) + ' km';
                updateETA(totalRouteDuration);

                // Update preview sheet values
                const previewDur = document.getElementById('preview-duration');
                const previewDist = document.getElementById('preview-distance');
                if (previewDur) previewDur.textContent = durationVal + ' min';
                if (previewDist) previewDist.textContent = `(${(totalRouteDistance / 1000).toFixed(1)} km)`;

                setActiveInstruction('Route ready (fallback path)', 'Google Directions Service failed; showing straight path.');
            }

            function renderFallbackManualRoute() {
                console.log("renderFallbackManualRoute: Rendering direct manual points path.");
                const bounds = new google.maps.LatLngBounds();
                routePathPoints = [];

                // Connect coordinates with a solid polyline
                const pathCoordinates = pointsWithCoords.map(p => {
                    const pos = { lat: p.lat, lng: p.lng };
                    bounds.extend(pos);
                    routePathPoints.push(pos);
                    return pos;
                });

                const fallbackPath = new google.maps.Polyline({
                    path: pathCoordinates,
                    geodesic: true,
                    strokeColor: '#047857',
                    strokeOpacity: 0.85,
                    strokeWeight: 6,
                    map: map
                });

                // Set positions
                routeStartPosition = { lat: pointsWithCoords[0].lat, lng: pointsWithCoords[0].lng };
                
                let midpointIndex = 0;
                if (hasCoordinates(routeData.midpoint)) {
                    let minDistance = Infinity;
                    pointsWithCoords.forEach((p, idx) => {
                        const dist = distanceMeters({ lat: p.lat, lng: p.lng }, toPosition(routeData.midpoint));
                        if (dist < minDistance) {
                            minDistance = dist;
                            midpointIndex = idx;
                        }
                    });
                } else {
                    midpointIndex = Math.floor(pointsWithCoords.length / 2);
                }
                routeMidpointPosition = { lat: pointsWithCoords[midpointIndex].lat, lng: pointsWithCoords[midpointIndex].lng };

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

                // Populate directionSteps from manual points
                directionSteps = manualPoints.map((p, index) => {
                    const nextPoint = manualPoints[index + 1] || null;
                    const startPos = { lat: p.lat, lng: p.lng };
                    const endPos = nextPoint ? { lat: nextPoint.lat, lng: nextPoint.lng } : startPos;

                    let distMeters = 0;
                    if (p.distance_km !== null) {
                        distMeters = p.distance_km * 1000;
                    } else if (nextPoint && Number.isFinite(p.lat) && Number.isFinite(p.lng) && Number.isFinite(nextPoint.lat) && Number.isFinite(nextPoint.lng)) {
                        distMeters = distanceMeters(startPos, endPos);
                    }

                    const distText = p.distance_km !== null 
                        ? (p.distance_km < 1 ? `${Math.round(p.distance_km * 1000)} m` : `${p.distance_km.toFixed(1)} km`)
                        : (distMeters > 0 ? formatDistance(distMeters) : '');

                    return {
                        legIndex: index <= midpointIndex ? 0 : 1,
                        html: p.instruction,
                        text: stripHtml(p.instruction),
                        distanceText: distText,
                        distanceMeters: distMeters,
                        durationText: p.duration || '',
                        start: startPos,
                        end: endPos,
                    };
                });

                renderDirectionList(directionSteps);
                initializeVehicle();
                map.fitBounds(bounds, 72);

                // Calculate total duration and distance
                let totalRouteDistance = 0;
                let totalRouteDuration = 0;
                
                manualPoints.forEach(p => {
                    if (p.distance_km !== null) {
                        totalRouteDistance += p.distance_km * 1000;
                    }
                    if (p.duration) {
                        const parsedDuration = parseInt(p.duration);
                        if (!isNaN(parsedDuration)) {
                            totalRouteDuration += parsedDuration * 60;
                        }
                    }
                });

                if (totalRouteDistance === 0 && routePathPoints.length >= 2) {
                    for (let i = 0; i < routePathPoints.length - 1; i++) {
                        totalRouteDistance += distanceMeters(routePathPoints[i], routePathPoints[i+1]);
                    }
                }
                if (totalRouteDuration === 0) {
                    // Average driving speed fallback (~40 km/h)
                    totalRouteDuration = Math.round((totalRouteDistance / 1000) * 90); // 90 seconds per km
                }

                const durationVal = Math.max(1, Math.round(totalRouteDuration / 60));
                document.getElementById('hud-duration-val').textContent = durationVal;
                document.getElementById('hud-distance-val').textContent = (totalRouteDistance / 1000).toFixed(1) + ' km';
                updateETA(totalRouteDuration);

                // Update preview sheet values
                const previewDur = document.getElementById('preview-duration');
                const previewDist = document.getElementById('preview-distance');
                if (previewDur) previewDur.textContent = durationVal + ' min';
                if (previewDist) previewDist.textContent = `(${(totalRouteDistance / 1000).toFixed(1)} km)`;

                setActiveInstruction('Route ready (fallback path)', 'GPS route calculated from database points.');
            }

            function renderManualDirections(results) {
                const bounds = new google.maps.LatLngBounds();

                routePathPoints = [];
                results.forEach((result) => {
                    const renderer = new google.maps.DirectionsRenderer({
                        map,
                        suppressMarkers: true,
                        preserveViewport: true,
                        polylineOptions: {
                            strokeColor: '#047857',
                            strokeOpacity: 0.95,
                            strokeWeight: 7,
                        },
                    });
                    renderer.setDirections(result);

                    result.routes[0].overview_path.forEach((point) => {
                        bounds.extend(point);
                        routePathPoints.push(latLngToPosition(point));
                    });
                });

                // Set positions
                routeStartPosition = { lat: pointsWithCoords[0].lat, lng: pointsWithCoords[0].lng };
                
                let midpointIndex = 0;
                if (hasCoordinates(routeData.midpoint)) {
                    let minDistance = Infinity;
                    pointsWithCoords.forEach((p, idx) => {
                        const dist = distanceMeters({ lat: p.lat, lng: p.lng }, toPosition(routeData.midpoint));
                        if (dist < minDistance) {
                            minDistance = dist;
                            midpointIndex = idx;
                        }
                    });
                } else {
                    midpointIndex = Math.floor(pointsWithCoords.length / 2);
                }
                routeMidpointPosition = { lat: pointsWithCoords[midpointIndex].lat, lng: pointsWithCoords[midpointIndex].lng };

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

                // Populate directionSteps from manual points
                directionSteps = manualPoints.map((p, index) => {
                    const nextPoint = manualPoints[index + 1] || null;
                    const startPos = { lat: p.lat, lng: p.lng };
                    const endPos = nextPoint ? { lat: nextPoint.lat, lng: nextPoint.lng } : startPos;

                    let distMeters = 0;
                    if (p.distance_km !== null) {
                        distMeters = p.distance_km * 1000;
                    } else if (nextPoint && Number.isFinite(p.lat) && Number.isFinite(p.lng) && Number.isFinite(nextPoint.lat) && Number.isFinite(nextPoint.lng)) {
                        distMeters = distanceMeters(startPos, endPos);
                    }

                    const distText = p.distance_km !== null 
                        ? (p.distance_km < 1 ? `${Math.round(p.distance_km * 1000)} m` : `${p.distance_km.toFixed(1)} km`)
                        : (distMeters > 0 ? formatDistance(distMeters) : '');

                    return {
                        legIndex: index <= midpointIndex ? 0 : 1,
                        html: p.instruction,
                        text: stripHtml(p.instruction),
                        distanceText: distText,
                        distanceMeters: distMeters,
                        durationText: p.duration || '',
                        start: startPos,
                        end: endPos,
                    };
                });

                renderDirectionList(directionSteps);
                initializeVehicle();
                map.fitBounds(bounds, 72);

                // Calculate total duration and distance
                let totalRouteDuration = 0;
                let totalRouteDistance = 0;
                
                manualPoints.forEach(p => {
                    if (p.distance_km !== null) {
                        totalRouteDistance += p.distance_km * 1000;
                    }
                    if (p.duration) {
                        const parsedDuration = parseInt(p.duration);
                        if (!isNaN(parsedDuration)) {
                            totalRouteDuration += parsedDuration * 60;
                        }
                    }
                });

                if (totalRouteDistance === 0) {
                    results.forEach((res) => {
                        const leg = res.routes?.[0]?.legs?.[0];
                        if (leg) {
                            totalRouteDistance += leg.distance?.value ?? 0;
                        }
                    });
                }
                if (totalRouteDuration === 0) {
                    results.forEach((res) => {
                        const leg = res.routes?.[0]?.legs?.[0];
                        if (leg) {
                            totalRouteDuration += leg.duration?.value ?? 0;
                        }
                    });
                }

                const durationVal = Math.max(1, Math.round(totalRouteDuration / 60));
                document.getElementById('hud-duration-val').textContent = durationVal;
                document.getElementById('hud-distance-val').textContent = (totalRouteDistance / 1000).toFixed(1) + ' km';
                updateETA(totalRouteDuration);

                // Update preview sheet values
                const previewDur = document.getElementById('preview-duration');
                const previewDist = document.getElementById('preview-distance');
                if (previewDur) previewDur.textContent = durationVal + ' min';
                if (previewDist) previewDist.textContent = `(${(totalRouteDistance / 1000).toFixed(1)} km)`;

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

            let deviceHeading = null;

            if (window.DeviceOrientationEvent) {
                window.addEventListener('deviceorientation', (event) => {
                    if (event.webkitCompassHeading) {
                        deviceHeading = event.webkitCompassHeading;
                    } else if (event.alpha !== null && event.absolute) {
                        deviceHeading = (360 - event.alpha) % 360;
                    }
                }, true);
            }

            function addRouteControls() {
                startRouteButton = document.getElementById('btn-preview-start');
                routeStatus = document.getElementById('preview-start-loc-label');
                locateButton = null;

                beginLocationWatch();
            }

            function initializeVehicle() {
                const initialPos = latestCurrentPosition || routeStartPosition;
                if (!initialPos) {
                    return;
                }

                const heading = directionSteps.length > 0 ? bearing(directionSteps[0].start, directionSteps[0].end) ?? 0 : 0;
                vehicleMarker = new google.maps.Marker({
                    position: initialPos,
                    map,
                    title: 'Your Location Avatar',
                    icon: vehicleIcon(heading),
                    zIndex: 1000,
                });

                lastVehiclePosition = initialPos;
                lastVehicleHeading = heading;
            }

            function getClosestPointOnSegment(p, a, b) {
                const latRad = degreesToRadians((a.lat + b.lat) / 2);
                const cosLat = Math.cos(latRad);
                const dx = (b.lng - a.lng) * cosLat;
                const dy = b.lat - a.lat;
                if (dx === 0 && dy === 0) return { point: a, t: 0 };
                const px = (p.lng - a.lng) * cosLat;
                const py = p.lat - a.lat;
                let t = (px * dx + py * dy) / (dx * dx + dy * dy);
                t = Math.max(0, Math.min(1, t));
                return {
                    point: { lat: a.lat + t * dy, lng: a.lng + (t * dx) / cosLat },
                    t: t
                };
            }

            function snapToRoute(position, maxSnapDistanceMeters = 50) {
                if (!routePathPoints || routePathPoints.length < 2) {
                    return { snapped: false, position: position, distance: 0, segmentIndex: 0 };
                }
                let closestPoint = position;
                let minDistance = Infinity;
                let closestSegmentIndex = 0;
                for (let i = 0; i < routePathPoints.length - 1; i++) {
                    const res = getClosestPointOnSegment(position, routePathPoints[i], routePathPoints[i + 1]);
                    const dist = distanceMeters(position, res.point);
                    if (dist < minDistance) {
                        minDistance = dist;
                        closestPoint = res.point;
                        closestSegmentIndex = i;
                    }
                }
                if (minDistance <= maxSnapDistanceMeters) {
                    return { snapped: true, position: closestPoint, distance: minDistance, segmentIndex: closestSegmentIndex };
                }
                return { snapped: false, position: position, distance: minDistance, segmentIndex: closestSegmentIndex };
            }

            function computeEffectiveHeading(currentPos, reportedHeading) {
                if (Number.isFinite(reportedHeading) && reportedHeading !== null && reportedHeading >= 0) {
                    return reportedHeading;
                }
                if (lastVehiclePosition && distanceMeters(lastVehiclePosition, currentPos) > 1.2) {
                    const calcBearing = bearing(lastVehiclePosition, currentPos);
                    if (calcBearing !== null) return calcBearing;
                }
                if (deviceHeading !== null && Number.isFinite(deviceHeading)) {
                    return deviceHeading;
                }
                return lastVehicleHeading ?? 0;
            }

            async function startLiveRoute(force = false) {
                if (!hasReachedStart && !force) {
                    const modal = document.getElementById('sim-modal');
                    if (modal) {
                        modal.classList.remove('opacity-0', 'pointer-events-none');
                        const card = modal.querySelector('div');
                        if (card) {
                            card.classList.remove('scale-95');
                            card.classList.add('scale-100');
                        }
                    }
                    return;
                }

                if (!accessConsumedForCurrentDrive) {
                    if (startRouteButton) {
                        startRouteButton.disabled = true;
                        const btnText = document.getElementById('btn-preview-start-text');
                        if (btnText) btnText.textContent = 'Starting...';
                    }

                    const accessGranted = await consumeMapStart();

                    if (!accessGranted) {
                        return;
                    }
                }

                driveStarted = true;
                removeCurrentLocationPreview();

                const searchHeader = document.getElementById('preview-search-header');
                const previewSheet = document.getElementById('preview-bottom-sheet');
                if (searchHeader) {
                    searchHeader.classList.remove('translate-y-0', 'opacity-100');
                    searchHeader.classList.add('-translate-y-36', 'opacity-0', 'pointer-events-none');
                }
                if (previewSheet) {
                    previewSheet.classList.remove('translate-y-0', 'opacity-100');
                    previewSheet.classList.add('translate-y-36', 'opacity-0', 'pointer-events-none');
                }

                const topBanner = document.getElementById('hud-top-banner');
                const bottomSheet = document.getElementById('hud-bottom-sheet');
                if (topBanner) topBanner.classList.add('hud-slide-down');
                if (bottomSheet) {
                    bottomSheet.classList.remove('hidden');
                    bottomSheet.classList.add('hud-slide-up');
                }

                const legacyCard = document.getElementById('active-instruction');
                if (legacyCard) legacyCard.classList.add('hidden');

                if (map) {
                    map.setMapTypeId(google.maps.MapTypeId.HYBRID);
                    map.setZoom(18);
                    if (typeof map.setTilt === 'function') {
                        map.setTilt(45);
                    }
                }

                const activeTarget = latestCurrentPosition || routeStartPosition;
                if (activeTarget) {
                    moveVehicle(activeTarget);
                    map.panTo(activeTarget);
                }

                updateActiveDrivingInstruction(activeTarget);
            }

            function interpolatePath(points, stepMeters = 3) {
                if (points.length < 2) return points;
                
                const densePoints = [];
                densePoints.push(points[0]);
                
                for (let i = 0; i < points.length - 1; i++) {
                    const start = points[i];
                    const end = points[i+1];
                    const segmentDist = distanceMeters(start, end);
                    
                    if (segmentDist <= stepMeters) {
                        densePoints.push(end);
                        continue;
                    }
                    
                    const numSteps = Math.floor(segmentDist / stepMeters);
                    const latStep = (end.lat - start.lat) / numSteps;
                    const lngStep = (end.lng - start.lng) / numSteps;
                    
                    for (let j = 1; j <= numSteps; j++) {
                        densePoints.push({
                            lat: start.lat + (latStep * j),
                            lng: start.lng + (lngStep * j)
                        });
                    }
                }
                
                return densePoints;
            }

            function startSimulationDrive() {
                driveStarted = true;
                accessConsumedForCurrentDrive = true;
                
                const searchHeader = document.getElementById('preview-search-header');
                const previewSheet = document.getElementById('preview-bottom-sheet');
                if (searchHeader) {
                    searchHeader.classList.remove('translate-y-0', 'opacity-100');
                    searchHeader.classList.add('-translate-y-36', 'opacity-0', 'pointer-events-none');
                }
                if (previewSheet) {
                    previewSheet.classList.remove('translate-y-0', 'opacity-100');
                    previewSheet.classList.add('translate-y-36', 'opacity-0', 'pointer-events-none');
                }

                const topBanner = document.getElementById('hud-top-banner');
                const bottomSheet = document.getElementById('hud-bottom-sheet');
                if (topBanner) topBanner.classList.add('hud-slide-down');
                if (bottomSheet) {
                    bottomSheet.classList.remove('hidden');
                    bottomSheet.classList.add('hud-slide-up');
                }

                const legacyCard = document.getElementById('active-instruction');
                if (legacyCard) legacyCard.classList.add('hidden');

                if (map) {
                    map.setMapTypeId(google.maps.MapTypeId.HYBRID);
                    map.setZoom(18);
                    if (typeof map.setTilt === 'function') {
                        map.setTilt(45);
                    }
                }

                const densePath = interpolatePath(routePathPoints, 3);

                simIndex = 0;
                if (densePath.length > 0) {
                    moveVehicle(densePath[0], lastVehicleHeading);
                    map.panTo(densePath[0]);
                }

                updateActiveDrivingInstruction(densePath[0]);

                if (simIntervalId) {
                    clearInterval(simIntervalId);
                }

                simIntervalId = setInterval(() => {
                    if (simIndex >= densePath.length) {
                        clearInterval(simIntervalId);
                        simIntervalId = null;
                        alertToast("Destination reached!");
                        exitNavigation();
                        return;
                    }

                    const currentPos = densePath[simIndex];
                    const nextPos = densePath[simIndex + 1] || currentPos;
                    const currentHeading = bearing(currentPos, nextPos) ?? lastVehicleHeading;

                    moveVehicle(currentPos, currentHeading);
                    map.panTo(currentPos);

                    updateActiveDrivingInstruction(currentPos);

                    const speedValEl = document.getElementById('hud-speed-val');
                    if (speedValEl) {
                        speedValEl.textContent = Math.round(simulatedSpeed + (Math.random() * 4 - 2));
                    }

                    simIndex += 1;
                }, 200);
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
                    if (routeStatus) {
                        routeStatus.textContent = driveStarted
                            ? 'Drive started. Live tracking is active.'
                            : hasReachedStart
                                ? 'You are on start point of route. Start the drive.'
                                : 'Live location is already active.';
                    }
                    return;
                }

                if (!navigator.geolocation) {
                    if (routeStatus) routeStatus.textContent = 'Location is not supported by this browser.';
                    return;
                }

                if (locateButton) {
                    locateButton.disabled = true;
                    locateButton.textContent = 'Locating...';
                    locateButton.className = 'ml-2 rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-500';
                }
                if (routeStatus) routeStatus.textContent = 'Allow location permission to show your current location.';

                watchId = navigator.geolocation.watchPosition(
                    handleLocationUpdate,
                    (error) => {
                        if (locateButton) {
                            locateButton.disabled = false;
                            locateButton.textContent = 'Use location';
                            locateButton.className = 'ml-2 rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100';
                        }
                        if (routeStatus) {
                            routeStatus.textContent = error.code === error.PERMISSION_DENIED
                                ? 'Location permission was denied.'
                                : 'Could not get your live location.';
                        }
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
                const rawPosition = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                latestCurrentPosition = rawPosition;

                const speed = position.coords.speed;
                const speedValEl = document.getElementById('hud-speed-val');
                if (speedValEl) {
                    if (speed !== null && speed !== undefined && speed >= 0) {
                        speedValEl.textContent = Math.round(speed * 3.6);
                    } else if (driveStarted) {
                        const simulatedSpeed = Math.floor(Math.random() * 8) + 45;
                        speedValEl.textContent = simulatedSpeed;
                    } else {
                        speedValEl.textContent = '--';
                    }
                }

                const snapResult = snapToRoute(rawPosition, 50);
                const activePosition = snapResult.snapped ? snapResult.position : rawPosition;
                const heading = computeEffectiveHeading(activePosition, position.coords.heading);

                if (!driveStarted) {
                    moveVehicle(activePosition, heading);
                    updateCurrentLocationAccuracyCircle(rawPosition, position.coords.accuracy);
                    updateStartProximity(rawPosition, position.coords.accuracy);
                    return;
                }

                moveVehicle(activePosition, heading);
                updateActiveDrivingInstruction(activePosition);
                map.panTo(activePosition);

                if (map.getZoom() < 17) {
                    map.setZoom(18);
                }
            }

            function updateCurrentLocationAccuracyCircle(position, accuracy) {
                if (!currentAccuracyCircle) {
                    currentAccuracyCircle = new google.maps.Circle({
                        map,
                        center: position,
                        radius: Number.isFinite(accuracy) ? accuracy : 20,
                        strokeColor: '#2563eb',
                        strokeOpacity: 0.25,
                        strokeWeight: 1.5,
                        fillColor: '#2563eb',
                        fillOpacity: 0.1,
                        zIndex: 900,
                    });
                    return;
                }

                currentAccuracyCircle.setCenter(position);
                if (Number.isFinite(accuracy)) {
                    currentAccuracyCircle.setRadius(accuracy);
                }
            }

            function updateStartProximity(position, accuracy) {
                if (!routeStartPosition) {
                    return;
                }

                const distance = distanceMeters(position, routeStartPosition);
                hasReachedStart = distance <= startDistanceThresholdMeters;
                if (locateButton) {
                    locateButton.disabled = false;
                    locateButton.textContent = 'Location on';
                    locateButton.className = 'ml-2 rounded-md border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-800';
                }

                if (hasReachedStart) {
                    enableStartDrive();
                    if (routeStatus) routeStatus.textContent = 'You are on start point of route. Start the drive.';
                    setActiveInstruction('You are on the start point', 'Start the drive when ready.');
                    return;
                }

                disableStartDrive();
                const accuracyText = Number.isFinite(accuracy) ? ` Accuracy ${Math.round(accuracy)} m.` : '';
                if (routeStatus) routeStatus.textContent = `${formatDistance(distance)} from start point.${accuracyText}`;
                setActiveInstruction('Go to start point', `${formatDistance(distance)} away from route start.`);
            }

            function updateActiveDrivingInstruction(position) {
                if (!position || directionSteps.length === 0) {
                    return;
                }

                while (currentStepIndex < directionSteps.length - 1 && distanceMeters(position, directionSteps[currentStepIndex].end) < 25) {
                    currentStepIndex += 1;
                }

                const step = directionSteps[currentStepIndex];
                const nextStep = directionSteps[currentStepIndex + 1] ?? null;
                const distanceToStepEnd = distanceMeters(position, step.end);

                let remainingDistance = distanceToStepEnd;
                for (let i = currentStepIndex + 1; i < directionSteps.length; i++) {
                    remainingDistance += directionSteps[i].distanceMeters;
                }
                const remainingDurationMinutes = Math.max(1, Math.round((remainingDistance / 1000) * 1.5));
                
                const durValEl = document.getElementById('hud-duration-val');
                const distValEl = document.getElementById('hud-distance-val');
                if (durValEl) durValEl.textContent = remainingDurationMinutes;
                if (distValEl) distValEl.textContent = (remainingDistance / 1000).toFixed(1) + ' km';
                updateETA(remainingDurationMinutes * 60);

                const nextStepEl = document.getElementById('hud-next-step');
                const nextStepTextEl = document.getElementById('hud-next-step-text');
                if (nextStepEl && nextStepTextEl) {
                    if (nextStep) {
                        nextStepEl.classList.remove('hidden');
                        nextStepTextEl.textContent = nextStep.text;
                    } else {
                        nextStepEl.classList.add('hidden');
                    }
                }

                if (currentStepIndex === directionSteps.length - 1 && distanceToStepEnd < 25) {
                    setActiveInstruction('Route complete', 'You have arrived at your destination.');
                    if (routeStatus) routeStatus.textContent = 'Route complete.';
                    highlightDirectionStep(currentStepIndex);
                    return;
                }

                const guidance = nextInstructionText(step, nextStep, distanceToStepEnd);
                setActiveInstruction(guidance, `${formatDistance(distanceToStepEnd)} remaining in step`);
                highlightDirectionStep(currentStepIndex);
                if (routeStatus) routeStatus.textContent = 'Drive started. Live tracking active.';
            }

            function nextInstructionText(step, nextStep, distance) {
                const distanceText = formatDistance(distance);

                if (distance <= 25) {
                    return nextStep ? (nextStep.text || 'Turn now') : (step.text || 'Arriving at destination');
                }

                if (nextStep) {
                    return `In ${distanceText}, ${nextStep.text || 'continue'}`;
                }

                const text = step.text || 'Continue on route';

                if (/^(head|continue|keep|merge|go straight|drive)/i.test(text)) {
                    return `Drive straight for ${distanceText}`;
                }

                return `${text} in ${distanceText}`;
            }

            function moveVehicle(position, reportedHeading = null) {
                const heading = computeEffectiveHeading(position, reportedHeading);

                if (simIntervalId && vehicleMarker) {
                    vehicleMarker.setPosition(position);
                    vehicleMarker.setIcon(vehicleIcon(heading));
                    lastVehiclePosition = position;
                    lastVehicleHeading = heading;
                } else {
                    animateVehicle(position, heading);
                }
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
                return position && Number.isFinite(position.lat) && Number.isFinite(position.lng);
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

            let audioEnabled = true;
            let lastSpokenInstruction = '';

            function getManeuverIcon(text) {
                const lower = text.toLowerCase();
                if (lower.includes('left')) {
                    return `<svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>`;
                }
                if (lower.includes('right')) {
                    return `<svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>`;
                }
                if (lower.includes('merge') || lower.includes('exit') || lower.includes('highway') || lower.includes('ramp')) {
                    return `<svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H8m0 0l4-4m-4 4l4 4" />
                    </svg>`;
                }
                if (lower.includes('roundabout')) {
                    return `<svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89" />
                    </svg>`;
                }
                return `<svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>`;
            }

            function speakInstruction(text) {
                if (!audioEnabled || !('speechSynthesis' in window)) return;
                const cleanText = text.replace(/\d+\s*(m|km)/gi, '').replace(/\s+/g, ' ').trim();
                if (!cleanText || cleanText === lastSpokenInstruction) return;
                lastSpokenInstruction = cleanText;

                try {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(cleanText);
                    utterance.rate = 1.0;
                    window.speechSynthesis.speak(utterance);
                } catch (e) {
                    console.error('SpeechSynthesis error:', e);
                }
            }

            function alertToast(message) {
                const existing = document.getElementById('hud-toast');
                if (existing) existing.remove();

                const toast = document.createElement('div');
                toast.id = 'hud-toast';
                toast.className = 'fixed bottom-28 left-1/2 transform -translate-x-1/2 z-[999] rounded-full bg-slate-900/90 text-white px-5 py-3 text-sm font-bold shadow-2xl backdrop-blur-md transition-opacity duration-300 pointer-events-none';
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            }

            function updateETA(durationSeconds) {
                const now = new Date();
                const etaDate = new Date(now.getTime() + durationSeconds * 1000);
                let hours = etaDate.getHours();
                const minutes = String(etaDate.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                const etaValEl = document.getElementById('hud-eta-val');
                if (etaValEl) etaValEl.textContent = `${hours}:${minutes} ${ampm}`;
            }

            function setActiveInstruction(title, detail) {
                const legacyTitle = document.getElementById('active-instruction-title');
                const legacyDetail = document.getElementById('active-instruction-detail');
                if (legacyTitle) legacyTitle.textContent = title;
                if (legacyDetail) legacyDetail.textContent = detail;

                const mainInstEl = document.getElementById('hud-main-instruction');
                const subInstEl = document.getElementById('hud-sub-instruction');
                const iconEl = document.getElementById('hud-maneuver-icon');

                if (mainInstEl) mainInstEl.textContent = title;
                if (subInstEl) subInstEl.textContent = detail;
                if (iconEl) iconEl.innerHTML = getManeuverIcon(title);

                if (driveStarted) {
                    speakInstruction(title + '. ' + detail);
                }
            }

            function exitNavigation() {
                driveStarted = false;
                hasReachedStart = false;
                accessConsumedForCurrentDrive = false;

                if (simIntervalId) {
                    clearInterval(simIntervalId);
                    simIntervalId = null;
                }

                const topBanner = document.getElementById('hud-top-banner');
                const bottomSheet = document.getElementById('hud-bottom-sheet');
                if (topBanner) topBanner.classList.remove('hud-slide-down');
                if (bottomSheet) {
                    bottomSheet.classList.remove('hud-slide-up');
                    bottomSheet.classList.add('hidden');
                }

                const searchHeader = document.getElementById('preview-search-header');
                const previewSheet = document.getElementById('preview-bottom-sheet');
                if (searchHeader) {
                    searchHeader.classList.remove('-translate-y-36', 'opacity-0', 'pointer-events-none');
                    searchHeader.classList.add('translate-y-0', 'opacity-100');
                }
                if (previewSheet) {
                    previewSheet.classList.remove('translate-y-36', 'opacity-0', 'pointer-events-none');
                    previewSheet.classList.add('translate-y-0', 'opacity-100');
                }

                const btnText = document.getElementById('btn-preview-start-text');
                if (btnText) btnText.textContent = 'Start drive';
                if (startRouteButton) startRouteButton.disabled = false;

                if (map) {
                    map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                    if (typeof map.setTilt === 'function') map.setTilt(0);
                    if (typeof map.setHeading === 'function') map.setHeading(0);
                    map.setZoom(14);
                    if (routeStartPosition) map.panTo(routeStartPosition);
                }

                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                }

                alertToast('Navigation ended');
            }

            document.addEventListener('DOMContentLoaded', () => {
                const btnCompass = document.getElementById('btn-hud-compass');
                const btnAudio = document.getElementById('btn-hud-audio');
                const btnReport = document.getElementById('btn-hud-report');
                const btnExit = document.getElementById('btn-hud-exit');
                const btnList = document.getElementById('btn-hud-list');

                const btnPreviewStart = document.getElementById('btn-preview-start');
                const btnPreviewSteps = document.getElementById('btn-preview-steps');
                const btnPreviewAudio = document.getElementById('btn-preview-audio');

                const btnSimDemo = document.getElementById('btn-sim-demo');
                const btnSimGps = document.getElementById('btn-sim-gps');
                const btnSimCancel = document.getElementById('btn-sim-cancel');
                const modal = document.getElementById('sim-modal');

                if (btnCompass) {
                    btnCompass.addEventListener('click', () => {
                        const targetPos = latestCurrentPosition || routeStartPosition;
                        if (targetPos) {
                            map.panTo(targetPos);
                        }
                        if (typeof map.setHeading === 'function') {
                            map.setHeading(0);
                        }
                        const svg = btnCompass.querySelector('svg');
                        if (svg) {
                            svg.style.transform = 'rotate(360deg)';
                            setTimeout(() => svg.style.transform = 'rotate(0deg)', 300);
                        }
                        alertToast('Map alignment reset');
                    });
                }

                function toggleAudioGuidance() {
                    audioEnabled = !audioEnabled;
                    const svgHud = document.getElementById('hud-audio-svg');
                    const svgPreview = document.getElementById('preview-audio-svg');
                    
                    const pathSoundOn = `<path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />`;
                    const pathMute = `<path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /><path stroke-linecap="round" stroke-linejoin="round" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />`;
                    
                    if (audioEnabled) {
                        if (svgHud) svgHud.innerHTML = pathSoundOn;
                        if (svgPreview) svgPreview.innerHTML = pathSoundOn;
                        alertToast('Voice guidance enabled');
                    } else {
                        if (svgHud) svgHud.innerHTML = pathMute;
                        if (svgPreview) svgPreview.innerHTML = pathMute;
                        alertToast('Voice guidance muted');
                    }
                }

                if (btnAudio) {
                    btnAudio.addEventListener('click', toggleAudioGuidance);
                }

                if (btnPreviewAudio) {
                    btnPreviewAudio.addEventListener('click', toggleAudioGuidance);
                }

                if (btnReport) {
                    btnReport.addEventListener('click', () => {
                        alertToast('Hazard reported. Thank you!');
                    });
                }

                if (btnExit) {
                    btnExit.addEventListener('click', exitNavigation);
                }

                if (btnList) {
                    btnList.addEventListener('click', () => {
                        const listSection = document.getElementById('directions-list');
                        if (listSection) {
                            listSection.scrollIntoView({ behavior: 'smooth' });
                            alertToast('Scrolled to instructions list');
                        }
                    });
                }

                if (btnPreviewSteps) {
                    btnPreviewSteps.addEventListener('click', () => {
                        const listSection = document.getElementById('directions-list');
                        if (listSection) {
                            listSection.scrollIntoView({ behavior: 'smooth' });
                            alertToast('Scrolled to instructions list');
                        }
                    });
                }

                if (btnPreviewStart) {
                    btnPreviewStart.addEventListener('click', () => {
                        startLiveRoute();
                    });
                }

                function closeModal() {
                    if (modal) {
                        modal.classList.add('opacity-0', 'pointer-events-none');
                        const card = modal.querySelector('div');
                        if (card) {
                            card.classList.remove('scale-100');
                            card.classList.add('scale-95');
                        }
                    }
                }

                if (btnSimDemo) {
                    btnSimDemo.addEventListener('click', () => {
                        closeModal();
                        startSimulationDrive();
                    });
                }

                if (btnSimGps) {
                    btnSimGps.addEventListener('click', () => {
                        closeModal();
                        startLiveRoute(true);
                    });
                }

                if (btnSimCancel) {
                    btnSimCancel.addEventListener('click', closeModal);
                }
            });

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
                const rot = Math.round((rotation % 360 + 360) % 360);
                const svg = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
                        <defs>
                            <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000000" flood-opacity="0.4"/>
                            </filter>
                        </defs>
                        <circle cx="24" cy="24" r="20" fill="#3b82f6" fill-opacity="0.2"/>
                        <g transform="rotate(${rot} 24 24)" filter="url(#shadow)">
                            <path d="M 24 6 L 37 38 L 24 30 L 11 38 Z" fill="#2563eb" stroke="#ffffff" stroke-width="3" stroke-linejoin="round"/>
                        </g>
                    </svg>
                `;

                return {
                    url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`,
                    scaledSize: new google.maps.Size(48, 48),
                    anchor: new google.maps.Point(24, 24),
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
                const duration = 500;
                const startHeading = lastVehicleHeading ?? heading;

                let deltaHeading = (heading - startHeading) % 360;
                if (deltaHeading > 180) deltaHeading -= 360;
                if (deltaHeading < -180) deltaHeading += 360;

                function step(now) {
                    const progress = Math.min((now - startedAt) / duration, 1);
                    const position = {
                        lat: start.lat + ((nextPosition.lat - start.lat) * progress),
                        lng: start.lng + ((nextPosition.lng - start.lng) * progress),
                    };
                    const currentHeading = (startHeading + (deltaHeading * progress) + 360) % 360;

                    vehicleMarker.setPosition(position);
                    vehicleMarker.setIcon(vehicleIcon(currentHeading));

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
