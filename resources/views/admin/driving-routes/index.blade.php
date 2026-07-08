@extends('layouts.admin')

@section('title', 'Admin Routes')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-stone-950">Admin Routes</h1>
                <p class="mt-2 text-stone-600">Manage driving test route maps, waypoints, instructions, pricing, and previews.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-md border border-stone-300 px-4 py-2 font-semibold text-stone-700 hover:bg-stone-100">
                    Dashboard
                </a>
                <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center justify-center rounded-md border border-stone-300 px-4 py-2 font-semibold text-stone-700 hover:bg-stone-100">
                    Purchases
                </a>
                <a href="{{ route('admin.driving-routes.create') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-semibold text-white hover:bg-emerald-800">
                    Add Route
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-100 text-left text-xs font-semibold uppercase tracking-normal text-stone-600">
                        <tr>
                            <th class="px-4 py-3">Route</th>
                            <th class="px-4 py-3">City</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Starts</th>
                            <th class="px-4 py-3">Summary</th>
                            <th class="px-4 py-3">Points</th>
                            <th class="px-4 py-3">Sales</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse($routes as $drivingRoute)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-stone-950">{{ $drivingRoute->title }}</div>
                                    <div class="mt-1 text-xs text-stone-500">{{ $drivingRoute->start_label }} to {{ $drivingRoute->destination_label }}</div>
                                </td>
                                <td class="px-4 py-4 text-stone-700">
                                    <div>{{ $drivingRoute->cityModel?->name ?? $drivingRoute->city }}, {{ $drivingRoute->province }}</div>
                                    @if($drivingRoute->cityModel?->address)
                                        <div class="mt-1 max-w-xs text-xs text-stone-500">{{ $drivingRoute->cityModel->address }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-semibold text-stone-950">${{ number_format((float) $drivingRoute->price, 2) }}</td>
                                <td class="px-4 py-4 text-stone-700">{{ $drivingRoute->access_limit ?? 1 }}</td>
                                <td class="px-4 py-4 text-stone-700">
                                    @if($drivingRoute->route_duration_minutes || $drivingRoute->route_length_km)
                                        {{ $drivingRoute->route_duration_minutes ? $drivingRoute->route_duration_minutes.' mins' : '' }}
                                        {{ $drivingRoute->route_duration_minutes && $drivingRoute->route_length_km ? ' / ' : '' }}
                                        {{ $drivingRoute->route_length_km ? $drivingRoute->route_length_km.' km' : '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-stone-700">{{ $drivingRoute->points_count }}</td>
                                <td class="px-4 py-4 text-stone-700">{{ $drivingRoute->purchases_count }}</td>
                                <td class="px-4 py-4">
                                    @if($drivingRoute->is_active)
                                        <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800">Active</span>
                                    @else
                                        <span class="rounded-md bg-stone-100 px-2 py-1 text-xs font-semibold text-stone-700">Hidden</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.driving-routes.edit', $drivingRoute) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:bg-stone-100">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.driving-routes.destroy', $drivingRoute) }}" onsubmit="return confirm('Delete this route?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-stone-600">No routes have been added yet.</td>
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
