@extends('layouts.app')

@section('title', 'Log In')

@section('content')
    <section class="auth-shell">
        <div class="auth-card">
            <div class="mb-6 text-center">
                <p class="text-sm font-black uppercase text-cyan-200">Welcome back</p>
                <h1 class="mt-2 text-3xl font-black text-white">Log In</h1>
                <p class="mt-2 text-sm leading-6 text-slate-400">Access purchased routes and saved driving test maps.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <label class="block">
                    <span class="mb-1 block text-sm font-bold text-slate-300">Email</span>
                    <span class="auth-field block">
                        <svg class="auth-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4 6h16v12H4z" />
                            <path d="m4 7 8 6 8-6" />
                        </svg>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus class="auth-input">
                    </span>
                    @error('email') <span class="mt-1 block text-xs font-semibold text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="mb-1 block text-sm font-bold text-slate-300">Password</span>
                    <span class="auth-field block">
                        <svg class="auth-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="5" y="11" width="14" height="10" rx="2" />
                            <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                        </svg>
                        <input type="password" name="password" required class="auth-input">
                    </span>
                    @error('password') <span class="mt-1 block text-xs font-semibold text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="flex items-center gap-2 text-sm font-semibold text-slate-400">
                    <input type="checkbox" name="remember" value="1" class="rounded border-blue-300 bg-white text-blue-700 focus:ring-blue-200">
                    Remember me
                </label>

                <button type="submit" class="auth-button">Log In</button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-400">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-black text-cyan-200 transition hover:text-white">Sign up</a>
            </p>
        </div>
    </section>
@endsection
