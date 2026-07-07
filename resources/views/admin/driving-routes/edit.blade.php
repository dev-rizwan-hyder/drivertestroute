@extends('layouts.admin')

@section('title', 'Edit Route')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('admin.driving-routes.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Back to admin routes</a>
                <h1 class="mt-3 text-3xl font-bold text-stone-950">Edit Route</h1>
            </div>

            <a href="{{ route('driving-routes.show', $route) }}" class="inline-flex items-center justify-center rounded-md border border-stone-300 px-4 py-2 font-semibold text-stone-700 hover:bg-stone-100">
                View Map
            </a>
        </div>

        @include('admin.driving-routes.form', [
            'route' => $route,
            'points' => $points,
            'action' => route('admin.driving-routes.update', $route),
            'method' => 'PUT',
        ])
    </section>
@endsection
