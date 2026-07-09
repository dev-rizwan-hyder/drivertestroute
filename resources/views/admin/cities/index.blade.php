@extends('layouts.admin')

@section('title', 'Admin Cities')

@push('styles')
    <style>
        .admin-city-page {
            --admin-bg: #f8f9fa;
            --admin-panel: rgba(255, 255, 255, .9);
            --admin-border: rgba(203, 213, 225, .9);
            --admin-muted: #5c6675;
            --admin-blue: #2563eb;
            --admin-cyan: #0891b2;
            color: #212529;
        }

        .admin-city-shell {
            margin: -1.5rem -1rem;
            min-height: calc(100vh - 4rem);
            background:
                radial-gradient(circle at 12% 16%, rgba(37, 99, 235, .08), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .06), transparent 28%),
                linear-gradient(180deg, #f8f9fa, #f1f3f5);
            padding: 2rem 1rem;
        }

        .admin-city-glass {
            border: 1px solid var(--admin-border);
            border-radius: .5rem;
            background: var(--admin-panel);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
        }

        .admin-city-input {
            width: 100%;
            border: 1px solid #cfd8e3;
            border-radius: .5rem;
            background: #ffffff;
            padding: .72rem .85rem;
            color: #212529;
            transition: border-color 200ms ease-out, box-shadow 200ms ease-out;
        }

        .admin-city-input:focus {
            border-color: rgba(37, 99, 235, .52);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: 0;
        }

        .admin-city-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border-radius: .5rem;
            padding: .72rem 1rem;
            font-weight: 800;
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), box-shadow 200ms ease-out, background 200ms ease-out, border-color 200ms ease-out;
        }

        .admin-city-button:hover {
            transform: translateY(-1px);
        }

        .admin-city-primary {
            color: #fff;
            background: linear-gradient(135deg, #1e40af, var(--admin-blue) 52%, var(--admin-cyan));
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
        }

        .admin-city-secondary {
            border: 1px solid rgba(37, 99, 235, .24);
            color: #1d4ed8;
            background: #ffffff;
        }

        .admin-city-row {
            opacity: 0;
            transform: translateY(8px);
            animation: admin-city-row-in 360ms cubic-bezier(.16, 1, .3, 1) forwards;
            animation-delay: calc(var(--row-index, 0) * 35ms);
        }

        .admin-city-row:hover {
            background: #eff6ff;
        }

        .admin-city-dialog {
            width: min(92vw, 34rem);
            border: 1px solid #e0e0e0;
            border-radius: .5rem;
            color: #212529;
            background: #ffffff;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .14);
            backdrop-filter: blur(18px);
        }

        .admin-city-dialog::backdrop {
            background: rgba(15, 23, 42, .35);
            backdrop-filter: blur(4px);
        }

        @keyframes admin-city-row-in {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 1024px) {
            .admin-city-shell {
                margin-right: -2rem;
                margin-left: -2rem;
                padding-right: 2rem;
                padding-left: 2rem;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $nextDirection = $direction === 'asc' ? 'desc' : 'asc';
        $sortLink = fn (string $field) => route('admin.cities.index', array_filter([
            'search' => request('search'),
            'sort' => $field,
            'direction' => $sort === $field ? $nextDirection : 'asc',
        ]));
    @endphp

    <section class="admin-city-page admin-city-shell">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase text-cyan-200">City Management</p>
                    <h1 class="mt-2 text-3xl font-black text-white">Cities</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Manage DriveTest locations, addresses, and route relationships.</p>
                </div>

                <button type="button" class="admin-city-button admin-city-primary" data-modal-open="city-create-modal">
                    Add City
                </button>
            </div>

            <div class="admin-city-glass mb-5 p-4">
                <form method="GET" action="{{ route('admin.cities.index') }}" class="grid gap-3 md:grid-cols-[1fr_auto_auto]">
                    <label class="block">
                        <span class="sr-only">Search cities</span>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by city or address" class="admin-city-input">
                    </label>
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="direction" value="{{ $direction }}">
                    <button type="submit" class="admin-city-button admin-city-primary">Search</button>
                    <a href="{{ route('admin.cities.index') }}" class="admin-city-button admin-city-secondary">Reset</a>
                </form>
            </div>

            <div class="admin-city-glass overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-sm">
                        <thead class="bg-white/[.04] text-left text-xs font-black uppercase text-slate-400">
                            <tr>
                                <th class="px-4 py-4">
                                    <a href="{{ $sortLink('name') }}" class="transition hover:text-cyan-200">City Name</a>
                                </th>
                                <th class="px-4 py-4">
                                    <a href="{{ $sortLink('address') }}" class="transition hover:text-cyan-200">Address</a>
                                </th>
                                <th class="px-4 py-4">
                                    <a href="{{ $sortLink('routes_count') }}" class="transition hover:text-cyan-200">Routes</a>
                                </th>
                                <th class="px-4 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($cities as $city)
                                <tr class="admin-city-row transition" style="--row-index: {{ $loop->index }};">
                                    <td class="px-4 py-4">
                                        <div class="font-black text-white">{{ $city->name }}</div>
                                    </td>
                                    <td class="max-w-xl px-4 py-4 text-slate-300">{{ $city->address }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-md border border-blue-500/20 bg-white/[.06] px-2.5 py-1 font-black text-cyan-100">{{ $city->routes_count }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" class="admin-city-button admin-city-secondary px-3 py-2" data-modal-open="city-edit-{{ $city->id }}">
                                                Edit
                                            </button>
                                            <button type="button" class="admin-city-button admin-city-secondary px-3 py-2" data-modal-open="city-delete-{{ $city->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-slate-400">No cities found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-slate-300">
                {{ $cities->links() }}
            </div>
        </div>

        <dialog id="city-create-modal" class="admin-city-dialog p-0">
            <form method="POST" action="{{ route('admin.cities.store') }}" class="p-5">
                @csrf
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white">Add City</h2>
                        <p class="mt-1 text-sm text-slate-400">Create a DriveTest city location.</p>
                    </div>
                    <button type="button" class="text-slate-400 transition hover:text-white" data-modal-close aria-label="Close modal">Close</button>
                </div>

                <div class="mt-5 grid gap-4">
                    <label class="block">
                        <span class="text-sm font-bold text-slate-300">Name</span>
                        <input type="text" name="name" required class="admin-city-input mt-1">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-slate-300">Address</span>
                        <input type="text" name="address" required class="admin-city-input mt-1">
                    </label>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="admin-city-button admin-city-secondary" data-modal-close>Cancel</button>
                    <button type="submit" class="admin-city-button admin-city-primary">Save City</button>
                </div>
            </form>
        </dialog>

        @foreach($cities as $city)
            <dialog id="city-edit-{{ $city->id }}" class="admin-city-dialog p-0">
                <form method="POST" action="{{ route('admin.cities.update', $city) }}" class="p-5">
                    @csrf
                    @method('PUT')
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black text-white">Edit {{ $city->name }}</h2>
                            <p class="mt-1 text-sm text-slate-400">Updates also sync the city label on linked routes.</p>
                        </div>
                        <button type="button" class="text-slate-400 transition hover:text-white" data-modal-close aria-label="Close modal">Close</button>
                    </div>

                    <div class="mt-5 grid gap-4">
                        <label class="block">
                            <span class="text-sm font-bold text-slate-300">Name</span>
                            <input type="text" name="name" value="{{ $city->name }}" required class="admin-city-input mt-1">
                        </label>
                        <label class="block">
                            <span class="text-sm font-bold text-slate-300">Address</span>
                            <input type="text" name="address" value="{{ $city->address }}" required class="admin-city-input mt-1">
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="admin-city-button admin-city-secondary" data-modal-close>Cancel</button>
                        <button type="submit" class="admin-city-button admin-city-primary">Save Changes</button>
                    </div>
                </form>
            </dialog>

            <dialog id="city-delete-{{ $city->id }}" class="admin-city-dialog p-0">
                <form method="POST" action="{{ route('admin.cities.destroy', $city) }}" class="p-5">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black text-white">Delete {{ $city->name }}?</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                This city has {{ $city->routes_count }} linked {{ \Illuminate\Support\Str::plural('route', $city->routes_count) }}.
                                Linked routes must be moved before the delete can complete.
                            </p>
                        </div>
                        <button type="button" class="text-slate-400 transition hover:text-white" data-modal-close aria-label="Close modal">Close</button>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="admin-city-button admin-city-secondary" data-modal-close>Cancel</button>
                        <button type="submit" class="admin-city-button admin-city-primary">Delete City</button>
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
