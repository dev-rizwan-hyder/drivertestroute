@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 via-white to-orange-50 px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl w-full">
        
        <!-- Large 500 Display -->
        <div class="mb-8">
            <div class="inline-flex items-center justify-center">
                <svg class="h-32 w-32 sm:h-40 sm:w-40 text-red-200 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="0.5"/>
                </svg>
                <div class="relative -ml-12">
                    <span class="text-7xl sm:text-8xl font-black bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">500</span>
                </div>
            </div>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-3">
            Server Error
        </h1>

        <!-- Description -->
        <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed max-w-xl mx-auto">
            Something went wrong on our end. Our team has been notified and we're working to fix it. Please try again later.
        </p>

        <!-- Error ID (for support) -->
        @if(isset($exception))
            <div class="mb-8 p-4 sm:p-6 bg-slate-100 border border-slate-300 rounded-2xl">
                <p class="text-xs sm:text-sm text-slate-600">
                    Error ID: <span class="font-mono font-bold text-slate-900">{{ md5($exception->getMessage() . now()) }}</span>
                </p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <!-- Reload Button -->
            <button onclick="location.reload()" class="w-full sm:w-auto px-6 py-3 sm:py-3.5 rounded-xl bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-black text-sm sm:text-base shadow-lg shadow-red-600/30 transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Try Again
            </button>

            <!-- Back Home Button -->
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 sm:py-3.5 rounded-xl border-2 border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-black text-sm sm:text-base transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                Go Home
            </a>
        </div>

        <!-- Support Section -->
        <div class="mt-12 pt-8 border-t border-slate-200">
            <p class="text-xs sm:text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Need Help?</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('contact') }}" class="px-4 py-2.5 rounded-lg bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50 text-xs sm:text-sm font-bold text-slate-700 hover:text-blue-700 transition">
                    📧 Contact Support
                </a>
                <a href="{{ route('driving-routes.index') }}" class="px-4 py-2.5 rounded-lg bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50 text-xs sm:text-sm font-bold text-slate-700 hover:text-blue-700 transition">
                    🗺️ Browse Routes
                </a>
            </div>
        </div>

        <!-- Footer Message -->
        <div class="mt-10 text-xs sm:text-sm text-slate-500">
            <p>Your error ID has been logged. Share it with our support team if the issue persists.</p>
        </div>

    </div>
</div>
@endsection
