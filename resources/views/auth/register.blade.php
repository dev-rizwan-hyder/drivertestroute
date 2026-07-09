@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
    <section class="auth-shell">
        <div class="auth-card">
            <div class="mb-6 text-center">
                <p class="text-sm font-black uppercase text-cyan-200">Create access</p>
                <h1 class="mt-2 text-3xl font-black text-white">Sign Up</h1>
                <p class="mt-2 text-sm leading-6 text-slate-400">Create an account to buy routes, track starts, and return to unlocked maps.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <label class="block">
                    <span class="mb-1 block text-sm font-bold text-slate-300">Name</span>
                    <span class="auth-field block">
                        <svg class="auth-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M20 21a8 8 0 1 0-16 0" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus class="auth-input">
                    </span>
                    @error('name') <span class="mt-1 block text-xs font-semibold text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="mb-1 block text-sm font-bold text-slate-300">Email</span>
                    <span class="auth-field block">
                        <svg class="auth-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4 6h16v12H4z" />
                            <path d="m4 7 8 6 8-6" />
                        </svg>
                        <input type="email" name="email" value="{{ old('email') }}" required class="auth-input">
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

                <label class="block">
                    <span class="mb-1 block text-sm font-bold text-slate-300">Confirm Password</span>
                    <span class="auth-field block">
                        <svg class="auth-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                        <input type="password" name="password_confirmation" required class="auth-input">
                    </span>
                </label>

                <button type="submit" class="auth-button">Sign Up</button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-black text-cyan-200 transition hover:text-white">Log in</a>
            </p>
        </div>
    </section>
@endsection
