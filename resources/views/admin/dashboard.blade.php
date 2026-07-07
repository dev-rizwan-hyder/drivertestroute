@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-stone-950">Admin Dashboard</h1>
                <p class="mt-2 text-stone-600">Sales, route access, and map-start usage at a glance.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.driving-routes.index') }}" class="inline-flex items-center justify-center rounded-md border border-stone-300 px-4 py-2 font-semibold text-stone-700 hover:bg-stone-100">
                    Manage Routes
                </a>
                <a href="{{ route('admin.driving-routes.create') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-semibold text-white hover:bg-emerald-800">
                    Add Route
                </a>
            </div>
        </div>

        <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-stone-500">Total Routes</dt>
                <dd class="mt-2 text-3xl font-bold text-stone-950">{{ number_format($stats['routes']) }}</dd>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-stone-500">Active Routes</dt>
                <dd class="mt-2 text-3xl font-bold text-stone-950">{{ number_format($stats['active_routes']) }}</dd>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-stone-500">Paid Purchases</dt>
                <dd class="mt-2 text-3xl font-bold text-stone-950">{{ number_format($stats['sales']) }}</dd>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-stone-500">Revenue</dt>
                <dd class="mt-2 text-3xl font-bold text-stone-950">${{ number_format((float) $stats['revenue'], 2) }}</dd>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-stone-500">Starts Used</dt>
                <dd class="mt-2 text-3xl font-bold text-stone-950">{{ number_format($stats['starts_used']) }}</dd>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-stone-500">Users</dt>
                <dd class="mt-2 text-3xl font-bold text-stone-950">{{ number_format($stats['users']) }}</dd>
            </div>
        </dl>

        <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <section class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-stone-200 p-5">
                    <div>
                        <h2 class="text-lg font-semibold text-stone-950">Recent Purchases</h2>
                        <p class="mt-1 text-sm text-stone-600">Latest paid checkout records and remaining starts.</p>
                    </div>
                    <a href="{{ route('admin.purchases.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">View all</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-100 text-left text-xs font-semibold uppercase tracking-normal text-stone-600">
                            <tr>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Route</th>
                                <th class="px-4 py-3">Paid</th>
                                <th class="px-4 py-3">Access</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @forelse($recentPurchases as $purchase)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-stone-950">{{ $purchase->student_name ?: ($purchase->user?->name ?? 'Deleted user') }}</div>
                                        <div class="mt-1 text-xs text-stone-500">{{ $purchase->student_email ?: $purchase->user?->email }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-stone-700">{{ $purchase->route?->title ?? 'Deleted route' }}</td>
                                    <td class="px-4 py-4 font-semibold text-stone-950">${{ number_format((float) $purchase->amount_paid, 2) }}</td>
                                    <td class="px-4 py-4 text-stone-700">{{ $purchase->access_used }} / {{ $purchase->access_limit }}</td>
                                    <td class="px-4 py-4 text-stone-700">{{ $purchase->purchased_at?->format('M j, Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-stone-600">No paid purchases yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <aside class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-stone-950">Top Routes</h2>
                <div class="mt-5 space-y-4">
                    @forelse($topRoutes as $route)
                        <div class="rounded-md border border-stone-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-stone-950">{{ $route->title }}</h3>
                                    <p class="mt-1 text-sm text-stone-600">{{ $route->city }}, {{ $route->province }}</p>
                                </div>
                                <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-sm font-semibold text-emerald-800">{{ $route->paid_purchases_count }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-stone-600">No route sales yet.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>
@endsection
