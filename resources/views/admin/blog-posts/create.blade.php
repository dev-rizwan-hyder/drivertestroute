@extends('layouts.admin')

@section('title', 'Add Blog Post')

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

        .admin-blog-input.is-invalid {
            border-color: rgba(239, 68, 68, .6);
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

        .admin-blog-select {
            width: 100%;
            border: 1px solid #cfd8e3;
            border-radius: .5rem;
            background: #ffffff;
            padding: .72rem .85rem;
            color: #212529;
            transition: border-color 200ms ease-out, box-shadow 200ms ease-out;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M4.646 5.646a.5.5 0 0 1 .708 0L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            padding-right: 2.5rem;
        }

        .admin-blog-select:focus {
            border-color: rgba(37, 99, 235, .52);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: 0;
        }

        .admin-blog-select option {
            background: #ffffff;
            color: #212529;
        }

        .admin-blog-checkbox {
            width: 1.25rem;
            height: 1.25rem;
            border: 1px solid rgba(37, 99, 235, .28);
            border-radius: .25rem;
            background: #ffffff;
            accent-color: var(--admin-cyan);
            cursor: pointer;
        }

        .admin-blog-error {
            color: #b91c1c;
            font-size: .8rem;
            margin-top: .35rem;
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
    <section class="admin-blog-page admin-blog-shell">
        <div class="mx-auto max-w-4xl">
            <div class="mb-8">
                <p class="text-sm font-black uppercase text-cyan-200">New Post</p>
                <h1 class="mt-2 text-3xl font-black text-white">Add Blog Post</h1>
            </div>

            <div class="admin-blog-glass p-6 md:p-8">
                <form method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Title --}}
                        <label class="block md:col-span-2">
                            <span class="text-sm font-bold text-slate-300">Title</span>
                            <input type="text" name="title" value="{{ old('title') }}" required class="admin-blog-input mt-1 @error('title') is-invalid @enderror" placeholder="Enter post title">
                            @error('title')
                                <p class="admin-blog-error">{{ $message }}</p>
                            @enderror
                        </label>

                        {{-- Category --}}
                        <label class="block">
                            <span class="text-sm font-bold text-slate-300">Category</span>
                            <select name="category" class="admin-blog-select mt-1 @error('category') is-invalid @enderror">
                                @foreach(['Planning', 'Practice', 'Access', 'Tips', 'News'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="admin-blog-error">{{ $message }}</p>
                            @enderror
                        </label>

                        {{-- Read Time --}}
                        <label class="block">
                            <span class="text-sm font-bold text-slate-300">Read Time</span>
                            <input type="text" name="read_time" value="{{ old('read_time', '3 min read') }}" class="admin-blog-input mt-1 @error('read_time') is-invalid @enderror" placeholder="e.g. 5 min read">
                            @error('read_time')
                                <p class="admin-blog-error">{{ $message }}</p>
                            @enderror
                        </label>

                        {{-- Excerpt --}}
                        <label class="block md:col-span-2">
                            <span class="text-sm font-bold text-slate-300">Excerpt</span>
                            <textarea name="excerpt" required maxlength="500" rows="3" class="admin-blog-input mt-1 @error('excerpt') is-invalid @enderror" placeholder="A short summary of the post (max 500 characters)">{{ old('excerpt') }}</textarea>
                            @error('excerpt')
                                <p class="admin-blog-error">{{ $message }}</p>
                            @enderror
                        </label>

                        {{-- Body --}}
                        <label class="block md:col-span-2">
                            <span class="text-sm font-bold text-slate-300">Body</span>
                            <textarea name="body" required rows="12" class="admin-blog-input mt-1 @error('body') is-invalid @enderror" placeholder="Full post content (HTML supported)">{{ old('body') }}</textarea>
                            @error('body')
                                <p class="admin-blog-error">{{ $message }}</p>
                            @enderror
                        </label>

                        {{-- Featured Image --}}
                        <label class="block md:col-span-2">
                            <span class="text-sm font-bold text-slate-300">Featured Image</span>
                            <input type="file" name="featured_image" accept="image/*" class="admin-blog-input mt-1 @error('featured_image') is-invalid @enderror file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3.5 file:py-1.5 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 transition">
                            @error('featured_image')
                                <p class="admin-blog-error">{{ $message }}</p>
                            @enderror
                        </label>

                        {{-- Published toggle --}}
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="admin-blog-checkbox">
                            <label for="is_published" class="text-sm font-bold text-slate-300 cursor-pointer">Publish this post</label>
                        </div>

                        {{-- Published Date --}}
                        <label class="block">
                            <span class="text-sm font-bold text-slate-300">Published Date</span>
                            <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="admin-blog-input mt-1 @error('published_at') is-invalid @enderror">
                            @error('published_at')
                                <p class="admin-blog-error">{{ $message }}</p>
                            @enderror
                        </label>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('admin.blog-posts.index') }}" class="admin-blog-button admin-blog-secondary">Cancel</a>
                        <button type="submit" class="admin-blog-button admin-blog-primary">Save Post</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
