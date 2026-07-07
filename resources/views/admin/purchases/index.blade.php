@extends('layouts.admin')

@section('title', 'Admin Purchases')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-stone-950">Purchases</h1>
                <p class="mt-2 text-stone-600">Paid checkout records, route access limits, and map-start usage.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-md border border-stone-300 px-4 py-2 font-semibold text-stone-700 hover:bg-stone-100">
                    Dashboard
                </a>
                <a href="{{ route('admin.driving-routes.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-4 py-2 font-semibold text-white hover:bg-emerald-800">
                    Manage Routes
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-100 text-left text-xs font-semibold uppercase tracking-normal text-stone-600">
                        <tr>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Route</th>
                            <th class="px-4 py-3">Payment</th>
                            <th class="px-4 py-3">Starts</th>
                            <th class="px-4 py-3">Remaining</th>
                            <th class="px-4 py-3">Last Access</th>
                            <th class="px-4 py-3">Purchased</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-stone-950">{{ $purchase->student_name ?: ($purchase->user?->name ?? 'Deleted user') }}</div>
                                    <div class="mt-1 text-xs text-stone-500">{{ $purchase->student_email ?: $purchase->user?->email }}</div>
                                    @if($purchase->student_phone)
                                        <div class="mt-1 text-xs text-stone-500">{{ $purchase->student_phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-stone-950">{{ $purchase->route?->title ?? 'Deleted route' }}</div>
                                    <div class="mt-1 text-xs text-stone-500">{{ $purchase->route?->city }}{{ $purchase->route?->province ? ', '.$purchase->route?->province : '' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-stone-950">${{ number_format((float) $purchase->amount_paid, 2) }}</div>
                                    <div class="mt-1 text-xs text-stone-500">{{ ucfirst($purchase->payment_provider ?? 'local') }} {{ $purchase->payment_id ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-4 text-stone-700">{{ $purchase->access_used }} / {{ $purchase->access_limit }}</td>
                                <td class="px-4 py-4">
                                    @if($purchase->remainingStarts() > 0)
                                        <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800">{{ $purchase->remainingStarts() }} left</span>
                                    @else
                                        <span class="rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">Expired</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-stone-700">{{ $purchase->last_accessed_at?->format('M j, Y g:i A') ?? '-' }}</td>
                                <td class="px-4 py-4 text-stone-700">{{ $purchase->purchased_at?->format('M j, Y g:i A') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-stone-600">No purchases have been recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $purchases->links() }}
        </div>
    </section>
@endsection
