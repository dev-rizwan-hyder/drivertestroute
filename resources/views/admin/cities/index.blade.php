@extends('layouts.admin')

@section('title', 'Cities')

@section('content')
    @php
        $nextDirection = $direction === 'asc' ? 'desc' : 'asc';
        $sortLink = fn (string $field) => route('admin.cities.index', array_filter([
            'search' => request('search'),
            'sort' => $field,
            'direction' => $sort === $field ? $nextDirection : 'asc',
        ]));
    @endphp

    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">City Management</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900 tracking-tight">Cities</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500">Manage DriveTest locations, addresses, and route relationships.</p>
            </div>

            <button type="button" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition" data-modal-open="city-create-modal">
                Add City
            </button>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
            <form method="GET" action="{{ route('admin.cities.index') }}" class="grid gap-3 md:grid-cols-[1fr_auto_auto]">
                <label class="block">
                    <span class="sr-only">Search cities</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by city or address" class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 outline-none transition">
                </label>
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">Search</button>
                <a href="{{ route('admin.cities.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Reset</a>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/70 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">
                                <a href="{{ $sortLink('name') }}" class="transition hover:text-blue-700 flex items-center gap-1.5">
                                    City Name
                                    @if($sort === 'name')
                                        <span class="text-[10px]">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3">
                                <a href="{{ $sortLink('address') }}" class="transition hover:text-blue-700 flex items-center gap-1.5">
                                    Address
                                    @if($sort === 'address')
                                        <span class="text-[10px]">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3">
                                <a href="{{ $sortLink('routes_count') }}" class="transition hover:text-blue-700 flex items-center gap-1.5">
                                    Routes
                                    @if($sort === 'routes_count')
                                        <span class="text-[10px]">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($cities as $city)
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 leading-snug">{{ $city->name }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-medium max-w-xl truncate">{{ $city->address }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-700/10">{{ $city->routes_count }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition" data-modal-open="city-edit-{{ $city->id }}">
                                            Edit
                                        </button>
                                        <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition" data-modal-open="city-delete-{{ $city->id }}">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-slate-500">No cities found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $cities->links() }}
        </div>

        <!-- Add City Modal -->
        <dialog id="city-create-modal" class="rounded-xl border border-slate-200 bg-white shadow-xl max-w-md w-full p-6 outline-none backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm">
            <form method="POST" action="{{ route('admin.cities.store') }}">
                @csrf
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Add City</h2>
                        <p class="mt-1 text-sm text-slate-500">Create a DriveTest city location.</p>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-600 transition" data-modal-close aria-label="Close modal">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-5 grid gap-4">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Name</span>
                        <input type="text" name="name" required class="w-full mt-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 outline-none transition">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Address</span>
                        <input type="text" name="address" required class="w-full mt-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 outline-none transition">
                    </label>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition" data-modal-close>Cancel</button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition">Save City</button>
                </div>
            </form>
        </dialog>

        @foreach($cities as $city)
            <!-- Edit City Modal -->
            <dialog id="city-edit-{{ $city->id }}" class="rounded-xl border border-slate-200 bg-white shadow-xl max-w-md w-full p-6 outline-none backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm">
                <form method="POST" action="{{ route('admin.cities.update', $city) }}">
                    @csrf
                    @method('PUT')
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">Edit {{ $city->name }}</h2>
                            <p class="mt-1 text-sm text-slate-500">Updates also sync the city label on linked routes.</p>
                        </div>
                        <button type="button" class="text-slate-400 hover:text-slate-600 transition" data-modal-close aria-label="Close modal">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-5 grid gap-4">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Name</span>
                            <input type="text" name="name" value="{{ $city->name }}" required class="w-full mt-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 outline-none transition">
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Address</span>
                            <input type="text" name="address" value="{{ $city->address }}" required class="w-full mt-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 outline-none transition">
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition" data-modal-close>Cancel</button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition">Save Changes</button>
                    </div>
                </form>
            </dialog>

            <!-- Delete City Modal -->
            <dialog id="city-delete-{{ $city->id }}" class="rounded-xl border border-slate-200 bg-white shadow-xl max-w-md w-full p-6 outline-none backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm">
                <form method="POST" action="{{ route('admin.cities.destroy', $city) }}">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">Delete {{ $city->name }}?</h2>
                                <p class="mt-2 text-sm leading-relaxed text-slate-500">
                                    This city has <span class="font-semibold text-slate-800">{{ $city->routes_count }} linked {{ \Illuminate\Support\Str::plural('route', $city->routes_count) }}</span>.
                                    All linked routes must be updated or deleted before this city can be removed.
                                </p>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-slate-600 transition" data-modal-close aria-label="Close modal">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex justify-end gap-2 mt-2">
                            <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition" data-modal-close>Cancel</button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">Delete City</button>
                        </div>
                    </div>
                </form>
            </dialog>
        @endforeach
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-modal-open]').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById(button.dataset.modalOpen)?.showModal();
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach((button) => {
            button.addEventListener('click', () => {
                button.closest('dialog')?.close();
            });
        });
    </script>
@endpush
