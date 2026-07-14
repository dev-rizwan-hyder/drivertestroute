@extends('layouts.app')

@section('title', 'Blog')

@push('styles')
    <style>
        .blog-page {
            background-color: #f8f9fa;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .09), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .07), transparent 30%),
                linear-gradient(180deg, rgba(248, 249, 250, .9), rgba(241, 243, 245, .94) 48%, rgba(248, 249, 250, .96)),
                var(--public-image-pages);
            background-position: center, center, center, center top;
            background-repeat: no-repeat;
            background-size: auto, auto, auto, cover;
            color: #212529;
        }

        .blog-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #1e40af 0%, #2563eb 44%, #0891b2 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .blog-filter {
            border: 1px solid rgba(37, 99, 235, .24);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .86);
            padding: .65rem .9rem;
            color: #1d4ed8;
            font-weight: 800;
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), background 200ms ease-out, box-shadow 200ms ease-out;
        }

        .blog-filter:hover,
        .blog-filter.is-active {
            color: #fff;
            background: linear-gradient(135deg, #1e40af, #2563eb 52%, #0891b2);
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            transform: translateY(-1px);
        }

        .blog-card {
            border: 1px solid rgba(203, 213, 225, .9);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            opacity: 1;
            transform: translateY(0);
            transition: opacity 220ms ease-out, transform 240ms cubic-bezier(.16, 1, .3, 1), box-shadow 240ms ease-out, border-color 240ms ease-out;
            backdrop-filter: blur(16px);
        }

        .blog-card:hover {
            border-color: rgba(37, 99, 235, .28);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .12);
            transform: translateY(-4px);
        }

        .blog-card.is-hidden {
            display: none;
        }

        .blog-card.is-entering {
            animation: blog-card-in 280ms cubic-bezier(.16, 1, .3, 1) both;
        }

        .blog-visual {
            position: relative;
            overflow: hidden;
            border-radius: .5rem .5rem 0 0;
            background-color: #f1f3f5;
            background-image:
                linear-gradient(135deg, rgba(30, 64, 175, .14), rgba(37, 99, 235, .08), rgba(8, 145, 178, .06)),
                linear-gradient(90deg, rgba(248, 249, 250, .58), rgba(255, 255, 255, .28), rgba(241, 243, 245, .64)),
                var(--public-image-route);
            background-position: center, center, center;
            background-repeat: no-repeat;
            background-size: auto, auto, cover;
        }

        .blog-visual svg {
            opacity: .74;
        }

        .blog-visual-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .blog-load-button {
            border-radius: .5rem;
            background: linear-gradient(135deg, #1e40af, #2563eb 52%, #0891b2);
            padding: .8rem 1.1rem;
            color: #fff;
            font-weight: 900;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), box-shadow 200ms ease-out;
        }

        .blog-load-button:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .28);
        }

        @keyframes blog-card-in {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    @php
        $categories = $posts->pluck('category')->unique()->values();
    @endphp

    <div class="blog-page">
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="max-w-4xl">
                <p class="text-sm font-black uppercase text-cyan-200">Blog</p>
                <h1 class="mt-4 text-5xl font-black leading-tight text-white sm:text-6xl">
                    Route practice notes for
                    <span class="blog-gradient-text block">sharper test-day prep.</span>
                </h1>
                <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-400">
                    Practical guidance on route planning, map access, and repeatable practice sessions for learner drivers and instructors.
                </p>
            </div>

            @if($posts->count())
                <div class="mt-9 flex flex-wrap gap-3" data-blog-filters>
                    <button type="button" class="blog-filter is-active" data-blog-filter="all">All</button>
                    @foreach($categories as $category)
                        <button type="button" class="blog-filter" data-blog-filter="{{ $category }}">{{ $category }}</button>
                    @endforeach
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-3" data-blog-grid>
                    @foreach($posts as $post)
                        <article class="blog-card overflow-hidden {{ $loop->index >= 6 ? 'is-hidden' : '' }}" data-blog-card data-category="{{ $post->category }}" data-page="{{ intdiv($loop->index, 3) }}">
                            <div class="blog-visual h-44">
                                @if($post->featured_image)
                                    <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" class="blog-visual-img" loading="lazy">
                                @else
                                    <svg class="h-full w-full" viewBox="0 0 420 220" fill="none" aria-hidden="true">
                                        <path d="M0 54H420M0 116H420M0 178H420M62 0V220M154 0V220M246 0V220M338 0V220" stroke="rgba(148,163,184,.14)" />
                                        <path d="M40 176 C112 96 150 146 210 76 C260 18 304 72 380 38" stroke="url(#blogRoute{{ $loop->index }})" stroke-width="8" stroke-linecap="round" />
                                        <circle cx="40" cy="176" r="10" fill="#38bdf8" />
                                        <circle cx="380" cy="38" r="10" fill="#2563eb" />
                                        <defs>
                                            <linearGradient id="blogRoute{{ $loop->index }}" x1="40" x2="380" y1="176" y2="38">
                                                <stop stop-color="#1e3a8a" />
                                                <stop offset=".55" stop-color="#2563eb" />
                                                <stop offset="1" stop-color="#06b6d4" />
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3 text-xs font-black uppercase text-slate-400">
                                    <span class="text-cyan-200">{{ $post->category }}</span>
                                    <span>{{ $post->read_time }}</span>
                                </div>
                                <h2 class="mt-4 text-xl font-black text-white">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="transition-colors duration-200 hover:text-cyan-200">{{ $post->title }}</a>
                                </h2>
                                <p class="mt-3 text-sm leading-6 text-slate-400">{{ $post->excerpt }}</p>
                                <p class="mt-5 text-xs font-bold text-slate-500">{{ $post->published_at->format('M j, Y') }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10 text-center">
                    <button type="button" class="blog-load-button" data-blog-load>Load More</button>
                </div>
            @else
                <div class="mt-16 text-center">
                    <svg class="mx-auto h-16 w-16 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V9a2 2 0 0 0-2-2h-1" />
                        <path d="M9 13h6m-6 4h4" />
                    </svg>
                    <h2 class="mt-4 text-xl font-black text-white">No posts yet</h2>
                    <p class="mt-2 text-sm text-slate-400">Check back soon — new practice tips and route guides are on the way.</p>
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const cards = Array.from(document.querySelectorAll('[data-blog-card]'));
            const filters = Array.from(document.querySelectorAll('[data-blog-filter]'));
            const loadButton = document.querySelector('[data-blog-load]');
            let activeCategory = 'all';
            let visibleCount = 6;

            function renderCards() {
                const matchingCards = cards.filter((card) => activeCategory === 'all' || card.dataset.category === activeCategory);

                cards.forEach((card) => {
                    const shouldShow = matchingCards.includes(card) && matchingCards.indexOf(card) < visibleCount;
                    card.classList.toggle('is-hidden', !shouldShow);

                    if (shouldShow) {
                        card.classList.add('is-entering');
                        setTimeout(() => card.classList.remove('is-entering'), 320);
                    }
                });

                loadButton?.classList.toggle('hidden', matchingCards.length <= visibleCount);
            }

            filters.forEach((filter) => {
                filter.addEventListener('click', () => {
                    activeCategory = filter.dataset.blogFilter;
                    visibleCount = 6;
                    filters.forEach((item) => item.classList.toggle('is-active', item === filter));
                    renderCards();
                });
            });

            loadButton?.addEventListener('click', () => {
                visibleCount += 3;
                renderCards();
            });

            renderCards();
        })();
    </script>
@endpush
