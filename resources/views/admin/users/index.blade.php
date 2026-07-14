@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">User Management</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900 tracking-tight">Users</h1>
                <p class="mt-2 text-sm text-slate-500">Customers, administrators, route purchases, and paid map-start usage.</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/70 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Role</th>
                            <th class="px-5 py-3">Paid Routes</th>
                            <th class="px-5 py-3">Starts</th>
                            <th class="px-5 py-3">Remaining</th>
                            <th class="px-5 py-3">Total Spent</th>
                            <th class="px-5 py-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($users as $user)
                            @php
                                $paidPurchases = $user->routePurchases;
                                $startsUsed = $paidPurchases->sum('access_used');
                                $startsLimit = $paidPurchases->sum('access_limit');
                                $remainingStarts = max(0, $startsLimit - $startsUsed);
                                $totalSpent = $paidPurchases->sum(fn ($purchase) => (float) $purchase->amount_paid);
                            @endphp
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-blue-50 to-cyan-50/80 text-sm font-black text-blue-700 ring-1 ring-blue-100/50">
                                            {{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 leading-tight">{{ $user->name }}</div>
                                            <div class="mt-0.5 text-xs text-slate-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-blue-600/10">Admin</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Customer</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-700 font-semibold">{{ $paidPurchases->count() }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 font-semibold text-slate-900">
                                        <span>{{ $startsUsed }}</span>
                                        <span class="text-slate-300">/</span>
                                        <span class="text-slate-500">{{ $startsLimit }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($remainingStarts > 0)
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/10">{{ $remainingStarts }} left</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">None</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-900">${{ number_format($totalSpent, 2) }}</td>
                                <td class="px-5 py-4 text-slate-500 font-medium">{{ $user->created_at?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-500">No users have registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </section>
@endsection
