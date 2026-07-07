@extends('layouts.app')

@section('title', 'Routes')

@section('content')
    <section class="border-b border-zinc-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Route catalog</p>
                    <h1 class="mt-2 text-4xl font-bold tracking-normal text-zinc-950">Driving Test Routes</h1>
                    <p class="mt-3 max-w-2xl text-zinc-600">Browse paid route maps, compare pricing, and unlock limited map starts for your test area.</p>
                </div>

                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.driving-routes.create') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-bold text-white transition hover:bg-emerald-800">
                            Add Route
                        </a>
                    @endif
                @endauth
            </div>

            @if($routes->isNotEmpty())
                @php
                    $cities = $routes->pluck('city')->filter()->unique()->take(6);
                    $lowestPrice = $routes->min('price');
                @endphp
                <dl class="mt-8 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
                        <dt class="text-sm text-zinc-500">Available routes</dt>
                        <dd class="mt-1 text-2xl font-bold text-zinc-950">{{ number_format($routes->count()) }}</dd>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
                        <dt class="text-sm text-zinc-500">Cities</dt>
                        <dd class="mt-1 text-2xl font-bold text-zinc-950">{{ number_format($routes->pluck('city')->filter()->unique()->count()) }}</dd>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
                        <dt class="text-sm text-zinc-500">Starting from</dt>
                        <dd class="mt-1 text-2xl font-bold text-zinc-950">${{ number_format((float) $lowestPrice, 2) }}</dd>
                    </div>
                </dl>

                @if($cities->isNotEmpty())
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($cities as $city)
                            <span class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-sm font-semibold text-zinc-700">{{ $city }}</span>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-zinc-950">All Routes</h2>
                <p class="mt-2 max-w-2xl text-zinc-600">Open your purchased routes immediately, or sign in to unlock a new practice map.</p>
            </div>
        </div>

        @if($routes->isEmpty())
            <div class="rounded-lg border border-dashed border-zinc-300 bg-white px-6 py-12 text-center">
                <h2 class="text-lg font-bold text-zinc-950">No routes available</h2>
                <p class="mt-2 text-sm text-zinc-600">An admin can add the first route from the admin panel.</p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($routes as $drivingRoute)
                    @php
                        $purchase = $purchases->get($drivingRoute->id);
                        $remainingStarts = $purchase?->remainingStarts() ?? 0;
                        $canOpenMap = auth()->user()?->is_admin || $remainingStarts > 0;
                    @endphp
                    <article class="flex min-h-72 flex-col justify-between rounded-lg border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div>
                            <div class="mb-4 flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-xl font-bold text-zinc-950">{{ $drivingRoute->title }}</h3>
                                    <p class="mt-1 text-sm text-zinc-600">{{ $drivingRoute->city }}, {{ $drivingRoute->province }}</p>
                                </div>
                                <div class="shrink-0 rounded-md bg-zinc-950 px-3 py-2 text-right text-white">
                                    <div class="text-xs text-zinc-300">Price</div>
                                    <div class="font-bold">${{ number_format((float) $drivingRoute->price, 2) }}</div>
                                </div>
                            </div>

                            @if($drivingRoute->description)
                                <p class="line-clamp-3 text-sm leading-6 text-zinc-600">{{ $drivingRoute->description }}</p>
                            @endif

                            <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-md bg-zinc-50 p-3">
                                    <dt class="font-medium text-zinc-500">Start</dt>
                                    <dd class="mt-1 font-bold text-zinc-900">{{ $drivingRoute->start_label ?: 'Start point' }}</dd>
                                </div>
                                <div class="rounded-md bg-zinc-50 p-3">
                                    <dt class="font-medium text-zinc-500">Destination</dt>
                                    <dd class="mt-1 font-bold text-zinc-900">{{ $drivingRoute->destination_label ?: 'Destination' }}</dd>
                                </div>
                                <div class="rounded-md bg-zinc-50 p-3">
                                    <dt class="font-medium text-zinc-500">Waypoints</dt>
                                    <dd class="mt-1 font-bold text-zinc-900">{{ $drivingRoute->points_count }}</dd>
                                </div>
                                <div class="rounded-md bg-zinc-50 p-3">
                                    <dt class="font-medium text-zinc-500">Starts Included</dt>
                                    <dd class="mt-1 font-bold text-zinc-900">{{ $drivingRoute->access_limit ?? 1 }}</dd>
                                </div>
                                <div class="rounded-md bg-zinc-50 p-3">
                                    <dt class="font-medium text-zinc-500">Duration</dt>
                                    <dd class="mt-1 font-bold text-zinc-900">
                                        {{ $drivingRoute->route_duration_minutes ? $drivingRoute->route_duration_minutes.' mins' : 'Ready' }}
                                    </dd>
                                </div>
                                <div class="rounded-md bg-zinc-50 p-3">
                                    <dt class="font-medium text-zinc-500">Length</dt>
                                    <dd class="mt-1 font-bold text-zinc-900">
                                        {{ $drivingRoute->route_length_km ? $drivingRoute->route_length_km.' km' : 'Route' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            @if($canOpenMap)
                                <a href="{{ route('driving-routes.show', $drivingRoute) }}" class="inline-flex flex-1 items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-bold text-white transition hover:bg-emerald-800">
                                    Open Map
                                </a>
                                @if(! auth()->user()?->is_admin)
                                    <span class="rounded-md bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-800">
                                        {{ $remainingStarts }} left
                                    </span>
                                @endif
                            @elseif(auth()->check())
                                <a href="{{ route('driving-routes.checkout', $drivingRoute) }}" class="inline-flex flex-1 items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-bold text-white transition hover:bg-emerald-800">
                                    {{ $purchase ? 'Buy More Starts' : 'Buy Route' }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex flex-1 items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-bold text-white transition hover:bg-emerald-800">
                                    Login to Buy
                                </a>
                            @endif

                            @if($drivingRoute->preview_pdf_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($drivingRoute->preview_pdf_path) }}" target="_blank" class="rounded-md border border-zinc-300 px-4 py-2 font-bold text-zinc-700 transition hover:bg-zinc-100">
                                    PDF
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
