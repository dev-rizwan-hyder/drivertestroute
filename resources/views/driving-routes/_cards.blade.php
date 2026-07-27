{{--
    Partial: driving-routes/_cards.blade.php
    Variables expected:
      $routes            – Collection<DrivingRoute>
      $purchases         – Collection<RoutePurchase> keyed by driving_route_id
      $search            – string
      $selectedPackageType – string|null
      $selectedCity      – City|null
--}}
@if($routes->isEmpty())
    <div class="routes-glass px-6 py-14 text-center" id="routes-empty-state">
        <svg class="mx-auto mb-4 h-10 w-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
        </svg>
        <h2 class="text-xl font-black text-slate-700">No routes found</h2>
        <p class="mt-2 text-sm text-slate-500">
            @if($search)
                No routes match <strong class="text-slate-700">"{{ $search }}"</strong>. Try a different keyword or
                <a href="{{ route('driving-routes.index', array_filter(array_merge(request()->query(), ['search' => null]))) }}" class="text-blue-600 underline">clear the search</a>.
            @else
                Try another city or package filter, or check back as new routes are added.
            @endif
        </p>
        @if($search || $selectedPackageType || $selectedCity)
            <a href="{{ route('driving-routes.index') }}" class="routes-button routes-button-secondary mt-5 inline-flex">
                Clear all filters
            </a>
        @endif
    </div>
@else
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($routes as $drivingRoute)
            @php
                $purchase = $purchases->get($drivingRoute->id);
                $remainingStarts = $purchase?->remainingStarts() ?? 0;
                $canOpenMap = auth()->user()?->is_admin || $remainingStarts > 0;
                $routeCity = $drivingRoute->relationLoaded('cityModel') ? $drivingRoute->cityModel : null;
                $cityName = $routeCity?->name ?? $drivingRoute->city;
                $cityAddress = $routeCity?->address;
            @endphp
            <article class="routes-glass routes-card flex min-h-[25rem] flex-col justify-between overflow-hidden">
                <div class="routes-card-visual relative h-36">
                    <svg class="h-full w-full" viewBox="0 0 420 180" fill="none" aria-hidden="true">
                        <path d="M0 44H420M0 96H420M0 148H420M70 0V180M154 0V180M238 0V180M322 0V180" stroke="rgba(148,163,184,.12)" />
                        <path d="M34 142 C96 68 156 110 210 54 C274 -12 322 62 386 30" stroke="url(#routeCard{{ $drivingRoute->id }})" stroke-width="7" stroke-linecap="round" />
                        <circle cx="34" cy="142" r="9" fill="#38bdf8" />
                        <circle cx="386" cy="30" r="9" fill="#2563eb" />
                        <defs>
                            <linearGradient id="routeCard{{ $drivingRoute->id }}" x1="34" x2="386" y1="142" y2="30">
                                <stop stop-color="#1e3a8a" />
                                <stop offset=".55" stop-color="#2563eb" />
                                <stop offset="1" stop-color="#06b6d4" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="mb-2">
                                <span class="inline-flex items-center rounded bg-blue-500/10 px-2 py-0.5 text-xs font-extrabold text-cyan-300 border border-blue-500/20 uppercase">
                                    {{ $drivingRoute->package_type === 'g1' ? 'G2 Test Routes' : 'G Test Routes' }}
                                </span>
                            </div>
                            <h3 class="text-xl font-black text-white">{{ $drivingRoute->title }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ $cityName }}, {{ $drivingRoute->province }}</p>
                            @if($cityAddress)
                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ $cityAddress }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 rounded-md border border-blue-500/20 bg-white/[.06] px-3 py-2 text-right text-white">
                            <div class="text-xs text-slate-400">Price</div>
                            <div class="font-black">${{ number_format((float) $drivingRoute->price, 2) }}</div>
                        </div>
                    </div>

                    @if($drivingRoute->description)
                        <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-400">{{ $drivingRoute->description }}</p>
                    @endif

                    <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Start</dt>
                            <dd class="mt-1 font-black text-white">{{ $drivingRoute->start_label ?: 'Start point' }}</dd>
                        </div>
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Destination</dt>
                            <dd class="mt-1 font-black text-white">{{ $drivingRoute->destination_label ?: 'Destination' }}</dd>
                        </div>
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Duration</dt>
                            <dd class="mt-1 font-black text-white">{{ $drivingRoute->route_duration_minutes ? $drivingRoute->route_duration_minutes.' mins' : 'Ready' }}</dd>
                        </div>
                        <div class="rounded-md bg-white/[.06] p-3">
                            <dt class="font-bold text-slate-500">Starts</dt>
                            <dd class="mt-1 font-black text-white">{{ $drivingRoute->access_limit ?? 1 }}</dd>
                        </div>
                    </dl>

                    <div class="mt-auto flex flex-wrap items-center gap-2 pt-5">
                        @if($canOpenMap)
                            <a href="{{ route('driving-routes.show', $drivingRoute) }}"
                               onclick="return confirmOpenMap(event, {{ $remainingStarts }});"
                               class="routes-button routes-button-primary flex-1">
                                Open Map
                            </a>
                            @if(! auth()->user()?->is_admin)
                                <span class="rounded-md border border-blue-500/20 bg-white/[.06] px-3 py-2 text-sm font-black text-cyan-100">
                                    {{ $remainingStarts }} left
                                </span>
                            @endif
                        @elseif(auth()->check())
                            <a href="{{ route('driving-routes.checkout', $drivingRoute) }}" class="routes-button routes-button-primary flex-1">
                                {{ $purchase ? 'Buy More Starts' : 'Buy Route' }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="routes-button routes-button-primary flex-1">
                                Log In to Buy
                            </a>
                        @endif

                        @if($drivingRoute->preview_pdf_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($drivingRoute->preview_pdf_path) }}" target="_blank" class="routes-button routes-button-secondary">
                                PDF
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endif
