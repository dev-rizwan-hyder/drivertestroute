@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-blue-50/60 px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center max-w-2xl w-full">
        
        <!-- Large 404 Display -->
        <div class="mb-8 relative flex items-center justify-center">
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <span class="text-8xl sm:text-9xl font-black text-slate-200/60 select-none tracking-widest blur-[1px]">404</span>
            </div>
            <div class="relative">
                <span class="text-7xl sm:text-8xl font-black bg-gradient-to-r from-teal-600 via-blue-600 to-indigo-600 bg-clip-text text-transparent drop-shadow-sm">404</span>
            </div>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-3">
            Oops! Page Not Found
        </h1>

        <!-- Description -->
        <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed max-w-xl mx-auto font-medium">
            The page you're looking for doesn't exist or has been moved. This could be a driving route that's no longer available or an invalid link.
        </p>

        <!-- Error Details (if applicable) -->
        @if(isset($exception) && $exception->getMessage())
            <div class="mb-8 p-4 sm:p-5 bg-amber-50/90 border border-amber-200 rounded-2xl shadow-sm max-w-xl mx-auto">
                <div class="flex items-center justify-center gap-2 mb-1 text-amber-800 font-bold text-xs uppercase tracking-wider">
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Details</span>
                </div>
                <p class="text-xs sm:text-sm font-mono text-amber-900 break-words font-semibold">
                    {{ $exception->getMessage() }}
                </p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <!-- Back Home Button -->
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-black text-sm sm:text-base shadow-lg shadow-teal-600/25 transition transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2.5">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Back to Home</span>
            </a>

            <!-- Browse Routes Button -->
            <a href="{{ route('driving-routes.index') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl border-2 border-slate-300 bg-white hover:bg-slate-50 text-slate-800 hover:text-slate-900 font-black text-sm sm:text-base shadow-sm transition transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2.5">
                <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span>Browse Routes</span>
            </a>
        </div>

        <!-- Helpful Links Section -->
        <div class="mt-12 pt-8 border-t border-slate-200/80">
            <p class="text-xs font-extrabold text-slate-500 uppercase tracking-widest mb-4">Quick Links</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 max-w-xl mx-auto">
                <a href="{{ route('home') }}" class="p-3 rounded-xl bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50/50 text-xs sm:text-sm font-bold text-slate-700 hover:text-teal-700 transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>🏠</span> <span>Home</span>
                </a>
                <a href="{{ route('driving-routes.index') }}" class="p-3 rounded-xl bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50/50 text-xs sm:text-sm font-bold text-slate-700 hover:text-teal-700 transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>🗺️</span> <span>Routes</span>
                </a>
                @auth
                    <a href="{{ route('driving-routes.my') }}" class="p-3 rounded-xl bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50/50 text-xs sm:text-sm font-bold text-slate-700 hover:text-teal-700 transition shadow-sm flex items-center justify-center gap-1.5">
                        <span>📋</span> <span>My Routes</span>
                    </a>
                @else
                    <a href="{{ route('blog') }}" class="p-3 rounded-xl bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50/50 text-xs sm:text-sm font-bold text-slate-700 hover:text-teal-700 transition shadow-sm flex items-center justify-center gap-1.5">
                        <span>📝</span> <span>Blog</span>
                    </a>
                @endauth
                <a href="{{ route('about') }}" class="p-3 rounded-xl bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50/50 text-xs sm:text-sm font-bold text-slate-700 hover:text-teal-700 transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>ℹ️</span> <span>About</span>
                </a>
                <a href="{{ route('contact') }}" class="p-3 rounded-xl bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50/50 text-xs sm:text-sm font-bold text-slate-700 hover:text-teal-700 transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>📧</span> <span>Contact</span>
                </a>
                <a href="javascript:history.back()" class="p-3 rounded-xl bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50/50 text-xs sm:text-sm font-bold text-slate-700 hover:text-teal-700 transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>↩️</span> <span>Go Back</span>
                </a>
            </div>
        </div>

        <!-- Footer Message -->
        <div class="mt-10 text-xs sm:text-sm text-slate-500 font-medium">
            <p>If you believe this is an error, please <a href="{{ route('contact') }}" class="font-bold text-teal-600 hover:text-teal-700 underline underline-offset-2 transition">contact us</a>.</p>
        </div>

    </div>
</div>
@endsection
