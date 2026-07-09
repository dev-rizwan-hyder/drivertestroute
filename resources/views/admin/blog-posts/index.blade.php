@extends('layouts.admin')

@section('title', 'Blog Posts')

@push('styles')
    <style>
        .admin-blog-page {
            --admin-bg: #f8f9fa;
            --admin-panel: rgba(255, 255, 255, .9);
            --admin-border: rgba(203, 213, 225, .9);
            --admin-muted: #5c6675;
            --admin-blue: #2563eb;
            --admin-cyan: #0891b2;
            color: #212529;
        }

        .admin-blog-shell {
            margin: -1.5rem -1rem;
            min-height: calc(100vh - 4rem);
            background:
                radial-gradient(circle at 12% 16%, rgba(37, 99, 235, .08), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .06), transparent 28%),
                linear-gradient(180deg, #f8f9fa, #f1f3f5);
            padding: 2rem 1rem;
        }

        .admin-blog-glass {
            border: 1px solid var(--admin-border);
            border-radius: .5rem;
            background: var(--admin-panel);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
        }

        .admin-blog-input {
            width: 100%;
            border: 1px solid #cfd8e3;
            border-radius: .5rem;
            background: #ffffff;
            padding: .72rem .85rem;
            color: #212529;
            transition: border-color 200ms ease-out, box-shadow 200ms ease-out;
        }

        .admin-blog-input:focus {
            border-color: rgba(37, 99, 235, .52);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: 0;
        }

        .admin-blog-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border-radius: .5rem;
            padding: .72rem 1rem;
            font-weight: 800;
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), box-shadow 200ms ease-out, background 200ms ease-out, border-color 200ms ease-out;
        }

        .admin-blog-button:hover {
            transform: translateY(-1px);
        }

        .admin-blog-primary {
            color: #fff;
            background: linear-gradient(135deg, #1e40af, var(--admin-blue) 52%, var(--admin-cyan));
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
        }

        .admin-blog-secondary {
            border: 1px solid rgba(37, 99, 235, .24);
            color: #1d4ed8;
            background: #ffffff;
        }

        .admin-blog-row {
            opacity: 0;
            transform: translateY(8px);
            animation: admin-blog-row-in 360ms cubic-bezier(.16, 1, .3, 1) forwards;
            animation-delay: calc(var(--row-index, 0) * 35ms);
        }

        .admin-blog-row:hover {
            background: #eff6ff;
        }

        .admin-blog-dialog {
            width: min(92vw, 34rem);
            border: 1px solid #e0e0e0;
            border-radius: .5rem;
            color: #212529;
            background: #ffffff;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .14);
            backdrop-filter: blur(18px);
        }

        .admin-blog-dialog::backdrop {
            background: rgba(15, 23, 42, .35);
            backdrop-filter: blur(4px);
        }

        @keyframes admin-blog-row-in {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 1024px) {
            .admin-blog-shell {
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
        $sortLink = fn (string $field) => route('admin.blog-posts.index', array_filter([
            'search' => request('search'),
            'sort' => $field,
            'direction' => $sort === $field ? $nextDirection : 'asc',
        ]));
    @endphp

    <section class="admin-blog-page admin-blog-shell">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase text-cyan-200">Blog Management</p>
                    <h1 class="mt-2 text-3xl font-black text-white">Blog Posts</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Create and manage blog articles, tips, and guides for driver test preparation.</p>
                </div>

                <a href="{{ route('admin.blog-posts.create') }}" class="admin-blog-button admin-blog-primary">
                    Add Post
                </a>
            </div>

            <div class="admin-blog-glass mb-5 p-4">
                <form method="GET" action="{{ route('admin.blog-posts.index') }}" class="grid gap-3 md:grid-cols-[1fr_auto_auto]">
                    <label class="block">
                        <span class="sr-only">Search blog posts</span>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by title or category" class="admin-blog-input">
                    </label>
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="direction" value="{{ $direction }}">
                    <button type="submit" class="admin-blog-button admin-blog-primary">Search</button>
                    <a href="{{ route('admin.blog-posts.index') }}" class="admin-blog-button admin-blog-secondary">Reset</a>
                </form>
            </div>

            <div class="admin-blog-glass overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-sm">
                        <thead class="bg-white/[.04] text-left text-xs font-black uppercase text-slate-400">
                            <tr>
                                <th class="px-4 py-4">
                                    <a href="{{ $sortLink('title') }}" class="transition hover:text-cyan-200">Title</a>
                                </th>
                                <th class="px-4 py-4">
                                    <a href="{{ $sortLink('category') }}" class="transition hover:text-cyan-200">Category</a>
                                </th>
                                <th class="px-4 py-4">
                                    <a href="{{ $sortLink('is_published') }}" class="transition hover:text-cyan-200">Status</a>
                                </th>
                                <th class="px-4 py-4">
                                    <a href="{{ $sortLink('published_at') }}" class="transition hover:text-cyan-200">Published Date</a>
                                </th>
                                <th class="px-4 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($posts as $post)
                                <tr class="admin-blog-row transition" style="--row-index: {{ $loop->index }};">
                                    <td class="px-4 py-4">
                                        <div class="font-black text-white">{{ $post->title }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-md border border-blue-500/20 bg-white/[.06] px-2.5 py-1 font-black text-cyan-100">{{ $post->category }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($post->is_published)
                                            <span class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 font-black text-emerald-300">Published</span>
                                        @else
                                            <span class="rounded-md border border-slate-500/20 bg-slate-500/10 px-2.5 py-1 font-black text-slate-400">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-slate-300">
                                        {{ $post->published_at ? $post->published_at->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="admin-blog-button admin-blog-secondary px-3 py-2">
                                                Edit
                                            </a>
                                            <button type="button" class="admin-blog-button admin-blog-secondary px-3 py-2" data-modal-open="post-delete-{{ $post->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-slate-400">No blog posts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-slate-300">
                {{ $posts->links() }}
            </div>
        </div>

        @foreach($posts as $post)
            <dialog id="post-delete-{{ $post->id }}" class="admin-blog-dialog p-0">
                <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" class="p-5">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black text-white">Delete "{{ $post->title }}"?</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                This action is permanent. The blog post and all associated data will be removed and cannot be recovered.
                            </p>
                        </div>
                        <button type="button" class="text-slate-400 transition hover:text-white" data-modal-close aria-label="Close modal">Close</button>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="admin-blog-button admin-blog-secondary" data-modal-close>Cancel</button>
                        <button type="submit" class="admin-blog-button admin-blog-primary">Delete Post</button>
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
