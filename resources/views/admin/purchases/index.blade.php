@extends('layouts.admin')

@section('title', 'Purchases')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Checkout Records</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900 tracking-tight">Purchases</h1>
                <p class="mt-2 text-sm text-slate-500">Paid checkout records, route access limits, and map-start usage.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    Dashboard
                </a>
                <a href="{{ route('admin.driving-routes.index') }}" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition">
                    Manage Routes
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/70 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Route</th>
                            <th class="px-5 py-3">Payment</th>
                            <th class="px-5 py-3">Starts</th>
                            <th class="px-5 py-3">Remaining</th>
                            <th class="px-5 py-3">Last Access</th>
                            <th class="px-5 py-3">Purchased</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($purchases as $purchase)
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-blue-50 to-cyan-50/80 text-sm font-black text-blue-700 ring-1 ring-blue-100/50">
                                            {{ strtoupper(substr($purchase->student_name ?: $purchase->user?->name ?? 'D', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 leading-tight">
                                                {{ $purchase->student_name ?: ($purchase->user?->name ?? 'Deleted user') }}
                                            </div>
                                            <div class="mt-0.5 text-xs text-slate-500">
                                                {{ $purchase->student_email ?: $purchase->user?->email }}
                                            </div>
                                            @if($purchase->student_phone)
                                                <div class="mt-0.5 text-[10px] text-slate-400 font-semibold">
                                                    {{ $purchase->student_phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 leading-snug">{{ $purchase->route?->title ?? 'Deleted route' }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500 font-medium">
                                        {{ $purchase->route?->city }}{{ $purchase->route?->province ? ', '.$purchase->route?->province : '' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900">${{ number_format((float) $purchase->amount_paid, 2) }}</div>
                                    <div class="mt-0.5 text-xs text-slate-400 font-semibold uppercase tracking-wider">
                                        {{ $purchase->payment_provider ?? 'local' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 font-semibold text-slate-900">
                                        <span>{{ $purchase->access_used }}</span>
                                        <span class="text-slate-300">/</span>
                                        <span class="text-slate-500">{{ $purchase->access_limit }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($purchase->remainingStarts() > 0)
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/10">
                                            {{ $purchase->remainingStarts() }} left
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-red-600/10">
                                            Expired
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-medium">
                                    {{ $purchase->last_accessed_at?->format('M j, Y g:i A') ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-medium">
                                    {{ $purchase->purchased_at?->format('M j, Y g:i A') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-500">No purchases have been recorded yet.</td>
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
