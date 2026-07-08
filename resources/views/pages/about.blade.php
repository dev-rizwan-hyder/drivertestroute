@extends('layouts.app')

@section('title', 'About Driver Test Routes')

@push('styles')
    <style>
        .public-dark-page {
            background:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .18), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .12), transparent 30%),
                linear-gradient(180deg, #0a0e1a, #0d1117 48%, #0a0e1a);
            color: #f8fafc;
        }

        .public-glass-card {
            border: 1px solid rgba(59, 130, 246, .22);
            border-radius: .5rem;
            background:
                linear-gradient(180deg, rgba(56, 189, 248, .075), rgba(15, 23, 42, .18)),
                rgba(17, 24, 39, .68);
            box-shadow: 0 22px 58px rgba(2, 6, 23, .34), inset 0 1px 0 rgba(255, 255, 255, .1);
            backdrop-filter: blur(16px);
            transition: transform 240ms cubic-bezier(.16, 1, .3, 1), box-shadow 240ms ease-out, border-color 240ms ease-out;
        }

        .public-glass-card:hover {
            border-color: rgba(56, 189, 248, .38);
            box-shadow: 0 0 20px rgba(59, 130, 246, .32), 0 26px 64px rgba(2, 6, 23, .38);
            transform: translateY(-3px);
        }

        .public-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #fff 0%, #bfdbfe 26%, #38bdf8 56%, #cffafe 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        [data-page-reveal] {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity 560ms cubic-bezier(.16, 1, .3, 1), transform 560ms cubic-bezier(.16, 1, .3, 1);
            transition-delay: var(--delay, 0ms);
        }

        [data-page-reveal].is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .public-glass-card,
            [data-page-reveal] {
                transition: none !important;
            }

            [data-page-reveal] {
                opacity: 1;
                transform: none;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $routeCount = \App\Models\DrivingRoute::where('is_active', true)->count();
        $cityCount = \App\Models\City::whereHas('routes', fn ($query) => $query->where('is_active', true))->count();
        $customerCount = \App\Models\RoutePurchase::where('payment_status', 'paid')->distinct('user_id')->count('user_id');
    @endphp

    <div class="public-dark-page">
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="max-w-4xl" data-page-reveal>
                <p class="text-sm font-black uppercase text-cyan-200">About us</p>
                <h1 class="mt-4 text-5xl font-black leading-tight text-white sm:text-6xl">
                    Route preparation with
                    <span class="public-gradient-text block">clearer expectations.</span>
                </h1>
                <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-400">
                    Driver Test Routes helps learners and instructors plan focused practice with paid route maps, controlled starts, and simple dashboards for repeatable test-day preparation.
                </p>
            </div>
        </section>

        <section class="border-y border-white/10">
            <div class="mx-auto grid max-w-7xl gap-5 px-4 py-14 sm:px-6 md:grid-cols-4 lg:px-8">
                @foreach([
                    ['label' => 'Years building route tools', 'value' => 5],
                    ['label' => 'Cities served', 'value' => $cityCount],
                    ['label' => 'Active routes', 'value' => $routeCount],
                    ['label' => 'Customers supported', 'value' => $customerCount],
                ] as $stat)
                    <div class="public-glass-card p-5" data-page-reveal style="--delay: {{ $loop->index * 80 }}ms;">
                        <p class="text-sm font-semibold text-slate-400">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-4xl font-black text-white" data-about-counter data-target="{{ $stat['value'] }}">0</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-[.85fr_1.15fr] lg:px-8">
            <div data-page-reveal>
                <p class="text-sm font-black uppercase text-cyan-200">Mission</p>
                <h2 class="mt-3 text-3xl font-black text-white">Professional route practice without guesswork.</h2>
                <p class="mt-5 text-base leading-8 text-slate-400">
                    We organize route details, access, and map starts so every learner can practice deliberately. The platform is built around clear locations, measurable use, and a clean account workflow.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach([
                    ['title' => 'Route-first catalog', 'copy' => 'Every route centers the city, start area, destination, schedule notes, and practice limits.'],
                    ['title' => 'Controlled access', 'copy' => 'Purchased routes unlock for signed-in users, with starts tracked for intentional practice.'],
                    ['title' => 'Instructor-ready workflow', 'copy' => 'Routes, purchases, and city coverage stay organized in the dashboard.'],
                    ['title' => 'Coverage growth', 'copy' => 'The city system makes it straightforward to add new test areas as the catalog expands.'],
                ] as $card)
                    <article class="public-glass-card p-5" data-page-reveal style="--delay: {{ $loop->index * 80 }}ms;">
                        <h3 class="text-lg font-black text-white">{{ $card['title'] }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-400">{{ $card['copy'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const reveals = document.querySelectorAll('[data-page-reveal]');
            const counters = document.querySelectorAll('[data-about-counter]');
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function animateCounter(counter) {
                const target = Number(counter.dataset.target || 0);

                if (prefersReducedMotion) {
                    counter.textContent = target.toLocaleString();
                    return;
                }

                let start = null;
                const duration = 900;

                function tick(timestamp) {
                    start ??= timestamp;
                    const progress = Math.min(1, (timestamp - start) / duration);
                    counter.textContent = Math.round(target * progress).toLocaleString();

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    }
                }

                requestAnimationFrame(tick);
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');

                    if (entry.target.hasAttribute('data-about-counter') && !entry.target.dataset.counted) {
                        entry.target.dataset.counted = 'true';
                        animateCounter(entry.target);
                    }

                    observer.unobserve(entry.target);
                });
            }, { threshold: .18 });

            reveals.forEach((element) => observer.observe(element));
            counters.forEach((element) => observer.observe(element));
        })();
    </script>
@endpush
