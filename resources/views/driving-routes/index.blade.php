@extends('layouts.app')

@section('title', 'Routes')

@push('styles')
    <style>
        .routes-page {
            background-color: #f8f9fa;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .09), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .07), transparent 30%),
                linear-gradient(180deg, rgba(248, 249, 250, .9), rgba(241, 243, 245, .94) 48%, rgba(248, 249, 250, .96)),
                var(--public-image-pages);
            background-position: center, center, center, center top;
            background-repeat: no-repeat;
            background-size: auto, auto, auto, cover;
            color: #212529;
        }

        .routes-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #1e40af 0%, #2563eb 44%, #0891b2 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .routes-glass {
            border: 1px solid rgba(203, 213, 225, .9);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
        }

        .routes-card {
            transition: transform 240ms cubic-bezier(.16, 1, .3, 1), box-shadow 240ms ease-out, border-color 240ms ease-out;
        }

        .routes-card:hover {
            border-color: rgba(37, 99, 235, .28);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .12);
            transform: translateY(-4px);
        }

        .routes-filter {
            border: 1px solid rgba(37, 99, 235, .24);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .86);
            padding: .62rem .85rem;
            color: #1d4ed8;
            font-size: .875rem;
            font-weight: 800;
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), background 200ms ease-out, box-shadow 200ms ease-out;
        }

        .routes-filter:hover,
        .routes-filter.is-active {
            color: #fff;
            background: linear-gradient(135deg, #1e40af, #2563eb 52%, #0891b2);
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            transform: translateY(-1px);
        }

        .routes-city-combobox {
            position: relative;
            max-width: 44rem;
        }

        .routes-city-input-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: .75rem;
            border: 1px solid #d8dee6;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .92);
            padding: .5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
            transition: border-color 220ms ease-out, box-shadow 220ms ease-out;
        }

        .routes-city-combobox.is-open .routes-city-input-wrap,
        .routes-city-input-wrap:focus-within {
            border-color: rgba(37, 99, 235, .5);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12), 0 8px 20px rgba(15, 23, 42, .08);
        }

        .routes-city-input {
            min-width: 0;
            border: 0;
            background: transparent;
            padding: .7rem .8rem;
            color: #212529;
            font-weight: 800;
            outline: 0;
        }

        .routes-city-input::placeholder {
            color: #6b7280;
        }

        .routes-city-panel {
            position: absolute;
            right: 0;
            left: 0;
            z-index: 20;
            margin-top: .55rem;
            max-height: 19rem;
            overflow-y: auto;
            border: 1px solid #d8dee6;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .98);
            box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-6px);
            transition: opacity 180ms ease-out, transform 180ms cubic-bezier(.16, 1, .3, 1);
            backdrop-filter: blur(18px);
        }

        .routes-city-combobox.is-open .routes-city-panel {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .routes-city-option {
            display: block;
            width: 100%;
            border: 0;
            border-bottom: 1px solid #e0e0e0;
            background: transparent;
            padding: .9rem 1rem;
            text-align: left;
            transition: background 180ms ease-out, transform 180ms cubic-bezier(.16, 1, .3, 1);
        }

        .routes-city-option:hover,
        .routes-city-option:focus-visible {
            background: #eff6ff;
            outline: 0;
            transform: translateX(2px);
        }

        .routes-city-option:last-child {
            border-bottom: 0;
        }

        .routes-button {
            display: inline-flex;
            min-height: 2.75rem;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            padding: .75rem 1rem;
            font-weight: 900;
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), box-shadow 200ms ease-out, border-color 200ms ease-out, background 200ms ease-out;
        }

        .routes-button:hover {
            transform: translateY(-1px) scale(1.02);
        }

        .routes-button-primary {
            color: #fff;
            background: linear-gradient(135deg, #1e40af, #2563eb 52%, #0891b2);
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
        }

        .routes-button-secondary {
            border: 1px solid rgba(37, 99, 235, .24);
            color: #1d4ed8;
            background: #ffffff;
        }

        .routes-card-visual {
            background-color: #f1f3f5;
            background-image:
                linear-gradient(135deg, rgba(248, 249, 250, .64), rgba(255, 255, 255, .34), rgba(241, 243, 245, .7)),
                var(--public-image-route);
            background-position: center, center;
            background-repeat: no-repeat;
            background-size: auto, cover;
        }

        .routes-card-visual svg {
            opacity: .78;
        }
    </style>
@endpush

@section('content')
    <div class="routes-page">
        <section class="border-b border-white/10">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase text-cyan-200">Route catalog</p>
                        <h1 class="mt-3 text-5xl font-black tracking-normal text-white">
                            Driving Test Routes
                            @if($selectedCity)
                                <span class="routes-gradient-text block">{{ $selectedCity->name }}</span>
                            @endif
                        </h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-400">
                            Browse paid route maps, compare pricing, and unlock limited map starts for your test area.
                        </p>
                    </div>

                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.driving-routes.create') }}" class="routes-button routes-button-primary">
                                Add Route
                            </a>
                        @endif
                    @endauth
                </div>

                <div class="mt-8 flex flex-col md:flex-row md:items-center gap-6">
                    <!-- Package Type Filters -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-400 mr-2">Package:</span>
                        <a href="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['package_type' => null]))) }}" class="routes-filter {{ !$selectedPackageType ? 'is-active' : '' }}">
                            All Packages
                        </a>
                        <a href="{{ route('driving-routes.index', array_merge(request()->query(), ['package_type' => 'g1'])) }}" class="routes-filter {{ $selectedPackageType === 'g1' ? 'is-active' : '' }}">
                            G1 Package
                        </a>
                        <a href="{{ route('driving-routes.index', array_merge(request()->query(), ['package_type' => 'g2'])) }}" class="routes-filter {{ $selectedPackageType === 'g2' ? 'is-active' : '' }}">
                            G2 Package
                        </a>
                    </div>
                </div>

                @if($cities->isNotEmpty())
                    <div class="routes-city-combobox mt-6" data-routes-city-combobox>
                        <div class="routes-city-input-wrap">
                            <input
                                type="text"
                                class="routes-city-input"
                                value="{{ $selectedCity?->name }}"
                                placeholder="Select your city"
                                autocomplete="off"
                                role="combobox"
                                aria-expanded="false"
                                aria-controls="routes-city-options"
                                data-routes-city-input
                            >
                            <a href="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['city' => null]))) }}" class="routes-button {{ $selectedCity ? 'routes-button-secondary' : 'routes-button-primary' }}">
                                All Cities
                            </a>
                        </div>

                        <div id="routes-city-options" class="routes-city-panel" role="listbox" data-routes-city-panel>
                            @foreach($cities as $city)
                                <button
                                    type="button"
                                    class="routes-city-option"
                                    role="option"
                                    data-routes-city-option
                                    data-city-name="{{ \Illuminate\Support\Str::lower($city->name) }}"
                                    data-city-address="{{ \Illuminate\Support\Str::lower($city->address) }}"
                                    data-city-url="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['city' => $city->id]))) }}"
                                    @if($selectedCity?->id === $city->id) aria-selected="true" @endif
                                >
                                    <span class="flex items-start justify-between gap-4">
                                        <span>
                                            <span class="block font-black text-white">{{ $city->name }}</span>
                                            <span class="mt-1 block text-sm leading-5 text-slate-400">{{ $city->address }}</span>
                                        </span>
                                        <span class="shrink-0 rounded-md border border-blue-500/20 bg-white/[.06] px-2 py-1 text-xs font-black text-cyan-100">
                                            {{ $city->active_routes_count }}
                                        </span>
                                    </span>
                                </button>
                            @endforeach
                            <p class="hidden px-4 py-5 text-sm font-semibold text-slate-400" data-routes-city-empty>No matching cities.</p>
                        </div>

                        @if($selectedCity)
                            <div class="routes-glass mt-4 p-4">
                                <p class="text-sm font-black text-white">{{ $selectedCity->name }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-400">{{ $selectedCity->address }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            @if($routes->isEmpty())
                <div class="routes-glass px-6 py-14 text-center">
                    <h2 class="text-xl font-black text-white">No routes available</h2>
                    <p class="mt-2 text-sm text-slate-400">Try another city or check back as new routes are added.</p>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($routes as $drivingRoute)
                        @php
                            $purchase = $purchases->get($drivingRoute->id);
                            $remainingStarts = $purchase?->remainingStarts() ?? 0;
                            $canOpenMap = auth()->user()?->is_admin || $remainingStarts > 0;
                            $routeCity = $drivingRoute->relationLoaded('cityModel') ? $drivingRoute->cityModel : null;
                            $cityName = $routeCity?->name ?? $drivingRoute->city;
                            $cityAddress = $routeCity?->address;
                        @endphp
                        <article class="routes-glass routes-card flex min-h-[25rem] flex-col justify-between overflow-hidden">
                            <div class="routes-card-visual relative h-36">
                                <svg class="h-full w-full" viewBox="0 0 420 180" fill="none" aria-hidden="true">
                                    <path d="M0 44H420M0 96H420M0 148H420M70 0V180M154 0V180M238 0V180M322 0V180" stroke="rgba(148,163,184,.15)" />
                                    <path d="M34 142 C96 68 156 110 210 54 C274 -12 322 62 386 30" stroke="url(#routeCard{{ $drivingRoute->id }})" stroke-width="7" stroke-linecap="round" />
                                    <circle cx="34" cy="142" r="9" fill="#38bdf8" />
                                    <circle cx="386" cy="30" r="9" fill="#2563eb" />
                                    <defs>
                                        <linearGradient id="routeCard{{ $drivingRoute->id }}" x1="34" x2="386" y1="142" y2="30">
                                            <stop stop-color="#1e3a8a" />
                                            <stop offset=".55" stop-color="#2563eb" />
                                            <stop offset="1" stop-color="#06b6d4" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="mb-2">
                                            <span class="inline-flex items-center rounded bg-blue-500/10 px-2 py-0.5 text-xs font-extrabold text-cyan-300 border border-blue-500/20 uppercase">
                                                {{ strtoupper($drivingRoute->package_type) }} Package
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-black text-white">{{ $drivingRoute->title }}</h3>
                                        <p class="mt-1 text-sm text-slate-400">{{ $cityName }}, {{ $drivingRoute->province }}</p>
                                        @if($cityAddress)
                                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ $cityAddress }}</p>
                                        @endif
                                    </div>
                                    <div class="shrink-0 rounded-md border border-blue-500/20 bg-white/[.06] px-3 py-2 text-right text-white">
                                        <div class="text-xs text-slate-400">Price</div>
                                        <div class="font-black">${{ number_format((float) $drivingRoute->price, 2) }}</div>
                                    </div>
                                </div>

                                @if($drivingRoute->description)
                                    <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-400">{{ $drivingRoute->description }}</p>
                                @endif

                                <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-md bg-white/[.06] p-3">
                                        <dt class="font-bold text-slate-500">Start</dt>
                                        <dd class="mt-1 font-black text-white">{{ $drivingRoute->start_label ?: 'Start point' }}</dd>
                                    </div>
                                    <div class="rounded-md bg-white/[.06] p-3">
                                        <dt class="font-bold text-slate-500">Destination</dt>
                                        <dd class="mt-1 font-black text-white">{{ $drivingRoute->destination_label ?: 'Destination' }}</dd>
                                    </div>
                                    <div class="rounded-md bg-white/[.06] p-3">
                                        <dt class="font-bold text-slate-500">Duration</dt>
                                        <dd class="mt-1 font-black text-white">{{ $drivingRoute->route_duration_minutes ? $drivingRoute->route_duration_minutes.' mins' : 'Ready' }}</dd>
                                    </div>
                                    <div class="rounded-md bg-white/[.06] p-3">
                                        <dt class="font-bold text-slate-500">Starts</dt>
                                        <dd class="mt-1 font-black text-white">{{ $drivingRoute->access_limit ?? 1 }}</dd>
                                    </div>
                                </dl>

                                <div class="mt-auto flex flex-wrap items-center gap-2 pt-5">
                                    @if($canOpenMap)
                                        <a href="{{ route('driving-routes.show', $drivingRoute) }}" class="routes-button routes-button-primary flex-1">
                                            Open Map
                                        </a>
                                        @if(! auth()->user()?->is_admin)
                                            <span class="rounded-md border border-blue-500/20 bg-white/[.06] px-3 py-2 text-sm font-black text-cyan-100">
                                                {{ $remainingStarts }} left
                                            </span>
                                        @endif
                                    @elseif(auth()->check())
                                        <a href="{{ route('driving-routes.checkout', $drivingRoute) }}" class="routes-button routes-button-primary flex-1">
                                            {{ $purchase ? 'Buy More Starts' : 'Buy Route' }}
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="routes-button routes-button-primary flex-1">
                                            Log In to Buy
                                        </a>
                                    @endif

                                    @if($drivingRoute->preview_pdf_path)
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($drivingRoute->preview_pdf_path) }}" target="_blank" class="routes-button routes-button-secondary">
                                            PDF
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-routes-city-combobox]').forEach((combobox) => {
            const input = combobox.querySelector('[data-routes-city-input]');
            const options = Array.from(combobox.querySelectorAll('[data-routes-city-option]'));
            const empty = combobox.querySelector('[data-routes-city-empty]');

            function openPanel() {
                combobox.classList.add('is-open');
                input?.setAttribute('aria-expanded', 'true');
            }

            function closePanel() {
                combobox.classList.remove('is-open');
                input?.setAttribute('aria-expanded', 'false');
            }

            function filterOptions() {
                const query = (input?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                options.forEach((option) => {
                    const matches = !query
                        || option.dataset.cityName.includes(query)
                        || option.dataset.cityAddress.includes(query);

                    option.hidden = !matches;
                    visibleCount += matches ? 1 : 0;
                });

                empty?.classList.toggle('hidden', visibleCount > 0);
                openPanel();
            }

            input?.addEventListener('focus', openPanel);
            input?.addEventListener('click', openPanel);
            input?.addEventListener('input', filterOptions);
            input?.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closePanel();
                }

                if (event.key === 'Enter') {
                    const firstVisibleOption = options.find((option) => !option.hidden);

                    if (firstVisibleOption) {
                        event.preventDefault();
                        window.location.href = firstVisibleOption.dataset.cityUrl;
                    }
                }
            });

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    window.location.href = option.dataset.cityUrl;
                });
            });

            document.addEventListener('click', (event) => {
                if (!combobox.contains(event.target)) {
                    closePanel();
                }
            });
        });
    </script>
@endpush
