@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <section>
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-stone-950">Users</h2>
                <p class="mt-2 text-stone-600">Customers, admins, route purchases, and paid map-start usage.</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-100 text-left text-xs font-semibold uppercase tracking-normal text-stone-600">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Paid Routes</th>
                            <th class="px-4 py-3">Starts</th>
                            <th class="px-4 py-3">Remaining</th>
                            <th class="px-4 py-3">Total Spent</th>
                            <th class="px-4 py-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse($users as $user)
                            @php
                                $paidPurchases = $user->routePurchases;
                                $startsUsed = $paidPurchases->sum('access_used');
                                $startsLimit = $paidPurchases->sum('access_limit');
                                $remainingStarts = max(0, $startsLimit - $startsUsed);
                                $totalSpent = $paidPurchases->sum(fn ($purchase) => (float) $purchase->amount_paid);
                            @endphp
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-stone-950">{{ $user->name }}</div>
                                    <div class="mt-1 text-xs text-stone-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($user->is_admin)
                                        <span class="rounded-md bg-emerald-700 px-2 py-1 text-xs font-semibold text-white">Admin</span>
                                    @else
                                        <span class="rounded-md bg-stone-100 px-2 py-1 text-xs font-semibold text-stone-700">Customer</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-stone-700">{{ $paidPurchases->count() }}</td>
                                <td class="px-4 py-4 text-stone-700">{{ $startsUsed }} / {{ $startsLimit }}</td>
                                <td class="px-4 py-4">
                                    @if($remainingStarts > 0)
                                        <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800">{{ $remainingStarts }} left</span>
                                    @else
                                        <span class="rounded-md bg-stone-100 px-2 py-1 text-xs font-semibold text-stone-700">None</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-semibold text-stone-950">${{ number_format($totalSpent, 2) }}</td>
                                <td class="px-4 py-4 text-stone-700">{{ $user->created_at?->format('M j, Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-stone-600">No users have registered yet.</td>
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
