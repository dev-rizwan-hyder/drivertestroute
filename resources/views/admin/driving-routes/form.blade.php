@php
    $existingPoints = collect($points)->map(function ($point, $index) {
        return [
            'sort_order' => data_get($point, 'sort_order', $index + 1),
            'maneuver' => data_get($point, 'maneuver', 'continue'),
            'instruction' => data_get($point, 'instruction', ''),
            'lat' => data_get($point, 'lat', ''),
            'lng' => data_get($point, 'lng', ''),
            'distance_km' => data_get($point, 'distance_km', ''),
            'duration' => data_get($point, 'duration', ''),
        ];
    })->values()->all();

    $formPoints = old('points', $existingPoints);
    $selectedCityId = old('city_id', $route->city_id ?: optional($cities->firstWhere('name', $route->city))->id);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-stone-950">Route Details</h2>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <label class="block md:col-span-2">
                <span class="text-sm font-medium text-stone-700">Title</span>
                <input type="text" name="title" value="{{ old('title', $route->title) }}" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">City</span>
                <select name="city_id" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Choose a city</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" @selected((string) $selectedCityId === (string) $city->id)>{{ $city->name }} - {{ $city->address }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Province</span>
                <input type="text" name="province" value="{{ old('province', $route->province) }}" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Price</span>
                <input type="number" name="price" value="{{ old('price', $route->price ?? 0) }}" required min="0" step="0.01" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Map Starts Included</span>
                <input type="number" name="access_limit" value="{{ old('access_limit', $route->access_limit ?? 1) }}" required min="1" step="1" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Route Duration Minutes</span>
                <input type="number" name="route_duration_minutes" value="{{ old('route_duration_minutes', $route->route_duration_minutes) }}" min="1" step="1" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Route Length km</span>
                <input type="number" name="route_length_km" value="{{ old('route_length_km', $route->route_length_km) }}" min="0" step="0.01" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Preview PDF</span>
                <input type="file" name="preview_pdf" accept="application/pdf" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-sm text-stone-700 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3.5 file:py-1.5 file:font-semibold file:text-blue-700 hover:file:bg-blue-100 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition">
                @if($route->preview_pdf_path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($route->preview_pdf_path) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-blue-700 hover:text-blue-800">Open current PDF</a>
                @endif
            </label>

            <label class="block md:col-span-2">
                <span class="text-sm font-medium text-stone-700">Description</span>
                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('description', $route->description) }}</textarea>
            </label>

            <label class="flex items-center gap-2 text-sm font-medium text-stone-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $route->is_active ?? true)) class="rounded border-stone-300 text-blue-600 focus:ring-blue-500">
                Active
            </label>
        </div>
    </section>

    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-stone-950">Start And Midpoint</h2>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <div class="rounded-md bg-stone-50 p-4">
                <h3 class="font-semibold text-stone-950">Start Point</h3>
                <div class="mt-4 grid gap-4">
                    <label class="block">
                        <span class="text-sm font-medium text-stone-700">Label</span>
                        <input type="text" name="start_label" value="{{ old('start_label', $route->start_label) }}" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </label>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Latitude</span>
                            <input type="number" name="start_lat" value="{{ old('start_lat', $route->start_lat) }}" step="0.0000001" min="-90" max="90" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Longitude</span>
                            <input type="number" name="start_lng" value="{{ old('start_lng', $route->start_lng) }}" step="0.0000001" min="-180" max="180" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>
                    </div>
                </div>
            </div>

            <div class="rounded-md bg-stone-50 p-4">
                <h3 class="font-semibold text-stone-950">Midpoint / End Point</h3>
                <div class="mt-4 grid gap-4">
                    <label class="block">
                        <span class="text-sm font-medium text-stone-700">Label</span>
                        <input type="text" name="destination_label" value="{{ old('destination_label', $route->destination_label) }}" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </label>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Latitude</span>
                            <input type="number" name="end_lat" value="{{ old('end_lat', $route->end_lat) }}" step="0.0000001" min="-90" max="90" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Longitude</span>
                            <input type="number" name="end_lng" value="{{ old('end_lng', $route->end_lng) }}" step="0.0000001" min="-180" max="180" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Optional Manual Points</h2>
                <p class="mt-1 text-sm text-stone-600">Leave this empty when Google should generate the road path and instructions from start to midpoint and back.</p>
            </div>
            <button type="button" id="add-point" class="rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100">
                Add Point
            </button>
        </div>

        <div id="points" class="mt-5 space-y-4">
            @foreach($formPoints as $index => $point)
                <div class="point-row rounded-md border border-stone-200 bg-stone-50 p-4" data-index="{{ $index }}">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="font-semibold text-stone-950">Point <span class="point-number">{{ $loop->iteration }}</span></h3>
                        <button type="button" class="remove-point rounded-md border border-red-200 px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">
                            Remove
                        </button>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-6">
                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Order</span>
                            <input type="number" name="points[{{ $index }}][sort_order]" value="{{ data_get($point, 'sort_order', $index + 1) }}" required min="1" class="sort-order mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Instruction</span>
                            <select name="points[{{ $index }}][maneuver]" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="continue" @selected(data_get($point, 'maneuver') === 'continue')>Continue</option>
                                <option value="turn_left" @selected(data_get($point, 'maneuver') === 'turn_left')>Turn left</option>
                                <option value="turn_right" @selected(data_get($point, 'maneuver') === 'turn_right')>Turn right</option>
                            </select>
                        </label>

                        <label class="block lg:col-span-2">
                            <span class="text-sm font-medium text-stone-700">Instruction Text</span>
                            <input type="text" name="points[{{ $index }}][instruction]" value="{{ data_get($point, 'instruction') }}" placeholder="Turn right onto Main Street" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Latitude</span>
                            <input type="number" name="points[{{ $index }}][lat]" value="{{ data_get($point, 'lat') }}" step="0.0000001" min="-90" max="90" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Longitude</span>
                            <input type="number" name="points[{{ $index }}][lng]" value="{{ data_get($point, 'lng') }}" step="0.0000001" min="-180" max="180" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Distance km</span>
                            <input type="number" name="points[{{ $index }}][distance_km]" value="{{ data_get($point, 'distance_km') }}" min="0" step="0.01" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-stone-700">Duration</span>
                            <input type="text" name="points[{{ $index }}][duration]" value="{{ data_get($point, 'duration') }}" placeholder="1 min" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('admin.driving-routes.index') }}" class="rounded-md border border-stone-300 px-4 py-2 font-semibold text-stone-700 hover:bg-stone-100">
            Cancel
        </a>
        <button type="submit" class="rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 hover:from-blue-800 hover:to-cyan-700 px-5 py-2 font-semibold text-white shadow-md shadow-blue-500/10 transition">
            Save Route
        </button>
    </div>
</form>

<template id="point-template">
    <div class="point-row rounded-md border border-stone-200 bg-stone-50 p-4" data-index="__INDEX__">
        <div class="mb-4 flex items-center justify-between gap-3">
            <h3 class="font-semibold text-stone-950">Point <span class="point-number">__NUMBER__</span></h3>
            <button type="button" class="remove-point rounded-md border border-red-200 px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">
                Remove
            </button>
        </div>

        <div class="grid gap-4 lg:grid-cols-6">
            <label class="block">
                <span class="text-sm font-medium text-stone-700">Order</span>
                <input type="number" name="points[__INDEX__][sort_order]" value="__NUMBER__" required min="1" class="sort-order mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Instruction</span>
                <select name="points[__INDEX__][maneuver]" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="continue">Continue</option>
                    <option value="turn_left">Turn left</option>
                    <option value="turn_right">Turn right</option>
                </select>
            </label>

            <label class="block lg:col-span-2">
                <span class="text-sm font-medium text-stone-700">Instruction Text</span>
                <input type="text" name="points[__INDEX__][instruction]" placeholder="Turn right onto Main Street" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Latitude</span>
                <input type="number" name="points[__INDEX__][lat]" step="0.0000001" min="-90" max="90" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Longitude</span>
                <input type="number" name="points[__INDEX__][lng]" step="0.0000001" min="-180" max="180" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Distance km</span>
                <input type="number" name="points[__INDEX__][distance_km]" min="0" step="0.01" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-stone-700">Duration</span>
                <input type="text" name="points[__INDEX__][duration]" placeholder="1 min" class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>
        </div>
    </div>
</template>

<script>
    const pointsContainer = document.getElementById('points');
    const pointTemplate = document.getElementById('point-template');
    const addPointButton = document.getElementById('add-point');

    function renumberPoints() {
        pointsContainer.querySelectorAll('.point-row').forEach((row, index) => {
            row.querySelector('.point-number').textContent = index + 1;
            const sortOrder = row.querySelector('.sort-order');

            if (!sortOrder.value) {
                sortOrder.value = index + 1;
            }
        });
    }

    addPointButton.addEventListener('click', () => {
        const index = Date.now();
        const number = pointsContainer.querySelectorAll('.point-row').length + 1;
        const html = pointTemplate.innerHTML
            .replaceAll('__INDEX__', index)
            .replaceAll('__NUMBER__', number);

        pointsContainer.insertAdjacentHTML('beforeend', html);
        renumberPoints();
    });

    pointsContainer.addEventListener('click', (event) => {
        if (!event.target.closest('.remove-point')) {
            return;
        }

        event.target.closest('.point-row').remove();
        renumberPoints();
    });
</script>
