@extends('layouts.admin')

@section('title', 'Routes')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Route Map Management</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900 tracking-tight">Admin Routes</h1>
                <p class="mt-2 text-sm text-slate-500">Manage driving test route maps, waypoints, instructions, pricing, and previews.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    Dashboard
                </a>
                <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    Purchases
                </a>
                <a href="{{ route('admin.driving-routes.create') }}" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition">
                    Add Route
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/70 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Route</th>
                            <th class="px-5 py-3">City</th>
                            <th class="px-5 py-3">Price</th>
                            <th class="px-5 py-3">Starts</th>
                            <th class="px-5 py-3">Summary</th>
                            <th class="px-5 py-3">Points</th>
                            <th class="px-5 py-3">Sales</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($routes as $drivingRoute)
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 leading-snug">{{ $drivingRoute->title }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">
                                        {{ $drivingRoute->start_label }} to {{ $drivingRoute->destination_label }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $drivingRoute->cityModel?->name ?? $drivingRoute->city }}, {{ $drivingRoute->province }}</div>
                                    @if($drivingRoute->cityModel?->address)
                                        <div class="mt-0.5 max-w-xs text-xs text-slate-400 font-medium truncate">{{ $drivingRoute->cityModel->address }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-900">${{ number_format((float) $drivingRoute->price, 2) }}</td>
                                <td class="px-5 py-4 text-slate-500 font-semibold">{{ $drivingRoute->access_limit ?? 1 }}</td>
                                <td class="px-5 py-4 text-slate-600 font-medium">
                                    @if($drivingRoute->route_duration_minutes || $drivingRoute->route_length_km)
                                        {{ $drivingRoute->route_duration_minutes ? $drivingRoute->route_duration_minutes.' mins' : '' }}
                                        {{ $drivingRoute->route_duration_minutes && $drivingRoute->route_length_km ? ' / ' : '' }}
                                        {{ $drivingRoute->route_length_km ? $drivingRoute->route_length_km.' km' : '' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-bold">{{ $drivingRoute->points_count }}</td>
                                <td class="px-5 py-4 text-slate-500 font-bold">{{ $drivingRoute->purchases_count }}</td>
                                <td class="px-5 py-4">
                                    @if($drivingRoute->is_active)
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-blue-600/10">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Hidden</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.driving-routes.edit', $drivingRoute) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.driving-routes.destroy', $drivingRoute) }}" onsubmit="return confirm('Delete this route?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center text-slate-500">No routes have been added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $routes->links() }}
        </div>
    </section>
@endsection
