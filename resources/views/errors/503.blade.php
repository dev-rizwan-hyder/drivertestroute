@extends('layouts.app')

@section('title', 'Service Unavailable')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-slate-50 px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl w-full">
        
        <!-- Maintenance Icon -->
        <div class="mb-8">
            <svg class="h-32 w-32 sm:h-40 sm:w-40 mx-auto text-blue-400 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-3">
            We're Under Maintenance
        </h1>

        <!-- Description -->
        <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed max-w-xl mx-auto">
            We're performing scheduled maintenance to improve your experience. We'll be back online shortly. Thank you for your patience!
        </p>

        <!-- Status Message -->
        <div class="mb-8 p-4 sm:p-6 bg-blue-50 border border-blue-200 rounded-2xl">
            <p class="text-xs sm:text-sm font-bold text-blue-900 mb-2">Expected Return Time</p>
            <p class="text-sm sm:text-base font-black text-blue-700">Estimated in the next few hours</p>
        </div>

        <!-- Action Button -->
        <div class="flex flex-col items-center justify-center gap-4">
            <!-- Reload Button -->
            <button onclick="location.reload()" class="w-full sm:w-auto px-8 py-3 sm:py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-black text-sm sm:text-base shadow-lg shadow-blue-600/30 transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Check Again
            </button>

            <!-- Alternative actions -->
            <div class="text-xs sm:text-sm text-slate-600">
                <p>In the meantime, you can <a href="mailto:support@drivertest.com" class="font-bold text-blue-600 hover:text-blue-700 transition">contact us</a> with any questions.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-8 border-t border-slate-200">
            <p class="text-xs sm:text-sm text-slate-500">
                Updates posted at <a href="https://status.drivertest.com" target="_blank" class="font-bold text-slate-700 hover:text-slate-900 transition">status.drivertest.com</a>
            </p>
        </div>

    </div>
</div>
@endsection
