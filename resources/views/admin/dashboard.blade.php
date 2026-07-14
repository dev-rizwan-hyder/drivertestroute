@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-black text-stone-950 tracking-tight">Admin Dashboard</h1>
                <p class="mt-2 text-stone-600">Sales statistics, active route access, and map-start usage at a glance.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.driving-routes.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-700 shadow-sm hover:bg-stone-50 transition">
                    Manage Routes
                </a>
                <a href="{{ route('admin.driving-routes.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition">
                    Add Route
                </a>
            </div>
        </div>

        <dl class="grid gap-5 sm:grid-cols-2 xl:grid-cols-6">
            <!-- Total Routes -->
            <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-5 shadow-sm hover:-translate-y-1 hover:shadow-md hover:border-blue-300 transition duration-300">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-semibold text-stone-500">Total Routes</dt>
                    <div class="rounded-lg bg-blue-50 p-2 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                </div>
                <dd class="mt-4 text-3xl font-black text-stone-950 tracking-tight">{{ number_format($stats['routes']) }}</dd>
            </div>

            <!-- Active Routes -->
            <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-5 shadow-sm hover:-translate-y-1 hover:shadow-md hover:border-blue-300 transition duration-300">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-semibold text-stone-500">Active Routes</dt>
                    <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <dd class="mt-4 text-3xl font-black text-stone-950 tracking-tight">{{ number_format($stats['active_routes']) }}</dd>
            </div>

            <!-- Paid Purchases -->
            <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-5 shadow-sm hover:-translate-y-1 hover:shadow-md hover:border-blue-300 transition duration-300">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-semibold text-stone-500">Paid Purchases</dt>
                    <div class="rounded-lg bg-indigo-50 p-2 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition duration-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                <dd class="mt-4 text-3xl font-black text-stone-950 tracking-tight">{{ number_format($stats['sales']) }}</dd>
            </div>

            <!-- Revenue -->
            <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-5 shadow-sm hover:-translate-y-1 hover:shadow-md hover:border-blue-300 transition duration-300">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-semibold text-stone-500">Revenue</dt>
                    <div class="rounded-lg bg-cyan-50 p-2 text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition duration-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <dd class="mt-4 text-3xl font-black text-stone-950 tracking-tight">${{ number_format((float) $stats['revenue'], 2) }}</dd>
            </div>

            <!-- Starts Used -->
            <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-5 shadow-sm hover:-translate-y-1 hover:shadow-md hover:border-blue-300 transition duration-300">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-semibold text-stone-500">Starts Used</dt>
                    <div class="rounded-lg bg-sky-50 p-2 text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition duration-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <dd class="mt-4 text-3xl font-black text-stone-950 tracking-tight">{{ number_format($stats['starts_used']) }}</dd>
            </div>

            <!-- Users -->
            <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-5 shadow-sm hover:-translate-y-1 hover:shadow-md hover:border-blue-300 transition duration-300">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-semibold text-stone-500">Users</dt>
                    <div class="rounded-lg bg-rose-50 p-2 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition duration-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <dd class="mt-4 text-3xl font-black text-stone-950 tracking-tight">{{ number_format($stats['users']) }}</dd>
            </div>
        </dl>

        <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-stone-200 p-5">
                    <div>
                        <h2 class="text-lg font-bold text-stone-950">Recent Purchases</h2>
                        <p class="mt-1 text-sm text-stone-600">Latest paid checkout records and remaining starts.</p>
                    </div>
                    <a href="{{ route('admin.purchases.index') }}"
                        class="text-sm font-bold text-blue-700 hover:text-blue-800 transition">View all</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-50/70 text-left text-xs font-semibold uppercase tracking-wider text-stone-600">
                            <tr>
                                <th class="px-5 py-3">Customer</th>
                                <th class="px-5 py-3">Route</th>
                                <th class="px-5 py-3">Paid</th>
                                <th class="px-5 py-3">Access</th>
                                <th class="px-5 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @forelse($recentPurchases as $purchase)
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-blue-50 to-cyan-50/80 text-sm font-black text-blue-700 ring-1 ring-blue-100/50">
                                                {{ strtoupper(substr($purchase->student_name ?: $purchase->user?->name ?? 'D', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-stone-950 leading-tight">
                                                    {{ $purchase->student_name ?: $purchase->user?->name ?? 'Deleted user' }}
                                                </div>
                                                <div class="mt-1 text-xs text-stone-500">
                                                    {{ $purchase->student_email ?: $purchase->user?->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-stone-700 font-medium">{{ $purchase->route?->title ?? 'Deleted route' }}</td>
                                    <td class="px-5 py-4 font-bold text-stone-950">
                                        ${{ number_format((float) $purchase->amount_paid, 2) }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-stone-900">{{ $purchase->access_used }}</span>
                                            <span class="text-stone-400">/</span>
                                            <span class="text-stone-500">{{ $purchase->access_limit }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-stone-700">
                                        {{ $purchase->purchased_at?->format('M j, Y') ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-stone-500">No paid purchases yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <aside class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-stone-950 tracking-tight">Top Routes</h2>
                <p class="mt-1 text-xs text-stone-500">Leaderboard of routes by total purchases.</p>
                <div class="mt-5 space-y-3">
                    @forelse($topRoutes as $route)
                        <div class="group flex items-center justify-between gap-4 rounded-xl border border-stone-150 bg-stone-50/30 p-4 hover:bg-white hover:shadow-md hover:border-blue-200 transition duration-200">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition duration-200">
                                    {{ $loop->iteration }}
                                </span>
                                <div>
                                    <h3 class="font-bold text-sm text-stone-950 leading-snug">{{ $route->title }}</h3>
                                    <p class="mt-0.5 text-xs text-stone-500">{{ $route->city }}, {{ $route->province }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-black text-blue-700">{{ $route->paid_purchases_count }}</span>
                                <span class="block text-[10px] font-semibold text-stone-400 uppercase tracking-wider">sales</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">No route sales yet.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>
@endsection
