@extends('layouts.app')

@section('title', 'My Routes')

@push('styles')
    <style>
        .my-routes-page {
            min-height: calc(100vh - 5rem);
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

        .my-routes-empty {
            border-color: rgba(203, 213, 225, .9);
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
        }
    </style>
@endpush

@section('content')
    <div class="my-routes-page">
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">My Routes</h1>
            <p class="mt-2 text-slate-400">Purchased driving routes, remaining map starts, and live map access.</p>
        </div>

        @if($purchases->isEmpty())
            <div class="my-routes-empty rounded-lg border border-dashed px-6 py-12 text-center">
                <h2 class="text-lg font-semibold text-white">No purchased routes</h2>
                <a href="{{ route('driving-routes.index') }}" class="mt-4 inline-flex rounded-md bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">
                    Browse Routes
                </a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($purchases as $purchase)
                    @php
                        $drivingRoute = $purchase->route;
                        $remainingStarts = $purchase->remainingStarts();
                    @endphp
                    <article class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-semibold text-stone-950">{{ $drivingRoute->title }}</h2>
                                <p class="mt-1 text-sm text-stone-600">{{ $drivingRoute->city }}, {{ $drivingRoute->province }}</p>
                            </div>
                            @if($remainingStarts > 0)
                                <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-sm font-semibold text-emerald-800">{{ $remainingStarts }} left</span>
                            @else
                                <span class="rounded-md bg-red-50 px-2.5 py-1 text-sm font-semibold text-red-700">No starts</span>
                            @endif
                        </div>

                        <div class="mt-5 space-y-3 text-sm text-stone-700">
                            <p><span class="font-semibold text-stone-950">Start:</span> {{ $drivingRoute->start_label ?: 'Start point' }}</p>
                            <p><span class="font-semibold text-stone-950">Destination:</span> {{ $drivingRoute->destination_label ?: 'Destination' }}</p>
                            <p><span class="font-semibold text-stone-950">Waypoints:</span> {{ $drivingRoute->points->count() }}</p>
                            <p><span class="font-semibold text-stone-950">Starts used:</span> {{ $purchase->access_used }} / {{ $purchase->access_limit }}</p>
                            <p><span class="font-semibold text-stone-950">Paid:</span> ${{ number_format((float) $purchase->amount_paid, 2) }}</p>
                            @if($purchase->last_accessed_at)
                                <p><span class="font-semibold text-stone-950">Last access:</span> {{ $purchase->last_accessed_at->format('M j, Y g:i A') }}</p>
                            @endif
                            @if($drivingRoute->route_duration_minutes)
                                <p><span class="font-semibold text-stone-950">Duration:</span> {{ $drivingRoute->route_duration_minutes }} mins</p>
                            @endif
                            @if($drivingRoute->route_length_km)
                                <p><span class="font-semibold text-stone-950">Length:</span> {{ $drivingRoute->route_length_km }} km</p>
                            @endif
                        </div>

                        <div class="mt-5 flex flex-wrap gap-2">
                            @if($remainingStarts > 0)
                                <a href="{{ route('driving-routes.show', $drivingRoute) }}" 
                                   onclick="return confirmOpenMap(event, {{ $remainingStarts }});"
                                   class="inline-flex flex-1 items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-semibold text-white hover:bg-emerald-800">
                                    Open Map
                                </a>
                            @else
                                <a href="{{ route('driving-routes.checkout', $drivingRoute) }}" class="inline-flex flex-1 items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-semibold text-white hover:bg-emerald-800">
                                    Buy More Starts
                                </a>
                            @endif

                            @if($drivingRoute->preview_pdf_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($drivingRoute->preview_pdf_path) }}" target="_blank" class="rounded-md border border-stone-300 px-4 py-2 font-semibold text-stone-700 hover:bg-stone-100">
                                    PDF
                                </a>
                            @endif
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
        function confirmOpenMap(event, remainingStarts) {
            if (@json(auth()->user()?->is_admin ?? false)) {
                return true;
            }
            const message = `Opening this map will consume 1 of your map starts. You have ${remainingStarts} starts remaining.\n\nOnce opened, this page counts as accessed. If you refresh or exit, you will need another start to open it again.\n\nDo you want to proceed?`;
            if (!confirm(message)) {
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
@endpush
