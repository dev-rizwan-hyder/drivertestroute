@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <section class="mx-auto grid min-h-[calc(100vh-73px)] max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-md rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-stone-950">Create Account</h1>
                <p class="mt-2 text-sm text-stone-600">The first registered account becomes the admin account.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <label class="block">
                    <span class="text-sm font-medium text-stone-700">Name</span>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-stone-700">Email</span>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-stone-700">Password</span>
                    <input
                        type="password"
                        name="password"
                        required
                        class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-stone-700">Confirm Password</span>
                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    >
                </label>

                <button type="submit" class="w-full rounded-md bg-emerald-700 px-4 py-2.5 font-semibold text-white hover:bg-emerald-800">
                    Register
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-stone-600">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Login</a>
            </p>
        </div>
    </section>
@endsection
