@extends('layouts.app')

@section('title', isset($title) ? $title : 'Error')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-slate-50 px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl w-full">
        
        <!-- Error Icon -->
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-red-100">
                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4v2m0 0v1m0-1a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-3">
            {{ $title ?? 'Error' }}
        </h1>

        <!-- Description -->
        <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed max-w-xl mx-auto">
            {{ $message ?? 'An unexpected error occurred. Please try again or contact support if the problem persists.' }}
        </p>

        <!-- Error Code (if available) -->
        @if(isset($exception))
            <div class="mb-8 p-4 sm:p-6 bg-slate-100 border border-slate-300 rounded-2xl max-w-md mx-auto">
                <p class="text-xs sm:text-sm text-slate-700 font-mono break-all">
                    {{ class_basename($exception) }}
                </p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <!-- Go Back Button -->
            <button onclick="history.back()" class="w-full sm:w-auto px-6 py-3 sm:py-3.5 rounded-xl bg-gradient-to-r from-slate-700 to-slate-800 hover:from-slate-800 hover:to-slate-900 text-white font-black text-sm sm:text-base shadow-lg transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Back
            </button>

            <!-- Home Button -->
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 sm:py-3.5 rounded-xl border-2 border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-black text-sm sm:text-base transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                Home
            </a>
        </div>

        <!-- Support Link -->
        <div class="mt-10 text-xs sm:text-sm text-slate-500">
            <p>Need help? <a href="{{ route('contact') }}" class="font-bold text-slate-700 hover:text-slate-900 transition">Contact our support team</a>.</p>
        </div>

    </div>
</div>
@endsection
