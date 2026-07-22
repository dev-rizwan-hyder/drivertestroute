@php
    $selectedCityId = old('city_id', $route->city_id ?: optional($cities->firstWhere('name', $route->city))->id);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <!-- Basic Route Information -->
    <section class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-stone-100">
            <div>
                <h2 class="text-lg font-bold text-stone-950">Route Details</h2>
                <p class="text-xs text-stone-500">Configure driving test route information and price</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 uppercase">
                {{ strtoupper($route->package_type ?: 'G1') }} Route
            </span>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <label class="block md:col-span-2">
                <span class="text-sm font-semibold text-stone-700">Route Title</span>
                <input type="text" name="title" value="{{ old('title', $route->title) }}" placeholder="e.g. Brampton Test Route 1" required class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Package Type</span>
                <select name="package_type" required class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="g1" @selected(old('package_type', $route->package_type) === 'g1')>G1 Package (G2 Road Test)</option>
                    <option value="g2" @selected(old('package_type', $route->package_type) === 'g2')>G2 Package (G Road Test)</option>
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">City</span>
                <select name="city_id" required class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Choose a city</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" @selected((string) $selectedCityId === (string) $city->id)>{{ $city->name }} - {{ $city->address }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Province</span>
                <input type="text" name="province" value="{{ old('province', $route->province ?: 'Ontario') }}" required class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Price ($ USD)</span>
                <input type="number" name="price" value="{{ old('price', $route->price ?? 0) }}" required min="0" step="0.01" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Map Starts Included</span>
                <input type="number" name="access_limit" value="{{ old('access_limit', $route->access_limit ?? 10) }}" required min="1" step="1" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Estimated Duration (Minutes)</span>
                <input type="number" name="route_duration_minutes" value="{{ old('route_duration_minutes', $route->route_duration_minutes) }}" placeholder="e.g. 20" min="1" step="1" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Route Length (km)</span>
                <input type="number" name="route_length_km" value="{{ old('route_length_km', $route->route_length_km) }}" placeholder="e.g. 12.5" min="0" step="0.01" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>
        </div>
    </section>

    <!-- Google Maps URL & Start/End Section -->
    <section class="rounded-xl border border-teal-200 bg-teal-50/30 p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-700 text-white font-bold text-sm">🗺️</span>
            <div>
                <h2 class="text-lg font-bold text-stone-950">Google Maps Navigation Link</h2>
                <p class="text-xs text-stone-600">Paste your custom Google Maps or My Maps link for voice-guided navigation</p>
            </div>
        </div>

        <div class="space-y-4">
            <label class="block">
                <span class="text-sm font-bold text-stone-800">Google Maps / My Maps URL</span>
                <input type="url" name="google_maps_url" value="{{ old('google_maps_url', $route->google_maps_url) }}" placeholder="https://www.google.com/maps/d/edit?mid=1YQ1ViUMHI1F4QwoxVFGgyIlt9L7MJYU&usp=sharing" class="mt-1 block w-full rounded-lg border border-teal-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-100">
                <span class="mt-1 block text-xs text-stone-500">Copy & paste your Google My Maps link or Google Maps Directions link here.</span>
            </label>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-semibold text-stone-700">Start Point Label</span>
                    <input type="text" name="start_label" value="{{ old('start_label', $route->start_label ?: 'Brampton Test Center') }}" required placeholder="e.g. Brampton Test Center" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-stone-700">Destination Point Label</span>
                    <input type="text" name="destination_label" value="{{ old('destination_label', $route->destination_label ?: 'Midpoint / Return to Start') }}" required placeholder="e.g. Return to Test Center" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none">
                </label>
            </div>
        </div>
    </section>

    <!-- Additional Files & Description -->
    <section class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-stone-950 mb-4">Preview PDF & Route Notes</h2>

        <div class="grid gap-5 md:grid-cols-2">
            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Preview PDF File (Optional)</span>
                <input type="file" name="preview_pdf" accept="application/pdf" class="mt-1 block w-full rounded-lg border border-stone-300 px-3 py-2 text-sm text-stone-700 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3.5 file:py-1.5 file:font-semibold file:text-blue-700 hover:file:bg-blue-100 focus:outline-none transition">
                @if($route->preview_pdf_path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($route->preview_pdf_path) }}" target="_blank" class="mt-2 inline-flex items-center gap-1 text-sm font-bold text-blue-700 hover:text-blue-800">
                        📄 View current preview PDF
                    </a>
                @endif
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Active Status</span>
                <div class="mt-2 flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $route->is_active ?? true)) class="h-4 w-4 rounded border-stone-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-stone-700">Active (Visible to users)</span>
                </div>
            </label>

            <label class="block md:col-span-2">
                <span class="text-sm font-semibold text-stone-700">Description & Route Tips</span>
                <textarea name="description" rows="3" placeholder="Add specific notes or test tips for student drivers..." class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('description', $route->description) }}</textarea>
            </label>
        </div>
    </section>

    <div class="flex items-center justify-end gap-3 pt-4">
        <a href="{{ route('admin.driving-routes.index') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 font-bold text-stone-700 hover:bg-stone-100 transition">
            Cancel
        </a>
        <button type="submit" class="rounded-lg bg-gradient-to-r from-blue-700 to-teal-700 hover:from-blue-800 hover:to-teal-800 px-6 py-2.5 font-black text-white shadow-md transition active:scale-98">
            Save Route
        </button>
    </div>
</form>
