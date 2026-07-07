@extends('layouts.app')

@section('title', 'Driver Test Routes')

@section('content')
    <section class="relative overflow-hidden bg-zinc-950 text-white">
        <div class="absolute inset-0 opacity-25" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 64px 64px;"></div>
        <div class="absolute inset-x-0 bottom-0 h-px bg-white/10"></div>

        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 pb-14 pt-28 sm:px-6 sm:pb-16 sm:pt-32 lg:grid-cols-[.9fr_1.1fr] lg:items-center lg:px-8 lg:pb-20 lg:pt-32">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-normal text-emerald-300">Professional paid route maps</p>
                <h1 class="mt-5 text-4xl font-bold tracking-normal text-white sm:text-6xl">Driver Test Routes</h1>
                <p class="mt-6 text-lg leading-8 text-zinc-200">
                    Buy the driving test route you need, open a clear practice map, and rehearse with controlled live starts before your appointment.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('driving-routes.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-500 px-5 py-3 font-bold text-zinc-950 shadow-sm transition hover:bg-emerald-400">
                        Browse Routes
                    </a>
                    @auth
                        <a href="{{ route('driving-routes.my') }}" class="inline-flex items-center justify-center rounded-md border border-white/25 px-5 py-3 font-bold text-white transition hover:bg-white/10">
                            My Routes
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md border border-white/25 px-5 py-3 font-bold text-white transition hover:bg-white/10">
                            Create Account
                        </a>
                    @endauth
                </div>

                <dl class="mt-10 grid max-w-2xl gap-3 sm:grid-cols-3">
                    <div class="rounded-md border border-white/15 bg-white/[.07] p-4 backdrop-blur">
                        <dt class="text-sm text-zinc-300">Active routes</dt>
                        <dd class="mt-1 text-3xl font-bold text-white">{{ number_format($stats['routes']) }}</dd>
                    </div>
                    <div class="rounded-md border border-white/15 bg-white/[.07] p-4 backdrop-blur">
                        <dt class="text-sm text-zinc-300">Cities covered</dt>
                        <dd class="mt-1 text-3xl font-bold text-white">{{ number_format($stats['cities']) }}</dd>
                    </div>
                    <div class="rounded-md border border-white/15 bg-white/[.07] p-4 backdrop-blur">
                        <dt class="text-sm text-zinc-300">Map starts used</dt>
                        <dd class="mt-1 text-3xl font-bold text-white">{{ number_format($stats['starts']) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="hero-panel-rise rounded-lg border border-white/15 bg-white/[.08] p-3 shadow-2xl backdrop-blur">
                <div class="relative overflow-hidden rounded-md bg-white text-zinc-950 shadow-xl">
                    <div class="absolute inset-x-0 top-0 h-1 bg-emerald-500"></div>

                    <div class="relative min-h-[27rem] overflow-hidden bg-zinc-950 p-5 text-white">
                        <div class="absolute inset-0 opacity-30" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>
                        <div class="hero-scan-line absolute inset-y-0 w-1/3 bg-white/10"></div>

                        <div class="relative flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-normal text-emerald-300">Practice readiness</p>
                                <h2 class="mt-1 text-xl font-bold text-white">Route prep dashboard</h2>
                            </div>
                            <span class="inline-flex items-center gap-2 rounded-md border border-emerald-400/25 bg-emerald-400/10 px-3 py-1.5 text-xs font-bold text-emerald-200">
                                <span class="hero-status-dot h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                Online
                            </span>
                        </div>

                        <div class="relative mt-8 grid gap-4 md:grid-cols-[.9fr_1.1fr]">
                            <div class="hero-float rounded-lg border border-white/10 bg-white/10 p-5 backdrop-blur">
                                <div class="mx-auto grid h-36 w-36 place-items-center">
                                    <svg class="h-36 w-36 -rotate-90" viewBox="0 0 80 80" role="img" aria-label="Animated readiness score">
                                        <circle cx="40" cy="40" r="30" fill="none" stroke="rgba(255,255,255,.14)" stroke-width="8" />
                                        <circle class="hero-meter-ring" cx="40" cy="40" r="30" fill="none" stroke="#34d399" stroke-width="8" stroke-linecap="round" />
                                    </svg>
                                    <div class="absolute text-center">
                                        <p class="text-4xl font-black text-white">92</p>
                                        <p class="text-xs font-bold uppercase tracking-normal text-zinc-400">Ready</p>
                                    </div>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-md bg-white/10 p-3">
                                        <p class="text-zinc-400">Focus</p>
                                        <p class="mt-1 font-bold text-white">High</p>
                                    </div>
                                    <div class="rounded-md bg-white/10 p-3">
                                        <p class="text-zinc-400">Session</p>
                                        <p class="mt-1 font-bold text-white">Ready</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="rounded-lg border border-white/10 bg-white/10 p-4 backdrop-blur">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-normal text-zinc-400">Route planning</p>
                                            <h3 class="mt-1 text-lg font-bold text-white">Practice queue</h3>
                                        </div>
                                        <span class="rounded-md bg-sky-400/15 px-3 py-1 text-xs font-bold text-sky-200">3 steps</span>
                                    </div>

                                    <div class="mt-5 space-y-4">
                                        <div>
                                            <div class="flex justify-between text-xs text-zinc-400">
                                                <span>Area review</span>
                                                <span>78%</span>
                                            </div>
                                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/10">
                                                <div class="hero-progress-fill h-full rounded-full bg-emerald-400"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between text-xs text-zinc-400">
                                                <span>Map unlock</span>
                                                <span>Ready</span>
                                            </div>
                                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/10">
                                                <div class="hero-progress-fill h-full rounded-full bg-sky-400" style="animation-delay: -1.4s;"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between text-xs text-zinc-400">
                                                <span>Live start</span>
                                                <span>Queued</span>
                                            </div>
                                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/10">
                                                <div class="hero-progress-fill h-full rounded-full bg-violet-400" style="animation-delay: -2.1s;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="rounded-lg border border-white/10 bg-white/10 p-4 backdrop-blur">
                                        <p class="text-xs font-bold uppercase tracking-normal text-zinc-400">Coverage</p>
                                        <p class="mt-2 text-3xl font-black text-white">{{ number_format($stats['cities']) }}</p>
                                        <p class="mt-1 text-xs text-zinc-400">cities</p>
                                    </div>
                                    <div class="rounded-lg border border-white/10 bg-white/10 p-4 backdrop-blur">
                                        <p class="text-xs font-bold uppercase tracking-normal text-zinc-400">Catalog</p>
                                        <p class="mt-2 text-3xl font-black text-white">{{ number_format($stats['routes']) }}</p>
                                        <p class="mt-1 text-xs text-zinc-400">active routes</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative mt-4 rounded-lg border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <p class="text-xs text-zinc-400">Browse</p>
                                    <p class="mt-1 font-bold text-white">Compare areas</p>
                                </div>
                                <div>
                                    <p class="text-xs text-zinc-400">Unlock</p>
                                    <p class="mt-1 font-bold text-white">Buy access</p>
                                </div>
                                <div>
                                    <p class="text-xs text-zinc-400">Practice</p>
                                    <p class="mt-1 font-bold text-white">Start live</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-zinc-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3">
                <article class="section-rise soft-sheen relative overflow-hidden rounded-lg border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="grid h-11 w-11 place-items-center rounded-md bg-emerald-50 text-lg font-black text-emerald-700">01</div>
                    <h2 class="mt-5 text-lg font-bold text-zinc-950">Clear route details</h2>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">Compare location, start point, destination, waypoints, duration, and included starts before buying.</p>
                </article>
                <article class="section-rise soft-sheen relative overflow-hidden rounded-lg border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg" style="animation-delay: .1s;">
                    <div class="grid h-11 w-11 place-items-center rounded-md bg-sky-50 text-lg font-black text-sky-700">02</div>
                    <h2 class="mt-5 text-lg font-bold text-zinc-950">Paid map access</h2>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">Unlock only the route you need and keep your purchased maps available inside your account.</p>
                </article>
                <article class="section-rise soft-sheen relative overflow-hidden rounded-lg border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg" style="animation-delay: .2s;">
                    <div class="grid h-11 w-11 place-items-center rounded-md bg-violet-50 text-lg font-black text-violet-700">03</div>
                    <h2 class="mt-5 text-lg font-bold text-zinc-950">Practice tracking</h2>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">Use live start counts to plan focused practice sessions without losing track of access.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="relative overflow-hidden bg-zinc-50">
        <div class="absolute right-[-4rem] top-16 h-1 w-80 rotate-[-18deg] bg-emerald-200/70"></div>
        <div class="absolute left-[-5rem] bottom-24 h-1 w-72 rotate-[16deg] bg-sky-200/70"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-9 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Featured coverage</p>
                    <h2 class="mt-2 text-3xl font-bold text-zinc-950">Featured Routes</h2>
                    <p class="mt-2 max-w-2xl text-zinc-600">Review price, included starts, route duration, and practice readiness before checkout.</p>
                </div>
                <a href="{{ route('driving-routes.index') }}" class="inline-flex items-center justify-center rounded-md border border-zinc-300 bg-white px-4 py-2 text-sm font-bold text-zinc-800 shadow-sm transition hover:-translate-y-0.5 hover:bg-zinc-100">View all routes</a>
            </div>

            @if($featuredRoutes->isEmpty())
                <div class="rounded-lg border border-dashed border-zinc-300 bg-white px-6 py-12 text-center">
                    <h2 class="text-lg font-bold text-zinc-950">No routes available</h2>
                    <p class="mt-2 text-sm text-zinc-600">Add active routes from the admin dashboard.</p>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($featuredRoutes as $drivingRoute)
                        <article class="section-rise group relative flex min-h-80 flex-col justify-between overflow-hidden rounded-lg border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl" style="animation-delay: {{ $loop->index * 0.06 }}s;">
                            <div class="absolute inset-x-0 top-0 h-1 bg-zinc-950 transition group-hover:bg-emerald-600"></div>
                            <div>
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800">Available now</span>
                                        <h3 class="mt-3 text-xl font-bold text-zinc-950">{{ $drivingRoute->title }}</h3>
                                        <p class="mt-1 text-sm text-zinc-600">{{ $drivingRoute->city }}, {{ $drivingRoute->province }}</p>
                                    </div>
                                    <div class="shrink-0 rounded-md bg-zinc-950 px-3 py-2 text-right text-white">
                                        <div class="text-xs text-zinc-300">Price</div>
                                        <div class="font-bold">${{ number_format((float) $drivingRoute->price, 2) }}</div>
                                    </div>
                                </div>

                                <p class="mt-5 line-clamp-3 text-sm leading-6 text-zinc-600">
                                    {{ $drivingRoute->description ?: 'Practice with a paid route map, live location tracking, and route confidence before test day.' }}
                                </p>

                                <dl class="mt-5 divide-y divide-zinc-100 border-y border-zinc-100 text-sm">
                                    <div class="flex items-center justify-between gap-3 py-2.5">
                                        <dt class="text-zinc-500">Starts included</dt>
                                        <dd class="font-bold text-zinc-950">{{ $drivingRoute->access_limit ?? 1 }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-3 py-2.5">
                                        <dt class="text-zinc-500">Estimated time</dt>
                                        <dd class="font-bold text-zinc-950">{{ $drivingRoute->route_duration_minutes ? $drivingRoute->route_duration_minutes.' mins' : 'Ready' }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-3 py-2.5">
                                        <dt class="text-zinc-500">Route points</dt>
                                        <dd class="font-bold text-zinc-950">{{ $drivingRoute->points_count }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="mt-5">
                                @auth
                                    <a href="{{ route('driving-routes.checkout', $drivingRoute) }}" class="inline-flex w-full items-center justify-center rounded-md bg-emerald-700 px-4 py-2.5 font-bold text-white transition hover:bg-emerald-800">
                                        Buy Route
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-md bg-emerald-700 px-4 py-2.5 font-bold text-white transition hover:bg-emerald-800">
                                        Login to Buy
                                    </a>
                                @endauth
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="relative overflow-hidden bg-zinc-950 text-white">
        <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(rgba(255,255,255,.14) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.14) 1px, transparent 1px); background-size: 52px 52px;"></div>

        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[.85fr_1.15fr] lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-normal text-emerald-300">How it works</p>
                <h2 class="mt-3 text-3xl font-bold text-white">A focused workflow from search to practice.</h2>
                <p class="mt-4 max-w-xl text-zinc-300">Browse the catalog, buy the route you need, and return to your purchased maps whenever you are ready to practice.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <article class="section-rise rounded-lg border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <div class="grid h-11 w-11 place-items-center rounded-md bg-emerald-400 font-black text-zinc-950">1</div>
                    <h3 class="mt-5 text-lg font-bold text-white">Choose</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-300">Find your city and compare practical route details.</p>
                    <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-white/10">
                        <div class="hero-progress-fill h-full rounded-full bg-emerald-400"></div>
                    </div>
                </article>
                <article class="section-rise rounded-lg border border-white/10 bg-white/10 p-5 backdrop-blur" style="animation-delay: .1s;">
                    <div class="grid h-11 w-11 place-items-center rounded-md bg-sky-400 font-black text-zinc-950">2</div>
                    <h3 class="mt-5 text-lg font-bold text-white">Unlock</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-300">Purchase map access and included starts.</p>
                    <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-white/10">
                        <div class="hero-progress-fill h-full rounded-full bg-sky-400" style="animation-delay: -1.2s;"></div>
                    </div>
                </article>
                <article class="section-rise rounded-lg border border-white/10 bg-white/10 p-5 backdrop-blur" style="animation-delay: .2s;">
                    <div class="grid h-11 w-11 place-items-center rounded-md bg-violet-400 font-black text-zinc-950">3</div>
                    <h3 class="mt-5 text-lg font-bold text-white">Practice</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-300">Open the live route and rehearse with purpose.</p>
                    <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-white/10">
                        <div class="hero-progress-fill h-full rounded-full bg-violet-400" style="animation-delay: -2.2s;"></div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="section-rise flex flex-col gap-5 rounded-lg border border-zinc-200 bg-zinc-950 p-6 text-white shadow-xl sm:p-8 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-emerald-300">Ready when you are</p>
                    <h2 class="mt-2 text-2xl font-bold">Start with the route catalog.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-300">Compare route details, unlock the map you need, and keep your practice organized from one account.</p>
                </div>
                <a href="{{ route('driving-routes.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-500 px-5 py-3 font-bold text-zinc-950 shadow-sm transition hover:bg-emerald-400">
                    Browse Routes
                </a>
            </div>
        </div>
    </section>
@endsection
