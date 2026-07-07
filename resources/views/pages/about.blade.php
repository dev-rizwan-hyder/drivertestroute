@extends('layouts.app')

@section('title', 'About Driver Test Routes')

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">About us</p>
                <h1 class="mt-3 text-4xl font-bold tracking-normal text-zinc-950 sm:text-5xl">Practice with clearer expectations before test day.</h1>
                <p class="mt-6 text-lg leading-8 text-zinc-600">
                    Driver Test Routes helps learners and instructors prepare with paid route maps, route details, and controlled map starts for focused practice sessions.
                </p>
            </div>
        </div>
    </section>

    <section class="border-y border-zinc-200 bg-zinc-50">
        <div class="mx-auto grid max-w-7xl gap-4 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
            <article class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-zinc-950">Route-first preparation</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-600">Each listing is centered on practical route information: start area, destination, duration, length, waypoints, and included starts.</p>
            </article>
            <article class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-zinc-950">Controlled access</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-600">Paid routes unlock only for purchased users, with remaining starts tracked so every practice session is intentional.</p>
            </article>
            <article class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-zinc-950">Built for repeat practice</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-600">Learners can return to their purchased routes, review access, and open maps from one account dashboard.</p>
            </article>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[.8fr_1.2fr] lg:px-8">
        <div>
            <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">What we value</p>
            <h2 class="mt-3 text-3xl font-bold text-zinc-950">Professional route practice without guesswork.</h2>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h3 class="font-bold text-zinc-950">Clarity</h3>
                <p class="mt-2 text-sm leading-6 text-zinc-600">Route details should be easy to compare before purchase.</p>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h3 class="font-bold text-zinc-950">Confidence</h3>
                <p class="mt-2 text-sm leading-6 text-zinc-600">Practice should feel organized, measured, and close to the test-day drive.</p>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h3 class="font-bold text-zinc-950">Accountability</h3>
                <p class="mt-2 text-sm leading-6 text-zinc-600">Starts, purchases, and map access are tracked in the learner account.</p>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h3 class="font-bold text-zinc-950">Coverage</h3>
                <p class="mt-2 text-sm leading-6 text-zinc-600">The catalog can grow by city and test area as new routes are added.</p>
            </div>
        </div>
    </section>
@endsection
