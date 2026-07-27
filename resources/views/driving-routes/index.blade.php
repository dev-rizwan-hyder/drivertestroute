@extends('layouts.app')

@section('title', 'Routes')

@push('styles')
    <style>
        /* ── Page shell ── */
        .routes-page {
            background-color: #eef1f6;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .08), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .06), transparent 30%);
            color: #212529;
            min-height: 100vh;
        }

        /* ── Gradient text ── */
        .routes-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #1e40af 0%, #2563eb 44%, #0891b2 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        /* ── Glass card ── */
        .routes-glass {
            border: 1px solid rgba(203, 213, 225, .75);
            border-radius: .75rem;
            background: rgba(255, 255, 255, .82);
            box-shadow: 0 2px 12px rgba(15, 23, 42, .07);
            backdrop-filter: blur(18px);
        }

        /* ── Route cards ── */
        .routes-card {
            transition: transform 240ms cubic-bezier(.16, 1, .3, 1),
                        box-shadow 240ms ease-out,
                        border-color 240ms ease-out;
        }

        .routes-card:hover {
            border-color: rgba(37, 99, 235, .32);
            box-shadow: 0 16px 36px rgba(15, 23, 42, .13);
            transform: translateY(-5px);
        }

        /* ── Package filter pills ── */
        .routes-filter {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid rgba(37, 99, 235, .22);
            border-radius: 9999px;
            background: rgba(255, 255, 255, .9);
            padding: .38rem 1rem;
            color: #1d4ed8;
            font-size: .8125rem;
            font-weight: 700;
            letter-spacing: .01em;
            transition: transform 190ms cubic-bezier(.16, 1, .3, 1),
                        background 190ms ease-out,
                        box-shadow 190ms ease-out,
                        border-color 190ms ease-out;
            white-space: nowrap;
        }

        .routes-filter:hover {
            border-color: rgba(37, 99, 235, .5);
            box-shadow: 0 4px 14px rgba(37, 99, 235, .14);
            transform: translateY(-1px);
        }

        .routes-filter.is-active {
            color: #fff;
            background: linear-gradient(135deg, #1e40af, #2563eb 55%, #0891b2);
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(37, 99, 235, .28);
            transform: translateY(-1px);
        }

        /* ── City combobox ── */
        .routes-city-combobox {
            position: relative;
            max-width: 44rem;
        }

        .routes-city-input-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: .75rem;
            border: 1.5px solid #d1d9e6;
            border-radius: .75rem;
            background: rgba(255, 255, 255, .92);
            padding: .45rem .45rem .45rem .9rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
            backdrop-filter: blur(16px);
            transition: border-color 200ms ease-out, box-shadow 200ms ease-out;
        }

        .routes-city-combobox.is-open .routes-city-input-wrap,
        .routes-city-input-wrap:focus-within {
            border-color: rgba(37, 99, 235, .55);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .11), 0 8px 22px rgba(15, 23, 42, .08);
        }

        .routes-city-input {
            min-width: 0;
            border: 0;
            background: transparent;
            padding: .65rem .6rem;
            color: #1a202c;
            font-size: .9375rem;
            font-weight: 600;
            outline: 0;
        }

        .routes-city-input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .routes-city-panel {
            position: absolute;
            right: 0;
            left: 0;
            z-index: 30;
            margin-top: .5rem;
            max-height: 19rem;
            overflow-y: auto;
            border: 1.5px solid #d1d9e6;
            border-radius: .75rem;
            background: rgba(255, 255, 255, .98);
            box-shadow: 0 20px 50px rgba(15, 23, 42, .13);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-8px);
            transition: opacity 170ms ease-out, transform 170ms cubic-bezier(.16, 1, .3, 1);
            backdrop-filter: blur(20px);
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
            border-bottom: 1px solid rgba(203, 213, 225, .6);
            background: transparent;
            padding: .85rem 1rem;
            text-align: left;
            cursor: pointer;
            transition: background 150ms ease-out, padding-left 150ms ease-out;
        }

        .routes-city-option:hover,
        .routes-city-option:focus-visible {
            background: #eff6ff;
            outline: 0;
            padding-left: 1.3rem;
        }

        .routes-city-option:last-child {
            border-bottom: 0;
        }

        /* ── Buttons ── */
        .routes-button {
            display: inline-flex;
            min-height: 2.6rem;
            align-items: center;
            justify-content: center;
            border-radius: .625rem;
            padding: .6rem 1.1rem;
            font-size: .875rem;
            font-weight: 700;
            transition: transform 190ms cubic-bezier(.16, 1, .3, 1),
                        box-shadow 190ms ease-out,
                        border-color 190ms ease-out,
                        background 190ms ease-out;
        }

        .routes-button:hover {
            transform: translateY(-1px) scale(1.015);
        }

        .routes-button-primary {
            color: #fff;
            background: linear-gradient(135deg, #1e40af, #2563eb 55%, #0891b2);
            box-shadow: 0 8px 24px rgba(37, 99, 235, .24);
        }

        .routes-button-primary:hover {
            box-shadow: 0 12px 30px rgba(37, 99, 235, .32);
        }

        .routes-button-secondary {
            border: 1.5px solid rgba(37, 99, 235, .28);
            color: #1d4ed8;
            background: #fff;
        }

        .routes-button-secondary:hover {
            border-color: rgba(37, 99, 235, .5);
            background: #eff6ff;
        }

        /* ── Card visual header ── */
        .routes-card-visual {
            background: linear-gradient(135deg, #e8edf8 0%, #dde8f6 50%, #d4e4f5 100%);
        }

        /* ── Search bar ── */
        .routes-search-wrap {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto auto;
            align-items: center;
            gap: .5rem;
            border: 1.5px solid #d1d9e6;
            border-radius: .75rem;
            background: rgba(255, 255, 255, .94);
            padding: .4rem .4rem .4rem .85rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
            backdrop-filter: blur(16px);
            transition: border-color 200ms ease-out, box-shadow 200ms ease-out;
        }

        .routes-search-wrap:focus-within {
            border-color: rgba(37, 99, 235, .55);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .11), 0 8px 22px rgba(15, 23, 42, .07);
        }

        /* loading ring inside search bar */
        .routes-search-wrap.is-loading::after {
            content: '';
            display: block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #d1d5db;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: routes-spin .6s linear infinite;
            flex-shrink: 0;
        }

        @keyframes routes-spin {
            to { transform: rotate(360deg); }
        }

        .routes-search-icon {
            flex-shrink: 0;
            width: 1.05rem;
            height: 1.05rem;
            color: #9ca3af;
        }

        .routes-search-input {
            min-width: 0;
            border: 0;
            background: transparent;
            padding: .6rem .3rem;
            color: #1a202c;
            font-size: .9375rem;
            font-weight: 600;
            outline: 0;
        }

        .routes-search-input::placeholder {
            color: #b0bac4;
            font-weight: 400;
        }

        .routes-search-input::-webkit-search-cancel-button { display: none; }

        .routes-search-clear {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            color: #6b7280;
            background: #e9ecef;
            transition: background 150ms, color 150ms;
        }

        .routes-search-clear:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .routes-search-btn {
            flex-shrink: 0;
            padding: .52rem 1.1rem;
            font-size: .875rem;
        }

        /* ── Active-filter badges ── */
        .routes-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border: 1px solid rgba(37, 99, 235, .25);
            border-radius: 9999px;
            background: rgba(37, 99, 235, .07);
            padding: .22rem .7rem;
            font-size: .8rem;
            font-weight: 700;
            color: #1d4ed8;
        }

        .routes-badge-remove {
            font-size: 1rem;
            line-height: 1;
            color: #9ca3af;
            font-weight: 900;
            transition: color 130ms;
        }

        .routes-badge-remove:hover {
            color: #dc2626;
        }

        /* ── Results grid fade-in when swapped ── */
        #routes-results-area {
            transition: opacity 180ms ease-out;
        }

        #routes-results-area.is-fetching {
            opacity: .4;
            pointer-events: none;
        }

        /* ── Count badge ── */
        #routes-count-label {
            transition: opacity 180ms ease-out;
        }
    </style>
@endpush

@section('content')
    <div class="routes-page">

        {{-- ══ Hero / filter header ══ --}}
        <section class="border-b border-slate-200/60">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">

                {{-- Title row --}}
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-blue-600">Route catalog</p>
                        <h1 class="mt-2 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                            Driving Test Routes
                            @if($selectedCity)
                                <span class="routes-gradient-text block">{{ $selectedCity->name }}</span>
                            @endif
                        </h1>
                        <p class="mt-3 max-w-2xl text-base leading-7 text-slate-500">
                            Browse paid route maps, compare pricing, and unlock limited map starts for your test area.
                        </p>
                    </div>

                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.driving-routes.create') }}" class="routes-button routes-button-primary self-start lg:self-auto">
                                + Add Route
                            </a>
                        @endif
                    @endauth
                </div>

                {{-- ── Search + filters ── --}}
                <div class="mt-8 space-y-4">

                    {{-- Search bar --}}
                    <form method="GET" action="{{ route('driving-routes.index') }}" id="routes-search-form">
                        @if($selectedCity)
                            <input type="hidden" name="city" value="{{ $selectedCity->id }}">
                        @endif
                        @if($selectedPackageType)
                            <input type="hidden" name="package_type" value="{{ $selectedPackageType }}">
                        @endif

                        <div class="routes-search-wrap" id="routes-search-wrap">
                            <svg class="routes-search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                            </svg>
                            <input
                                type="search"
                                name="search"
                                id="routes-search-input"
                                class="routes-search-input"
                                value="{{ $search }}"
                                placeholder="Search by route name or package…"
                                autocomplete="off"
                                aria-label="Search routes"
                            >
                            <button
                                type="button"
                                id="routes-search-clear"
                                class="routes-search-clear {{ $search ? '' : 'hidden' }}"
                                aria-label="Clear search"
                            >
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                                </svg>
                            </button>
                            <button type="submit" class="routes-button routes-button-primary routes-search-btn">
                                Search
                            </button>
                        </div>
                    </form>

                    {{-- Package pills --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-400 mr-1">Package:</span>
                        <a href="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['package_type' => null]))) }}"
                           class="routes-filter {{ !$selectedPackageType ? 'is-active' : '' }}">
                            All Packages
                        </a>
                        @foreach($packageOptions as $slug => $label)
                            <a href="{{ route('driving-routes.index', array_merge(request()->query(), ['package_type' => $slug])) }}"
                               class="routes-filter {{ $selectedPackageType === $slug ? 'is-active' : '' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Active-filter summary --}}
                    @if($search || $selectedPackageType || $selectedCity)
                        <div class="flex flex-wrap items-center gap-2 pt-1" id="routes-filter-summary">
                            <span class="text-xs font-semibold text-slate-400">Filtering by:</span>
                            @if($search)
                                <span class="routes-badge">
                                    "{{ $search }}"
                                    <a href="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['search' => null]))) }}" class="routes-badge-remove" aria-label="Remove search filter">&times;</a>
                                </span>
                            @endif
                            @if($selectedPackageType)
                                <span class="routes-badge">
                                    {{ $packageOptions[$selectedPackageType] ?? $selectedPackageType }}
                                    <a href="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['package_type' => null]))) }}" class="routes-badge-remove" aria-label="Remove package filter">&times;</a>
                                </span>
                            @endif
                            @if($selectedCity)
                                <span class="routes-badge">
                                    {{ $selectedCity->name }}
                                    <a href="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['city' => null]))) }}" class="routes-badge-remove" aria-label="Remove city filter">&times;</a>
                                </span>
                            @endif
                            <a href="{{ route('driving-routes.index') }}" class="text-xs font-bold text-blue-500 hover:text-blue-700 underline underline-offset-2 ml-1">
                                Clear all
                            </a>
                            <span class="ml-auto text-xs text-slate-400" id="routes-count-label">
                                {{ $routes->count() }} {{ Str::plural('route', $routes->count()) }} found
                            </span>
                        </div>
                    @else
                        {{-- Count shown even without active filters --}}
                        <p class="text-xs text-slate-400" id="routes-count-label">
                            {{ $routes->count() }} {{ Str::plural('route', $routes->count()) }} found
                        </p>
                    @endif
                </div>

                {{-- City combobox --}}
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
                            <a href="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['city' => null]))) }}"
                               class="routes-button {{ $selectedCity ? 'routes-button-secondary' : 'routes-button-primary' }} shrink-0">
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
                                            <span class="block font-bold text-slate-800">{{ $city->name }}</span>
                                            <span class="mt-0.5 block text-sm leading-5 text-slate-500">{{ $city->address }}</span>
                                        </span>
                                        <span class="shrink-0 rounded-full border border-blue-200 bg-blue-50 px-2 py-0.5 text-xs font-black text-blue-600">
                                            {{ $city->active_routes_count }}
                                        </span>
                                    </span>
                                </button>
                            @endforeach
                            <p class="hidden px-4 py-5 text-sm font-semibold text-slate-400" data-routes-city-empty>No matching cities.</p>
                        </div>

                        @if($selectedCity)
                            <div class="routes-glass mt-4 p-4">
                                <p class="text-sm font-bold text-slate-800">{{ $selectedCity->name }}</p>
                                <p class="mt-0.5 text-sm leading-6 text-slate-500">{{ $selectedCity->address }}</p>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </section>

        {{-- ══ Route cards ══ --}}
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div id="routes-results-area">
                @include('driving-routes._cards')
            </div>
        </section>

    </div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Helpers ── */
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const plural = (n, word) => `${n} ${n === 1 ? word : word + 's'}`;

    /* ── Live search (fetch, no reload) ── */
    const form        = $('#routes-search-form');
    const searchInput = $('#routes-search-input');
    const clearBtn    = $('#routes-search-clear');
    const searchWrap  = $('#routes-search-wrap');
    const resultsArea = $('#routes-results-area');
    const countLabel  = $('#routes-count-label');

    let debounceTimer = null;
    let activeRequest = null; // AbortController

    function buildPartialUrl(query) {
        const params = new URLSearchParams(window.location.search);
        if (query.trim() === '') {
            params.delete('search');
        } else {
            params.set('search', query.trim());
        }
        params.set('partial', '1');
        return `${form ? form.action : window.location.pathname}?${params.toString()}`;
    }

    async function fetchResults(query) {
        // Cancel any in-flight request
        if (activeRequest) activeRequest.abort();
        activeRequest = new AbortController();

        searchWrap?.classList.add('is-loading');
        resultsArea?.classList.add('is-fetching');

        try {
            const res = await fetch(buildPartialUrl(query), {
                signal: activeRequest.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!res.ok) throw new Error('Network response not OK');
            const data = await res.json();

            if (resultsArea) resultsArea.innerHTML = data.html;
            if (countLabel) countLabel.textContent = plural(data.count, 'route') + ' found';

            // Update browser URL without reload
            const params = new URLSearchParams(window.location.search);
            if (query.trim() === '') {
                params.delete('search');
            } else {
                params.set('search', query.trim());
            }
            const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
            window.history.replaceState(null, '', newUrl);

        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('Live search error:', err);
            }
        } finally {
            searchWrap?.classList.remove('is-loading');
            resultsArea?.classList.remove('is-fetching');
            activeRequest = null;
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value;

            // Show/hide clear button
            clearBtn?.classList.toggle('hidden', q.length === 0);

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchResults(q), 380);
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            clearBtn.classList.add('hidden');
            clearTimeout(debounceTimer);
            fetchResults('');
        });
    }

    // Standard form submit still works (Enter key, Search button)
    if (form) {
        form.addEventListener('submit', () => {
            clearTimeout(debounceTimer);
            if (activeRequest) activeRequest.abort();
            // Let the default form submit proceed
        });
    }

    /* ── City combobox ── */
    document.querySelectorAll('[data-routes-city-combobox]').forEach((combobox) => {
        const input   = combobox.querySelector('[data-routes-city-input]');
        const options = Array.from(combobox.querySelectorAll('[data-routes-city-option]'));
        const empty   = combobox.querySelector('[data-routes-city-empty]');

        const openPanel  = () => { combobox.classList.add('is-open');    input?.setAttribute('aria-expanded', 'true');  };
        const closePanel = () => { combobox.classList.remove('is-open'); input?.setAttribute('aria-expanded', 'false'); };

        function filterOptions() {
            const q = (input?.value || '').trim().toLowerCase();
            let visible = 0;
            options.forEach((opt) => {
                const hit = !q
                    || opt.dataset.cityName.includes(q)
                    || opt.dataset.cityAddress.includes(q);
                opt.hidden = !hit;
                if (hit) visible++;
            });
            empty?.classList.toggle('hidden', visible > 0);
            openPanel();
        }

        input?.addEventListener('focus', openPanel);
        input?.addEventListener('click', openPanel);
        input?.addEventListener('input', filterOptions);
        input?.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') { closePanel(); return; }
            if (e.key === 'Enter') {
                const first = options.find((o) => !o.hidden);
                if (first) { e.preventDefault(); window.location.href = first.dataset.cityUrl; }
            }
        });

        options.forEach((opt) => {
            opt.addEventListener('click', () => { window.location.href = opt.dataset.cityUrl; });
        });

        document.addEventListener('click', (e) => {
            if (!combobox.contains(e.target)) closePanel();
        });
    });

    /* ── Confirm map open ── */
    window.confirmOpenMap = function (event, remainingStarts) {
        if (@json(auth()->user()?->is_admin ?? false)) return true;
        const msg = `🚗 Ready to practice this test route?\n\nOpening this map will use 1 map start (${remainingStarts} ${remainingStarts === 1 ? 'start' : 'starts'} remaining).\n\n⚠️ IMPORTANT: Keep this page open while driving. Refreshing or exiting will end your session and require another start to reopen.\n\nWould you like to proceed?`;
        if (!confirm(msg)) { event.preventDefault(); return false; }
        return true;
    };
})();
</script>
@endpush
