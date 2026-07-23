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

    // If points is empty, try to parse from google_maps_url
    if (empty($existingPoints) && ! empty($route->parsed_waypoints)) {
        $existingPoints = collect($route->parsed_waypoints)->map(function ($pt, $idx) {
            return [
                'sort_order' => $idx + 1,
                'maneuver' => $pt['maneuver'] ?? 'continue',
                'instruction' => $pt['instruction'] ?? '',
                'lat' => $pt['lat'] ?? '',
                'lng' => $pt['lng'] ?? '',
                'distance_km' => '',
                'duration' => '',
            ];
        })->all();
    }

    $formPoints = old('points', $existingPoints);
    $selectedCityId = old('city_id', $route->city_id ?: optional($cities->firstWhere('name', $route->city))->id);
    $mapsKey = config('services.google.maps_key');
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
                <select name="city_id" id="city_id_select" required class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
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
                <input type="number" id="route_duration_minutes_input" name="route_duration_minutes" value="{{ old('route_duration_minutes', $route->route_duration_minutes) }}" placeholder="e.g. 20" min="1" step="1" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-stone-700">Route Length (km)</span>
                <input type="number" id="route_length_km_input" name="route_length_km" value="{{ old('route_length_km', $route->route_length_km) }}" placeholder="e.g. 12.5" min="0" step="0.01" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>
        </div>
    </section>

    <!-- Import Google Maps Directions URL & Visual Builder -->
    <section class="rounded-xl border border-teal-200 bg-teal-50/40 p-6 shadow-sm">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-teal-200/60">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-700 text-white font-bold text-sm">🗺️</span>
                <div>
                    <h2 class="text-lg font-bold text-stone-950">Import Route from Google Maps Link</h2>
                    <p class="text-xs text-stone-600">Paste your Google Maps Directions URL below to automatically load all stops and turn instructions</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <label class="block">
                <span class="text-sm font-bold text-stone-800">Google Maps Directions URL</span>
                <div class="mt-1 flex gap-2">
                    <input type="url" id="google_maps_url_input" name="google_maps_url" value="{{ old('google_maps_url', $route->google_maps_url) }}" placeholder="https://www.google.com/maps/dir/199+Longside+Dr,+Mississauga,+ON+L5W+1Z9,+Canada/43.6349525,-79.6968147/43.6369668,-79.6943133/..." class="flex-1 rounded-lg border border-teal-300 px-3.5 py-2.5 text-stone-950 shadow-sm focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    <button type="button" id="btn-import-route" class="rounded-lg bg-teal-700 hover:bg-teal-800 text-white px-5 py-2.5 font-bold text-sm shadow-md transition shrink-0">
                        Import Route & Waypoints
                    </button>
                </div>
                <span class="mt-1 block text-xs text-stone-500">Paste the full directions URL from Google Maps (e.g. <code>https://www.google.com/maps/dir/...</code>) to fetch all waypoints automatically.</span>
            </label>

            <!-- Search place helper -->
            <div class="flex gap-2 pt-2">
                <input type="text" id="map-search-input" placeholder="Search address or location to add a point..." class="flex-1 rounded-lg border border-stone-300 px-3.5 py-2 text-sm text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none">
                <button type="button" id="btn-add-search-point" class="rounded-lg bg-stone-900 hover:bg-stone-800 text-white px-4 py-2 font-bold text-sm transition">
                    + Add Point
                </button>
            </div>

            <!-- Google Maps Interactive Canvas -->
            <div id="admin-route-map" class="h-[450px] w-full rounded-xl border border-stone-200 shadow-inner z-0"></div>

            <div class="grid gap-4 md:grid-cols-2 pt-2">
                <label class="block">
                    <span class="text-sm font-semibold text-stone-700">Start Location Name</span>
                    <input type="text" id="start_label_input" name="start_label" value="{{ old('start_label', $route->start_label ?: 'Brampton Test Center') }}" required placeholder="e.g. Brampton Test Center" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none">
                    <input type="hidden" id="start_lat_input" name="start_lat" value="{{ old('start_lat', $route->start_lat) }}">
                    <input type="hidden" id="start_lng_input" name="start_lng" value="{{ old('start_lng', $route->start_lng) }}">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-stone-700">Destination Location Name</span>
                    <input type="text" id="destination_label_input" name="destination_label" value="{{ old('destination_label', $route->destination_label ?: 'Return to Start') }}" required placeholder="e.g. Return to Test Center" class="mt-1 block w-full rounded-lg border border-stone-300 px-3.5 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none">
                    <input type="hidden" id="end_lat_input" name="end_lat" value="{{ old('end_lat', $route->end_lat) }}">
                    <input type="hidden" id="end_lng_input" name="end_lng" value="{{ old('end_lng', $route->end_lng) }}">
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

    <!-- Turn-by-Turn Waypoints Manager -->
    <section class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-stone-100">
            <div>
                <h2 class="text-lg font-bold text-stone-950">Turn Directions & Waypoints</h2>
                <p class="text-xs text-stone-500">Review all imported steps or add turn instructions manually</p>
            </div>
            <button type="button" id="btn-add-step" class="inline-flex items-center gap-1.5 rounded-lg bg-stone-900 hover:bg-stone-800 px-4 py-2 text-xs font-bold text-white transition active:scale-95">
                <span>+ Add Turn Instruction</span>
            </button>
        </div>

        <div id="waypoints-container" class="mt-5 space-y-3">
            <!-- Dynamically populated via JavaScript -->
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

@push('scripts')
@if($mapsKey)
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $mapsKey }}&libraries=places,geometry"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let waypoints = @json($formPoints);
        const container = document.getElementById('waypoints-container');
        const btnAdd = document.getElementById('btn-add-step');
        const btnImport = document.getElementById('btn-import-route');
        const urlInput = document.getElementById('google_maps_url_input');

        let map, directionsService, directionsRenderer, geocoder;
        let markers = [];

        function renderSteps() {
            container.innerHTML = '';

            if (!waypoints || waypoints.length === 0) {
                container.innerHTML = `
                    <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50 p-6 text-center text-sm font-semibold text-stone-500">
                        No turn instructions added yet. Click "Import Route & Waypoints" or "+ Add Turn Instruction" to add guidance.
                    </div>
                `;
                return;
            }

            waypoints.forEach((wp, index) => {
                const row = document.createElement('div');
                row.className = 'flex flex-col sm:flex-row items-start sm:items-center gap-3 rounded-xl border border-stone-200 bg-white p-4 shadow-sm';
                
                row.innerHTML = `
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-xs font-black text-slate-700 border border-slate-200">
                            ${index + 1}
                        </span>
                        <input type="hidden" name="points[${index}][sort_order]" value="${index + 1}">
                        <input type="hidden" name="points[${index}][lat]" value="${wp.lat || ''}">
                        <input type="hidden" name="points[${index}][lng]" value="${wp.lng || ''}">
                        <input type="hidden" name="points[${index}][distance_km]" value="${wp.distance_km || ''}">
                    </div>

                    <div class="w-full sm:w-40 shrink-0">
                        <select name="points[${index}][maneuver]" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-sm text-stone-900 focus:outline-none">
                            <option value="continue" ${wp.maneuver === 'continue' ? 'selected' : ''}>Continue Straight</option>
                            <option value="turn_left" ${wp.maneuver === 'turn_left' ? 'selected' : ''}>Turn Left</option>
                            <option value="turn_right" ${wp.maneuver === 'turn_right' ? 'selected' : ''}>Turn Right</option>
                        </select>
                    </div>

                    <div class="flex-1 w-full">
                        <input type="text" name="points[${index}][instruction]" value="${wp.instruction || ''}" placeholder="e.g. Turn left onto Main Street North" required class="w-full rounded-lg border border-stone-300 px-3.5 py-1.5 text-sm text-stone-900 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                        <button type="button" class="btn-remove-step p-1.5 text-slate-400 hover:text-red-600 transition" title="Delete Step">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                `;

                row.querySelector('.btn-remove-step').addEventListener('click', () => {
                    waypoints.splice(index, 1);
                    renderSteps();
                });

                row.querySelector('select').addEventListener('change', (e) => {
                    wp.maneuver = e.target.value;
                });

                row.querySelector('input[type="text"]').addEventListener('input', (e) => {
                    wp.instruction = e.target.value;
                });

                container.appendChild(row);
            });
        }

        // Initialize Google Map Canvas if Google Maps API loaded
        const mapElement = document.getElementById('admin-route-map');
        if (mapElement && typeof google !== 'undefined' && google.maps) {
            map = new google.maps.Map(mapElement, {
                center: { lat: 43.6532, lng: -79.3832 },
                zoom: 12,
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: false,
            });
            geocoder = new google.maps.Geocoder();
        }

        // Import Route Handler
        if (btnImport) {
            btnImport.addEventListener('click', () => {
                const url = urlInput.value.trim();
                if (!url) {
                    alert('Please paste a Google Maps Directions URL first.');
                    return;
                }

                // Extract locations/coordinates from Google Maps Directions URL
                let parsedStops = [];

                if (url.includes('/maps/dir/')) {
                    const match = url.match(/\/maps\/dir\/([^\?]+)/);
                    if (match && match[1]) {
                        const rawPath = match[1].split('/@')[0];
                        const parts = rawPath.split('/').filter(p => p && !p.startsWith('data=') && !p.startsWith('am='));

                        parts.forEach((p, idx) => {
                            const decoded = decodeURIComponent(p);
                            const coordMatch = decoded.match(/^(-?\d+\.\d+),\s*(-?\d+\.\d+)$/);

                            if (coordMatch) {
                                parsedStops.push({
                                    lat: parseFloat(coordMatch[1]),
                                    lng: parseFloat(coordMatch[2]),
                                    query: `${coordMatch[1]},${coordMatch[2]}`,
                                    label: `Waypoint ${idx + 1}`,
                                });
                            } else {
                                const cleanLabel = decoded.replace(/\+/g, ' ');
                                parsedStops.push({
                                    lat: null,
                                    lng: null,
                                    query: cleanLabel,
                                    label: cleanLabel,
                                });
                            }
                        });
                    }
                }

                if (parsedStops.length === 0) {
                    alert('Could not parse waypoints from the URL. Please ensure it is a valid Google Maps Directions URL.');
                    return;
                }

                // Populate Start Label and Destination Label
                if (parsedStops.length > 0 && document.getElementById('start_label_input')) {
                    document.getElementById('start_label_input').value = parsedStops[0].label;
                }
                if (parsedStops.length > 1 && document.getElementById('destination_label_input')) {
                    document.getElementById('destination_label_input').value = parsedStops[parsedStops.length - 1].label;
                }

                // Call DirectionsService if Google Maps JS API available
                if (directionsService && parsedStops.length >= 2) {
                    const origin = parsedStops[0].lat !== null 
                        ? { lat: parsedStops[0].lat, lng: parsedStops[0].lng }
                        : parsedStops[0].query;

                    const destination = parsedStops[parsedStops.length - 1].lat !== null
                        ? { lat: parsedStops[parsedStops.length - 1].lat, lng: parsedStops[parsedStops.length - 1].lng }
                        : parsedStops[parsedStops.length - 1].query;

                    const waypointsList = [];
                    for (let i = 1; i < parsedStops.length - 1; i++) {
                        const st = parsedStops[i];
                        waypointsList.push({
                            location: st.lat !== null ? { lat: st.lat, lng: st.lng } : st.query,
                            stopover: true,
                        });
                    }

                    directionsService.route({
                        origin: origin,
                        destination: destination,
                        waypoints: waypointsList,
                        travelMode: google.maps.TravelMode.DRIVING,
                    }, (result, status) => {
                        if (status === 'OK' && result.routes && result.routes[0]) {
                            directionsRenderer.setDirections(result);

                            const route = result.routes[0];
                            let totalDistanceMeters = 0;
                            let totalDurationSecs = 0;

                            waypoints = [];

                            route.legs.forEach((leg, legIdx) => {
                                totalDistanceMeters += leg.distance ? leg.distance.value : 0;
                                totalDurationSecs += leg.duration ? leg.duration.value : 0;

                                leg.steps.forEach((step, stepIdx) => {
                                    const rawText = step.instructions.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                                    let maneuver = 'continue';
                                    if (rawText.toLowerCase().includes('left')) maneuver = 'turn_left';
                                    if (rawText.toLowerCase().includes('right')) maneuver = 'turn_right';

                                    waypoints.push({
                                        sort_order: waypoints.length + 1,
                                        lat: step.start_location.lat(),
                                        lng: step.start_location.lng(),
                                        instruction: rawText,
                                        maneuver: maneuver,
                                        distance_km: (step.distance.value / 1000).toFixed(2),
                                    });
                                });
                            });

                            // Fill Stats
                            const totalKm = (totalDistanceMeters / 1000).toFixed(1);
                            const totalMins = Math.round(totalDurationSecs / 60);

                            if (document.getElementById('route_length_km_input')) {
                                document.getElementById('route_length_km_input').value = totalKm;
                            }
                            if (document.getElementById('route_duration_minutes_input')) {
                                document.getElementById('route_duration_minutes_input').value = totalMins;
                            }

                            renderSteps();
                        } else {
                            // Fallback if Directions API fails (e.g. key domain restrictions)
                            waypoints = parsedStops.map((st, idx) => ({
                                sort_order: idx + 1,
                                lat: st.lat,
                                lng: st.lng,
                                instruction: st.label,
                                maneuver: idx === 0 ? 'start' : 'continue',
                            }));
                            renderSteps();
                        }
                    });
                } else {
                    // Fallback JS parsing without API
                    waypoints = parsedStops.map((st, idx) => ({
                        sort_order: idx + 1,
                        lat: st.lat,
                        lng: st.lng,
                        instruction: st.label,
                        maneuver: idx === 0 ? 'start' : 'continue',
                    }));
                    renderSteps();
                }
            });
        }

        if (btnAdd) {
            btnAdd.addEventListener('click', () => {
                waypoints.push({
                    sort_order: waypoints.length + 1,
                    maneuver: 'continue',
                    instruction: '',
                });
                renderSteps();
            });
        }

        renderSteps();
    });
</script>
@endpush
