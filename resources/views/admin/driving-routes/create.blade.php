@extends('layouts.admin')

@section('title', 'Add Route')

@section('content')
    <section>
        <div class="mb-8">
            <a href="{{ route('admin.driving-routes.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Back to admin routes</a>
            <h1 class="mt-3 text-3xl font-bold text-stone-950">Add Route</h1>
        </div>

        @include('admin.driving-routes.form', [
            'route' => $route,
            'points' => $points,
            'action' => route('admin.driving-routes.store'),
            'method' => 'POST',
        ])
    </section>
@endsection
