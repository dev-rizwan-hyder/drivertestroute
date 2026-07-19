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
                <span class="text-sm font-medium text-stone-700">Package Type</span>
                <select name="package_type" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="g1" @selected(old('package_type', $route->package_type) === 'g1')>G1 Package (G2 Road Test)</option>
                    <option value="g2" @selected(old('package_type', $route->package_type) === 'g2')>G2 Package (G Road Test)</option>
                </select>
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
        <h2 class="text-lg font-bold text-stone-950">Visual Route Builder</h2>
        <p class="mt-1 text-sm text-stone-600">Click on the map or search addresses below to add waypoints. Drag markers to adjust paths. Start and Midpoint details are updated automatically.</p>

        <!-- Import from Google Maps Link Box -->
        <div class="mt-4 p-4 bg-emerald-50/40 rounded-xl border border-emerald-200/60 shadow-sm">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block mb-1">Import Entire Route from Google Maps Link</span>
            <span class="text-xs text-stone-600 block mb-3">Copy the directions URL from Google Maps (e.g. `https://www.google.com/maps/dir/PointA/PointB/...`) and paste it below to load all stops instantly.</span>
            <div class="flex gap-2">
                <input type="text" id="import-url-input" placeholder="Paste Google Maps directions URL here..." class="flex-1 rounded-md border border-stone-300 px-3 py-2 text-sm text-stone-950 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                <button type="button" id="btn-import-url" class="rounded-md bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 font-semibold text-sm transition shrink-0">Import Route</button>
            </div>
        </div>

        <!-- Place Search Input -->
        <div class="mt-4 flex gap-2">
            <input type="text" id="map-search-input" placeholder="Search address, landmark, or street name to add a waypoint..." class="flex-1 rounded-md border border-stone-300 px-3 py-2 text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            <button type="button" id="btn-add-search-point" class="rounded-md bg-stone-900 hover:bg-stone-850 text-white px-4 py-2 font-semibold text-sm transition">Add Point</button>
        </div>

        <!-- Google Map Container -->
        <div id="admin-route-map" class="mt-4 h-[500px] w-full rounded-lg border border-stone-200 shadow-inner z-0"></div>

        <!-- Start & Destination Summary Info (Required by Backend) -->
        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <div class="rounded-md bg-stone-50 p-4 border border-stone-150 shadow-sm">
                <h3 class="font-bold text-emerald-800 text-sm flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-600"></span>
                    Start Point (Waypoint 1)
                </h3>
                <div class="mt-3 grid gap-3">
                    <label class="block">
                        <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Start Label</span>
                        <input type="text" id="route-start-label-input" name="start_label" value="{{ old('start_label', $route->start_label) }}" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-sm text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none">
                    </label>
                    <input type="hidden" id="route-start-lat-input" name="start_lat" value="{{ old('start_lat', $route->start_lat) }}">
                    <input type="hidden" id="route-start-lng-input" name="start_lng" value="{{ old('start_lng', $route->start_lng) }}">
                </div>
            </div>

            <div class="rounded-md bg-stone-50 p-4 border border-stone-150 shadow-sm">
                <h3 class="font-bold text-orange-700 text-sm flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-orange-600"></span>
                    Midpoint / Destination
                </h3>
                <div class="mt-3 grid gap-3">
                    <label class="block">
                        <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Destination Label</span>
                        <input type="text" id="route-destination-label-input" name="destination_label" value="{{ old('destination_label', $route->destination_label) }}" required class="mt-1 block w-full rounded-md border border-stone-300 px-3 py-2 text-sm text-stone-950 shadow-sm focus:border-blue-500 focus:outline-none">
                    </label>
                    <input type="hidden" id="route-end-lat-input" name="end_lat" value="{{ old('end_lat', $route->end_lat) }}">
                    <input type="hidden" id="route-end-lng-input" name="end_lng" value="{{ old('end_lng', $route->end_lng) }}">
                </div>
            </div>
        </div>

        <!-- Waypoints List -->
        <h3 class="mt-6 font-extrabold text-stone-950 text-base">Route Waypoints & Turn Directions</h3>
        <div id="route-builder-waypoints" class="mt-3 space-y-3">
            <!-- Dynamically populated via JavaScript -->
        </div>

        <!-- Length & Duration sync helper -->
        <div class="mt-5 flex flex-wrap justify-between items-center bg-stone-50 p-4 rounded-xl border border-stone-200 gap-3">
            <div class="text-sm font-semibold text-stone-700">
                Calculated driving path: <span id="auto-dist-text" class="text-stone-950 font-black">-- km</span> | <span id="auto-dur-text" class="text-stone-950 font-black">-- mins</span>
            </div>
            <button type="button" id="btn-sync-summary" class="rounded-md border border-blue-300 hover:bg-blue-50 px-3.5 py-1.5 text-xs font-black text-blue-700 transition">
                Apply Distance & Duration
            </button>
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

@push('scripts')
<script>
    let map;
    let directionsService;
    let directionsRenderer;
    let autocomplete;
    let waypoints = @json($formPoints);
    let markers = [];
    let midpointIndex = null;

    const endLat = parseFloat(document.getElementById('route-end-lat-input').value);
    const endLng = parseFloat(document.getElementById('route-end-lng-input').value);

    function initRouteBuilderMap() {
        const initialCenter = getInitialMapCenter();

        map = new google.maps.Map(document.getElementById('admin-route-map'), {
            center: initialCenter,
            zoom: 14,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: '#2563eb',
                strokeOpacity: 0.85,
                strokeWeight: 6,
            }
        });

        // Set up search box
        const searchInput = document.getElementById('map-search-input');
        autocomplete = new google.maps.places.Autocomplete(searchInput);
        autocomplete.bindTo('bounds', map);

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (place.geometry && place.geometry.location) {
                addPlaceWaypoint(place);
                searchInput.value = '';
            }
        });

        document.getElementById('btn-add-search-point').addEventListener('click', () => {
            const address = searchInput.value.trim();
            if (address) {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ address: address }, (results, status) => {
                    if (status === 'OK' && results?.[0]) {
                        addPlaceWaypoint(results[0]);
                        searchInput.value = '';
                    } else {
                        alert('Location not found.');
                    }
                });
            }
        });

        // Click map listener
        map.addListener('click', (event) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            addCoordinateWaypoint(lat, lng);
        });

        // URL importer listener
        const importBtn = document.getElementById('btn-import-url');
        if (importBtn) {
            importBtn.addEventListener('click', () => {
                const url = document.getElementById('import-url-input').value.trim();
                if (url) {
                    importRouteFromUrl(url);
                } else {
                    alert('Please enter a Google Maps Directions URL.');
                }
            });
        }

        // Set midpoint
        if (Number.isFinite(endLat) && Number.isFinite(endLng)) {
            let minDistance = Infinity;
            waypoints.forEach((wp, idx) => {
                const wpLat = parseFloat(wp.lat);
                const wpLng = parseFloat(wp.lng);
                if (Number.isFinite(wpLat) && Number.isFinite(wpLng)) {
                    const dist = Math.hypot(wpLat - endLat, wpLng - endLng);
                    if (dist < minDistance) {
                        minDistance = dist;
                        midpointIndex = idx;
                    }
                }
            });
        }
        if (midpointIndex === null && waypoints.length > 0) {
            midpointIndex = Math.floor(waypoints.length / 2);
        }

        renderWaypoints();
        calculateRoute();
    }

    function getInitialMapCenter() {
        for (const wp of waypoints) {
            const lat = parseFloat(wp.lat);
            const lng = parseFloat(wp.lng);
            if (Number.isFinite(lat) && Number.isFinite(lng)) {
                return { lat, lng };
            }
        }
        
        const selectedCity = document.querySelector('select[name="city_id"]');
        if (selectedCity && selectedCity.options[selectedCity.selectedIndex].text.includes('Karachi')) {
            return { lat: 24.8916, lng: 67.1546 };
        }
        return { lat: 43.6532, lng: -79.3832 }; // Toronto default
    }

    function addPlaceWaypoint(place) {
        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();
        const address = place.formatted_address || place.name;
        
        const newWaypoint = {
            lat: lat,
            lng: lng,
            instruction: 'Continue onto ' + cleanAddress(address),
            maneuver: 'continue',
            distance_km: null,
            duration: null,
            sort_order: waypoints.length + 1
        };

        waypoints.push(newWaypoint);
        if (waypoints.length === 1) {
            midpointIndex = 0;
        }
        renderWaypoints();
        calculateRoute();
        map.panTo({ lat, lng });
    }

    function addCoordinateWaypoint(lat, lng) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: { lat, lng } }, (results, status) => {
            let address = `Waypoint ${waypoints.length + 1}`;
            if (status === 'OK' && results?.[0]) {
                address = results[0].formatted_address;
            }

            const newWaypoint = {
                lat: lat,
                lng: lng,
                instruction: 'Continue onto ' + cleanAddress(address),
                maneuver: 'continue',
                distance_km: null,
                duration: null,
                sort_order: waypoints.length + 1
            };

            waypoints.push(newWaypoint);
            if (waypoints.length === 1) {
                midpointIndex = 0;
            }
            renderWaypoints();
            calculateRoute();
        });
    }

    function cleanAddress(fullAddress) {
        if (!fullAddress) return '';
        const parts = fullAddress.split(',');
        if (parts.length >= 2) {
            return (parts[0].trim() + ', ' + parts[1].trim());
        }
        return fullAddress;
    }

    function parseGoogleMapsUrl(url) {
        const addresses = [];
        try {
            const decodedUrl = decodeURIComponent(url);
            const urlObj = new URL(url);
            
            if (urlObj.pathname.includes('/maps/dir/')) {
                const dirPart = urlObj.pathname.split('/maps/dir/')[1];
                if (dirPart) {
                    const segments = dirPart.split('/');
                    segments.forEach(segment => {
                        if (segment && !segment.startsWith('@') && !segment.startsWith('data=')) {
                            const decoded = decodeURIComponent(segment.replace(/\+/g, ' '));
                            if (decoded.trim() !== '') {
                                addresses.push(decoded.trim());
                            }
                        }
                    });
                }
            } else {
                const params = new URLSearchParams(urlObj.search);
                const origin = params.get('origin');
                const destination = params.get('destination');
                const waypointsParam = params.get('waypoints');

                if (origin) addresses.push(origin);
                if (waypointsParam) {
                    waypointsParam.split('|').forEach(wp => {
                        if (wp.trim() !== '') addresses.push(wp.trim());
                    });
                }
                if (destination) addresses.push(destination);
            }
        } catch (e) {
            const dirMatch = url.match(/\/dir\/([^\?]+)/);
            if (dirMatch && dirMatch[1]) {
                const segments = dirMatch[1].split('/');
                segments.forEach(segment => {
                    if (segment && !segment.startsWith('@') && !segment.startsWith('data=')) {
                        const decoded = decodeURIComponent(segment.replace(/\+/g, ' '));
                        if (decoded.trim() !== '') {
                            addresses.push(decoded.trim());
                        }
                    }
                });
            }
        }
        return addresses;
    }

    async function importRouteFromUrl(url) {
        const addresses = parseGoogleMapsUrl(url);
        if (addresses.length === 0) {
            alert('Could not find any locations in that URL. Please ensure it is a Google Maps Directions URL from your browser address bar.');
            return;
        }

        const importBtn = document.getElementById('btn-import-url');
        const origText = importBtn.textContent;
        importBtn.disabled = true;
        importBtn.textContent = 'Importing...';

        const geocoder = new google.maps.Geocoder();
        const newWaypoints = [];

        for (let i = 0; i < addresses.length; i++) {
            importBtn.textContent = `Importing (${i + 1}/${addresses.length})...`;
            const addr = addresses[i];
            const coordMatch = addr.match(/^(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)$/);

            try {
                const result = await new Promise((resolve, reject) => {
                    const query = coordMatch
                        ? { location: { lat: parseFloat(coordMatch[1]), lng: parseFloat(coordMatch[2]) } }
                        : { address: addr };

                    geocoder.geocode(query, (results, status) => {
                        if (status === 'OK' && results?.[0]) {
                            resolve(results[0]);
                        } else {
                            reject(status);
                        }
                    });
                });

                const lat = coordMatch ? parseFloat(coordMatch[1]) : result.geometry.location.lat();
                const lng = coordMatch ? parseFloat(coordMatch[2]) : result.geometry.location.lng();
                const addressText = result.formatted_address || addr;

                newWaypoints.push({
                    lat: lat,
                    lng: lng,
                    instruction: 'Continue onto ' + cleanAddress(addressText),
                    maneuver: 'continue',
                    distance_km: null,
                    duration: null,
                    sort_order: newWaypoints.length + 1
                });
            } catch (err) {
                console.warn(`Could not geocode waypoint: ${addr}. Status: ${err}`);
                // Fallback to exact coordinates if geocoder lookup fails for a coord pair
                if (coordMatch) {
                    const lat = parseFloat(coordMatch[1]);
                    const lng = parseFloat(coordMatch[2]);
                    newWaypoints.push({
                        lat: lat,
                        lng: lng,
                        instruction: `Waypoint at ${lat.toFixed(5)}, ${lng.toFixed(5)}`,
                        maneuver: 'continue',
                        distance_km: null,
                        duration: null,
                        sort_order: newWaypoints.length + 1
                    });
                }
            }
        }

        importBtn.disabled = false;
        importBtn.textContent = origText;

        if (newWaypoints.length > 0) {
            waypoints = newWaypoints;
            midpointIndex = Math.floor(waypoints.length / 2);
            renderWaypoints();
            calculateRoute();
            map.panTo({ lat: waypoints[0].lat, lng: waypoints[0].lng });
            document.getElementById('import-url-input').value = '';
            
            document.getElementById('route-start-label-input').value = cleanAddress(waypoints[0].address || waypoints[0].instruction);
            document.getElementById('route-destination-label-input').value = cleanAddress(waypoints[midpointIndex].address || waypoints[midpointIndex].instruction);
            updateStartMidpointInputs();
            
            alert(`Imported ${waypoints.length} route points successfully!`);
        } else {
            alert('Failed to geocode any locations from the pasted URL. Please try again.');
        }
    }

    function renderWaypoints() {
        markers.forEach(m => m.setMap(null));
        markers = [];

        const container = document.getElementById('route-builder-waypoints');
        container.innerHTML = '';

        if (waypoints.length === 0) {
            container.innerHTML = '<div class="text-sm text-stone-500 bg-stone-50 p-4 rounded-md border border-stone-200 text-center">No waypoints added yet. Click on the map or search above to add your first point.</div>';
            updateStartMidpointInputs();
            return;
        }

        if (midpointIndex === null || midpointIndex >= waypoints.length) {
            midpointIndex = Math.floor(waypoints.length / 2);
        }

        waypoints.forEach((wp, index) => {
            const lat = parseFloat(wp.lat);
            const lng = parseFloat(wp.lng);
            const instruction = wp.instruction || '';
            const maneuver = wp.maneuver || 'continue';
            const distanceKm = wp.distance_km || 0;
            const duration = wp.duration || '';
            
            let address = wp.address || wp.instruction || `Waypoint at ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            if (address.toLowerCase().startsWith('continue onto ') || address.toLowerCase().startsWith('turn left onto ') || address.toLowerCase().startsWith('turn right onto ')) {
                address = address.replace(/^(continue|turn left|turn right) onto /i, '');
            }

            const isStart = index === 0;
            const isMidpoint = index === midpointIndex;
            
            let markerColor = '#64748b';
            let markerLabel = (index + 1).toString();
            
            if (isStart) {
                markerColor = '#047857';
                markerLabel = 'S';
            } else if (isMidpoint) {
                markerColor = '#ea580c';
                markerLabel = 'M';
            }

            const marker = new google.maps.Marker({
                position: { lat, lng },
                map: map,
                draggable: true,
                label: {
                    text: markerLabel,
                    color: 'white',
                    fontWeight: 'bold',
                    fontSize: '11px'
                },
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: markerColor,
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2,
                    scale: 13,
                }
            });

            marker.addListener('dragend', (event) => {
                updateWaypointPosition(index, event.latLng.lat(), event.latLng.lng());
            });

            markers.push(marker);

            const row = document.createElement('div');
            row.className = `waypoint-row flex items-start gap-4 rounded-xl border border-stone-200 bg-white p-4 shadow-sm transition hover:shadow-md`;
            
            if (isStart) {
                row.classList.add('border-emerald-300', 'bg-emerald-50/[.03]');
            } else if (isMidpoint) {
                row.classList.add('border-orange-300', 'bg-orange-50/[.03]');
            }

            row.innerHTML = `
                <div class="flex flex-col items-center gap-1.5 pt-1 shrink-0">
                    <div class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-black transition ${
                        isStart ? 'bg-emerald-700 text-white' : (isMidpoint ? 'bg-orange-600 text-white shadow-sm' : 'bg-stone-100 text-stone-700')
                    }">
                        ${markerLabel}
                    </div>
                    <button type="button" class="btn-move-up text-stone-400 hover:text-stone-700 disabled:opacity-30 transition" ${index === 0 ? 'disabled' : ''} title="Move Up">
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button type="button" class="btn-move-down text-stone-400 hover:text-stone-700 disabled:opacity-30 transition" ${index === waypoints.length - 1 ? 'disabled' : ''} title="Move Down">
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 grid gap-3 md:grid-cols-6 items-center">
                    <div class="md:col-span-2 min-w-0">
                        <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Address</span>
                        <span class="font-bold text-sm text-stone-900 block truncate" title="${address}">${address}</span>
                        <span class="text-[10px] text-stone-500 font-medium font-mono">${lat.toFixed(5)}, ${lng.toFixed(5)}</span>
                        
                        <input type="hidden" name="points[${index}][sort_order]" value="${index + 1}">
                        <input type="hidden" name="points[${index}][lat]" value="${lat}">
                        <input type="hidden" name="points[${index}][lng]" value="${lng}">
                        <input type="hidden" name="points[${index}][distance_km]" value="${distanceKm}">
                        <input type="hidden" name="points[${index}][duration]" value="${duration}">
                    </div>

                    <div class="md:col-span-1">
                        <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block mb-1">Maneuver</span>
                        <select name="points[${index}][maneuver]" class="w-full rounded-md border border-stone-300 px-2 py-1 text-sm text-stone-950 focus:outline-none">
                            <option value="continue" ${maneuver === 'continue' ? 'selected' : ''}>Continue</option>
                            <option value="turn_left" ${maneuver === 'turn_left' ? 'selected' : ''}>Turn left</option>
                            <option value="turn_right" ${maneuver === 'turn_right' ? 'selected' : ''}>Turn right</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block mb-1">Instruction</span>
                        <input type="text" name="points[${index}][instruction]" value="${instruction}" placeholder="Driving instruction..." required class="w-full rounded-md border border-stone-300 px-3 py-1 text-sm text-stone-950 focus:outline-none">
                    </div>

                    <div class="md:col-span-1 flex items-center justify-end gap-3 pt-4 md:pt-0">
                        <button type="button" class="btn-set-midpoint flex h-8 w-8 items-center justify-center rounded-full border transition active:scale-90 ${
                            isMidpoint 
                                ? 'bg-orange-600 border-orange-600 text-white shadow-md shadow-orange-500/20' 
                                : 'border-stone-200 hover:border-orange-200 bg-white hover:bg-orange-50 text-stone-400 hover:text-orange-500'
                        }" title="${isMidpoint ? 'Current Midpoint' : 'Set as Midpoint'}">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>

                        <button type="button" class="btn-remove-wp flex h-8 w-8 items-center justify-center rounded-full border border-stone-200 hover:border-red-200 bg-white hover:bg-red-50 text-stone-400 hover:text-red-500 transition active:scale-90" title="Delete Point">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            row.querySelector('.btn-move-up').addEventListener('click', () => {
                if (index > 0) {
                    const temp = waypoints[index];
                    waypoints[index] = waypoints[index - 1];
                    waypoints[index - 1] = temp;
                    
                    if (midpointIndex === index) {
                        midpointIndex = index - 1;
                    } else if (midpointIndex === index - 1) {
                        midpointIndex = index;
                    }

                    renderWaypoints();
                    calculateRoute();
                }
            });

            row.querySelector('.btn-move-down').addEventListener('click', () => {
                if (index < waypoints.length - 1) {
                    const temp = waypoints[index];
                    waypoints[index] = waypoints[index + 1];
                    waypoints[index + 1] = temp;

                    if (midpointIndex === index) {
                        midpointIndex = index + 1;
                    } else if (midpointIndex === index + 1) {
                        midpointIndex = index;
                    }

                    renderWaypoints();
                    calculateRoute();
                }
            });

            row.querySelector('.btn-set-midpoint').addEventListener('click', () => {
                midpointIndex = index;
                renderWaypoints();
                calculateRoute();
            });

            row.querySelector('.btn-remove-wp').addEventListener('click', () => {
                waypoints.splice(index, 1);
                if (midpointIndex === index) {
                    midpointIndex = Math.floor(waypoints.length / 2);
                } else if (midpointIndex > index) {
                    midpointIndex -= 1;
                }
                renderWaypoints();
                calculateRoute();
            });

            row.querySelector('input[type="text"]').addEventListener('input', (e) => {
                wp.instruction = e.target.value;
            });

            row.querySelector('select').addEventListener('change', (e) => {
                wp.maneuver = e.target.value;
            });

            container.appendChild(row);
        });

        updateStartMidpointInputs();
    }

    function updateWaypointPosition(index, lat, lng) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: { lat, lng } }, (results, status) => {
            let address = `Waypoint ${index + 1}`;
            if (status === 'OK' && results?.[0]) {
                address = results[0].formatted_address;
            }

            waypoints[index].lat = lat;
            waypoints[index].lng = lng;
            waypoints[index].address = address;
            
            if (waypoints[index].instruction.toLowerCase().startsWith('continue onto ') || waypoints[index].instruction.includes('Waypoint')) {
                waypoints[index].instruction = 'Continue onto ' + cleanAddress(address);
            }

            renderWaypoints();
            calculateRoute();
        });
    }

    function updateStartMidpointInputs() {
        const startLabelInput = document.getElementById('route-start-label-input');
        const startLatInput = document.getElementById('route-start-lat-input');
        const startLngInput = document.getElementById('route-start-lng-input');
        
        if (waypoints.length > 0) {
            const startWp = waypoints[0];
            let address = startWp.address || startWp.instruction || 'Start Point';
            address = address.replace(/^(continue|turn left|turn right) onto /i, '');
            
            if (!startLabelInput.value || startLabelInput.value === 'Start Point') {
                startLabelInput.value = address;
            }
            startLatInput.value = startWp.lat;
            startLngInput.value = startWp.lng;
        } else {
            startLatInput.value = '';
            startLngInput.value = '';
        }

        const destLabelInput = document.getElementById('route-destination-label-input');
        const destLatInput = document.getElementById('route-end-lat-input');
        const destLngInput = document.getElementById('route-end-lng-input');

        if (waypoints.length > 0 && midpointIndex !== null && midpointIndex < waypoints.length) {
            const midWp = waypoints[midpointIndex];
            let address = midWp.address || midWp.instruction || 'Midpoint';
            address = address.replace(/^(continue|turn left|turn right) onto /i, '');

            if (!destLabelInput.value || destLabelInput.value === 'Midpoint') {
                destLabelInput.value = address;
            }
            destLatInput.value = midWp.lat;
            destLngInput.value = midWp.lng;
        } else {
            destLatInput.value = '';
            destLngInput.value = '';
        }
    }

    async function calculateRoute() {
        if (waypoints.length < 2) {
            if (directionsRenderer) directionsRenderer.setDirections({ routes: [] });
            document.getElementById('auto-dist-text').textContent = '-- km';
            document.getElementById('auto-dur-text').textContent = '-- mins';
            return;
        }

        const segments = [];
        const chunkSize = 20;
        for (let i = 0; i < waypoints.length - 1; i += chunkSize - 1) {
            const chunk = waypoints.slice(i, i + chunkSize);
            if (chunk.length >= 2) {
                segments.push(chunk);
            }
        }

        const promises = segments.map(segment => {
            const origin = { lat: parseFloat(segment[0].lat), lng: parseFloat(segment[0].lng) };
            const destination = { lat: parseFloat(segment[segment.length - 1].lat), lng: parseFloat(segment[segment.length - 1].lng) };
            const segmentWaypoints = segment.slice(1, -1).map(p => ({
                location: { lat: parseFloat(p.lat), lng: parseFloat(p.lng) },
                stopover: false
            }));

            return new Promise((resolve, reject) => {
                directionsService.route({
                    origin,
                    destination,
                    waypoints: segmentWaypoints,
                    optimizeWaypoints: false,
                    travelMode: google.maps.TravelMode.DRIVING
                }, (result, status) => {
                    if (status === 'OK' && result) {
                        resolve(result);
                    } else {
                        reject(status);
                    }
                });
            });
        });

        try {
            const results = await Promise.all(promises);
            directionsRenderer.setDirections(results[0]);
            
            // Draw additional segments if any (multiple renderer instances)
            // But usually 1 segment is active.
            
            let totalDistanceMeters = 0;
            let totalDurationSeconds = 0;

            results.forEach((res, resIdx) => {
                res.routes[0].legs.forEach(leg => {
                    totalDistanceMeters += leg.distance?.value ?? 0;
                    totalDurationSeconds += leg.duration?.value ?? 0;
                });
            });

            const totalDistKm = (totalDistanceMeters / 1000).toFixed(1);
            const totalDurMins = Math.max(1, Math.round(totalDurationSeconds / 60));

            document.getElementById('auto-dist-text').textContent = totalDistKm + ' km';
            document.getElementById('auto-dur-text').textContent = totalDurMins + ' mins';

            // Sync button handler
            const syncBtn = document.getElementById('btn-sync-summary');
            const syncHandler = () => {
                const durationField = document.querySelector('input[name="route_duration_minutes"]');
                const lengthField = document.querySelector('input[name="route_length_km"]');
                if (durationField) durationField.value = totalDurMins;
                if (lengthField) lengthField.value = totalDistKm;
                
                syncBtn.textContent = 'Applied!';
                syncBtn.classList.remove('text-blue-700', 'border-blue-300');
                syncBtn.classList.add('text-emerald-700', 'border-emerald-300', 'bg-emerald-50');
                setTimeout(() => {
                    syncBtn.textContent = 'Apply Distance & Duration';
                    syncBtn.classList.add('text-blue-700', 'border-blue-300');
                    syncBtn.classList.remove('text-emerald-700', 'border-emerald-300', 'bg-emerald-50');
                }, 2000);
            };

            syncBtn.replaceWith(syncBtn.cloneNode(true));
            document.getElementById('btn-sync-summary').addEventListener('click', syncHandler);

        } catch (e) {
            console.error('Route calculation failed:', e);
        }
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places&loading=async&callback=initRouteBuilderMap"></script>
@endpush
