@extends('layouts.admin')

@section('title', 'Blog Posts')

@section('content')
    @php
        $nextDirection = $direction === 'asc' ? 'desc' : 'asc';
        $sortLink = fn (string $field) => route('admin.blog-posts.index', array_filter([
            'search' => request('search'),
            'sort' => $field,
            'direction' => $sort === $field ? $nextDirection : 'asc',
        ]));
    @endphp

    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Blog Management</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900 tracking-tight">Blog Posts</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500">Create, edit, and publish articles, tips, and guides for driver test preparation.</p>
            </div>

            <a href="{{ route('admin.blog-posts.create') }}" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition">
                Add Post
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
            <form method="GET" action="{{ route('admin.blog-posts.index') }}" class="grid gap-3 md:grid-cols-[1fr_auto_auto]">
                <label class="block">
                    <span class="sr-only">Search blog posts</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by title or category" class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 outline-none transition">
                </label>
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">Search</button>
                <a href="{{ route('admin.blog-posts.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Reset</a>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/70 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">
                                <a href="{{ $sortLink('title') }}" class="transition hover:text-blue-700 flex items-center gap-1.5">
                                    Title
                                    @if($sort === 'title')
                                        <span class="text-[10px]">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3">
                                <a href="{{ $sortLink('category') }}" class="transition hover:text-blue-700 flex items-center gap-1.5">
                                    Category
                                    @if($sort === 'category')
                                        <span class="text-[10px]">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3">
                                <a href="{{ $sortLink('is_published') }}" class="transition hover:text-blue-700 flex items-center gap-1.5">
                                    Status
                                    @if($sort === 'is_published')
                                        <span class="text-[10px]">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3">
                                <a href="{{ $sortLink('published_at') }}" class="transition hover:text-blue-700 flex items-center gap-1.5">
                                    Published Date
                                    @if($sort === 'published_at')
                                        <span class="text-[10px]">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($posts as $post)
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 leading-snug">{{ $post->title }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-700/10">{{ $post->category }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($post->is_published)
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/10">Published</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Draft</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-medium">
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.blog-posts.edit', $post) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                                            Edit
                                        </a>
                                        <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition" data-modal-open="post-delete-{{ $post->id }}">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500">No blog posts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>

        @foreach($posts as $post)
            <dialog id="post-delete-{{ $post->id }}" class="rounded-xl border border-slate-200 bg-white shadow-xl max-w-md w-full p-6 outline-none backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm">
                <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">Delete Post</h2>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                                Are you sure you want to delete <span class="font-semibold text-slate-800">"{{ $post->title }}"</span>? This action is permanent and cannot be undone.
                            </p>
                        </div>

                        <div class="flex justify-end gap-2 mt-2">
                            <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition" data-modal-close>Cancel</button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">Delete Post</button>
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
