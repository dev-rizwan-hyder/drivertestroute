@extends('layouts.app')

@section('title', 'Blog')

@push('styles')
    <style>
        .blog-page {
            background:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .18), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .12), transparent 30%),
                linear-gradient(180deg, #0a0e1a, #0d1117 48%, #0a0e1a);
            color: #f8fafc;
        }

        .blog-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #fff 0%, #bfdbfe 26%, #38bdf8 56%, #cffafe 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .blog-filter {
            border: 1px solid rgba(59, 130, 246, .34);
            border-radius: .5rem;
            background: rgba(15, 23, 42, .48);
            padding: .65rem .9rem;
            color: #bfdbfe;
            font-weight: 800;
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), background 200ms ease-out, box-shadow 200ms ease-out;
        }

        .blog-filter:hover,
        .blog-filter.is-active {
            color: #fff;
            background: linear-gradient(135deg, #1e3a8a, #2563eb 52%, #06b6d4);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .24), inset 0 1px 0 rgba(255, 255, 255, .14);
            transform: translateY(-1px);
        }

        .blog-card {
            border: 1px solid rgba(59, 130, 246, .22);
            border-radius: .5rem;
            background:
                linear-gradient(180deg, rgba(56, 189, 248, .075), rgba(15, 23, 42, .18)),
                rgba(17, 24, 39, .68);
            box-shadow: 0 22px 58px rgba(2, 6, 23, .34), inset 0 1px 0 rgba(255, 255, 255, .1);
            opacity: 1;
            transform: translateY(0);
            transition: opacity 220ms ease-out, transform 240ms cubic-bezier(.16, 1, .3, 1), box-shadow 240ms ease-out, border-color 240ms ease-out;
            backdrop-filter: blur(16px);
        }

        .blog-card:hover {
            border-color: rgba(56, 189, 248, .38);
            box-shadow: 0 0 20px rgba(59, 130, 246, .34), 0 26px 64px rgba(2, 6, 23, .38);
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
            background:
                linear-gradient(135deg, rgba(30, 58, 138, .42), rgba(37, 99, 235, .22), rgba(6, 182, 212, .16)),
                rgba(10, 14, 26, .88);
        }

        .blog-load-button {
            border-radius: .5rem;
            background: linear-gradient(135deg, #1e3a8a, #2563eb 52%, #06b6d4);
            padding: .8rem 1.1rem;
            color: #fff;
            font-weight: 900;
            box-shadow: 0 16px 34px rgba(37, 99, 235, .24), inset 0 1px 0 rgba(255, 255, 255, .14);
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), box-shadow 200ms ease-out;
        }

        .blog-load-button:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 0 22px rgba(6, 182, 212, .32), 0 20px 42px rgba(37, 99, 235, .24);
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
        $posts = [
            ['category' => 'Planning', 'title' => 'How to compare driving test routes before you practice', 'excerpt' => 'Use city, start area, destination, duration, and route notes to decide which session needs your attention first.', 'date' => 'Jul 8, 2026', 'read' => '4 min read'],
            ['category' => 'Practice', 'title' => 'Making limited map starts count', 'excerpt' => 'Treat each start like a focused rehearsal: warm up, drive the route, then review the decisions that felt uncertain.', 'date' => 'Jul 6, 2026', 'read' => '5 min read'],
            ['category' => 'Access', 'title' => 'Keeping purchased routes organized in your dashboard', 'excerpt' => 'A simple route dashboard helps you track starts, revisit maps, and keep practice history close at hand.', 'date' => 'Jul 4, 2026', 'read' => '3 min read'],
            ['category' => 'Planning', 'title' => 'What to check before choosing a DriveTest city', 'excerpt' => 'Address, route coverage, travel time, and instructor availability all shape a cleaner practice plan.', 'date' => 'Jul 1, 2026', 'read' => '4 min read'],
            ['category' => 'Practice', 'title' => 'Building confidence with repeatable route sessions', 'excerpt' => 'Repeat the route with a goal for each run, then use the next start only when the previous issue is understood.', 'date' => 'Jun 28, 2026', 'read' => '6 min read'],
            ['category' => 'Access', 'title' => 'Why controlled map starts help keep practice intentional', 'excerpt' => 'Limited starts encourage planned sessions instead of casual map opening and help learners stay accountable.', 'date' => 'Jun 24, 2026', 'read' => '3 min read'],
            ['category' => 'Planning', 'title' => 'Using route notes to prepare for unfamiliar intersections', 'excerpt' => 'Route notes can highlight turns, lane decisions, and timing cues before you reach the test area.', 'date' => 'Jun 20, 2026', 'read' => '5 min read'],
            ['category' => 'Practice', 'title' => 'How instructors can structure a mock route run', 'excerpt' => 'Start with route context, drive without interruption, then debrief with specific route markers and next steps.', 'date' => 'Jun 18, 2026', 'read' => '4 min read'],
            ['category' => 'Access', 'title' => 'What to do when a route has no starts remaining', 'excerpt' => 'Review your dashboard, decide whether another run is needed, and purchase more starts only when the practice plan is clear.', 'date' => 'Jun 12, 2026', 'read' => '3 min read'],
        ];
        $categories = collect($posts)->pluck('category')->unique()->values();
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

            <div class="mt-9 flex flex-wrap gap-3" data-blog-filters>
                <button type="button" class="blog-filter is-active" data-blog-filter="all">All</button>
                @foreach($categories as $category)
                    <button type="button" class="blog-filter" data-blog-filter="{{ $category }}">{{ $category }}</button>
                @endforeach
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-3" data-blog-grid>
                @foreach($posts as $post)
                    <article class="blog-card overflow-hidden {{ $loop->index >= 6 ? 'is-hidden' : '' }}" data-blog-card data-category="{{ $post['category'] }}" data-page="{{ intdiv($loop->index, 3) }}">
                        <div class="blog-visual h-44">
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
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-between gap-3 text-xs font-black uppercase text-slate-400">
                                <span class="text-cyan-200">{{ $post['category'] }}</span>
                                <span>{{ $post['read'] }}</span>
                            </div>
                            <h2 class="mt-4 text-xl font-black text-white">{{ $post['title'] }}</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-400">{{ $post['excerpt'] }}</p>
                            <p class="mt-5 text-xs font-bold text-slate-500">{{ $post['date'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <button type="button" class="blog-load-button" data-blog-load>Load More</button>
            </div>
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
