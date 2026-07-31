@extends('layouts.app')

@section('title', 'Access Forbidden')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-amber-50 via-white to-yellow-50 px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl w-full">
        
        <!-- Large 403 Display -->
        <div class="mb-8">
            <div class="inline-flex items-center justify-center">
                <svg class="h-32 w-32 sm:h-40 sm:w-40 text-amber-200 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="0.5"/>
                </svg>
                <div class="relative -ml-12">
                    <span class="text-7xl sm:text-8xl font-black bg-gradient-to-r from-amber-600 to-yellow-600 bg-clip-text text-transparent">403</span>
                </div>
            </div>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-3">
            Access Denied
        </h1>

        <!-- Description -->
        <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed max-w-xl mx-auto">
            You don't have permission to access this resource. This could be because the route hasn't been purchased or your access has expired.
        </p>

        <!-- What to do section -->
        <div class="mb-8 p-4 sm:p-6 bg-amber-50 border border-amber-200 rounded-2xl">
            <h3 class="text-sm font-bold text-amber-900 mb-2">What Now?</h3>
            <ul class="text-xs sm:text-sm text-amber-800 space-y-1 text-left">
                <li>• If this is a driving route, you may need to purchase it first</li>
                <li>• Check that you're signed in to the correct account</li>
                <li>• Your access limit may have been exceeded</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <!-- Browse Routes Button -->
            <a href="{{ route('driving-routes.index') }}" class="w-full sm:w-auto px-6 py-3 sm:py-3.5 rounded-xl bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-black text-sm sm:text-base shadow-lg shadow-amber-600/30 transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m0 0h6m-6 0h6" />
                </svg>
                Browse Available Routes
            </a>

            <!-- My Routes Button (if authenticated) -->
            @auth
                <a href="{{ route('driving-routes.my') }}" class="w-full sm:w-auto px-6 py-3 sm:py-3.5 rounded-xl border-2 border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-black text-sm sm:text-base transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11l-7 7-7-7m0 0l7-7 7 7" />
                    </svg>
                    My Routes
                </a>
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 sm:py-3.5 rounded-xl border-2 border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-black text-sm sm:text-base transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </a>
            @endauth
        </div>

        <!-- Quick Links -->
        <div class="mt-12 pt-8 border-t border-slate-200">
            <p class="text-xs sm:text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Quick Links</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-3 flex-wrap justify-center">
                <a href="{{ route('home') }}" class="px-3 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-amber-700 transition">
                    🏠 Home
                </a>
                <span class="text-slate-300">•</span>
                <a href="{{ route('driving-routes.index') }}" class="px-3 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-amber-700 transition">
                    🗺️ All Routes
                </a>
                <span class="text-slate-300">•</span>
                <a href="{{ route('contact') }}" class="px-3 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-amber-700 transition">
                    📧 Support
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
