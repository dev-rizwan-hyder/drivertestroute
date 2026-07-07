@extends('layouts.app')

@section('title', 'Contact Driver Test Routes')

@section('content')
    <section class="bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[.85fr_1.15fr] lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Contact</p>
                <h1 class="mt-3 text-4xl font-bold tracking-normal text-zinc-950 sm:text-5xl">Questions about routes, access, or coverage?</h1>
                <p class="mt-6 text-lg leading-8 text-zinc-600">
                    Send a message and include the city or route name when relevant. This helps us review route availability, purchase access, or account questions faster.
                </p>

                <div class="mt-8 grid gap-4">
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-5">
                        <h2 class="font-bold text-zinc-950">Route support</h2>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">For map unlocks, remaining starts, route details, and PDF preview questions.</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-5">
                        <h2 class="font-bold text-zinc-950">Coverage requests</h2>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">Tell us which city, province, or test area you want added next.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('contact.submit') }}" class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
                @csrf

                <div class="grid gap-5">
                    <label class="block">
                        <span class="text-sm font-bold text-zinc-800">Name</span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        >
                    </label>

                    <label class="block">
                        <span class="text-sm font-bold text-zinc-800">Email</span>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        >
                    </label>

                    <label class="block">
                        <span class="text-sm font-bold text-zinc-800">Topic</span>
                        <select
                            name="topic"
                            required
                            class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        >
                            <option value="">Choose a topic</option>
                            @foreach(['Route purchase', 'Map access', 'Coverage request', 'Account help', 'Other'] as $topic)
                                <option value="{{ $topic }}" @selected(old('topic') === $topic)>{{ $topic }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-sm font-bold text-zinc-800">Message</span>
                        <textarea
                            name="message"
                            rows="6"
                            required
                            class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        >{{ old('message') }}</textarea>
                    </label>

                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-emerald-800">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
