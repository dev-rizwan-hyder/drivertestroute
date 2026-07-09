@extends('layouts.app')

@section('title', $post->title)

@push('styles')
    <style>
        .blog-show-page {
            background-color: #f8f9fa;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .09), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .07), transparent 30%),
                linear-gradient(180deg, rgba(248, 249, 250, .9), rgba(241, 243, 245, .94) 48%, rgba(248, 249, 250, .96)),
                var(--public-image-section);
            background-position: center, center, center, center top;
            background-repeat: no-repeat;
            background-size: auto, auto, auto, cover;
            color: #212529;
        }

        .blog-show-hero {
            position: relative;
            max-height: 400px;
            overflow: hidden;
            border-radius: 0 0 .5rem .5rem;
        }

        .blog-show-hero img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }

        .blog-show-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(248, 249, 250, .08) 0%, rgba(248, 249, 250, .78) 100%);
        }

        .blog-show-hero-placeholder {
            position: relative;
            max-height: 400px;
            overflow: hidden;
            border-radius: 0 0 .5rem .5rem;
            background-color: #f1f3f5;
            background-image:
                linear-gradient(135deg, rgba(30, 64, 175, .14), rgba(37, 99, 235, .08), rgba(8, 145, 178, .06)),
                linear-gradient(90deg, rgba(248, 249, 250, .58), rgba(255, 255, 255, .28), rgba(241, 243, 245, .64)),
                var(--public-image-route);
            background-position: center, center, center;
            background-repeat: no-repeat;
            background-size: auto, auto, cover;
        }

        .blog-show-hero-placeholder svg {
            opacity: .74;
        }

        .blog-show-divider {
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, .7), rgba(6, 182, 212, .76), transparent);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .blog-show-body p {
            color: #374151;
            line-height: 2;
            margin-bottom: 1.5rem;
        }

        .blog-show-body h2 {
            color: #212529;
            font-weight: 900;
            font-size: 1.5rem;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
        }

        .blog-show-body h3 {
            color: #212529;
            font-weight: 900;
            font-size: 1.25rem;
            margin-top: 2rem;
            margin-bottom: .75rem;
        }

        .blog-show-body h4 {
            color: #334155;
            font-weight: 800;
            font-size: 1.125rem;
            margin-top: 1.5rem;
            margin-bottom: .5rem;
        }

        .blog-show-body a {
            color: #1d4ed8;
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: color 200ms ease-out;
        }

        .blog-show-body a:hover {
            color: #1e40af;
        }

        .blog-show-body ul,
        .blog-show-body ol {
            color: #374151;
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .blog-show-body ul {
            list-style-type: disc;
        }

        .blog-show-body ol {
            list-style-type: decimal;
        }

        .blog-show-body li {
            margin-bottom: .5rem;
            line-height: 1.85;
        }

        .blog-show-body blockquote {
            border-left: 3px solid rgba(6, 182, 212, .56);
            padding-left: 1.25rem;
            margin: 1.5rem 0;
            color: #5c6675;
            font-style: italic;
        }

        .blog-show-body pre {
            background: #f1f3f5;
            border: 1px solid #e0e0e0;
            border-radius: .5rem;
            padding: 1rem 1.25rem;
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }

        .blog-show-body code {
            color: #1d4ed8;
            font-size: .875em;
        }

        .blog-show-body pre code {
            color: #212529;
        }

        .blog-show-body img {
            border-radius: .5rem;
            max-width: 100%;
            height: auto;
            margin: 1.5rem 0;
        }

        .blog-show-back {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            border: 1px solid rgba(37, 99, 235, .24);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .86);
            padding: .65rem .9rem;
            color: #1d4ed8;
            font-weight: 800;
            text-decoration: none;
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), background 200ms ease-out, box-shadow 200ms ease-out, color 200ms ease-out;
        }

        .blog-show-back:hover {
            color: #fff;
            background: linear-gradient(135deg, #1e40af, #2563eb 52%, #0891b2);
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            transform: translateY(-1px);
        }

        @keyframes blog-show-fade-in {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .blog-show-animate {
            animation: blog-show-fade-in 420ms cubic-bezier(.16, 1, .3, 1) both;
        }
    </style>
@endpush

@section('content')
    <div class="blog-show-page">
        {{-- Hero --}}
        @if($post->featured_image)
            <div class="blog-show-hero">
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                <div class="blog-show-hero-overlay"></div>
            </div>
        @else
            <div class="blog-show-hero-placeholder">
                <svg class="w-full" viewBox="0 0 1200 400" fill="none" aria-hidden="true">
                    <path d="M0 100H1200M0 200H1200M0 300H1200M150 0V400M350 0V400M550 0V400M750 0V400M950 0V400" stroke="rgba(148,163,184,.1)" />
                    <path d="M80 340 C240 180 340 280 480 140 C600 30 720 130 880 70 C960 44 1040 80 1120 60" stroke="url(#blogShowRoute)" stroke-width="10" stroke-linecap="round" />
                    <circle cx="80" cy="340" r="14" fill="#38bdf8" />
                    <circle cx="1120" cy="60" r="14" fill="#2563eb" />
                    <defs>
                        <linearGradient id="blogShowRoute" x1="80" x2="1120" y1="340" y2="60">
                            <stop stop-color="#1e3a8a" />
                            <stop offset=".55" stop-color="#2563eb" />
                            <stop offset="1" stop-color="#06b6d4" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        @endif

        {{-- Content --}}
        <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="blog-show-animate">
                {{-- Category badge --}}
                <p class="text-sm font-black uppercase tracking-wider text-cyan-200">{{ $post->category }}</p>

                {{-- Title --}}
                <h1 class="mt-4 text-4xl font-black leading-tight text-white sm:text-5xl">{{ $post->title }}</h1>

                {{-- Meta line --}}
                <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-slate-400">
                    <span>{{ $post->published_at->format('M j, Y') }}</span>
                    <span class="h-1 w-1 rounded-full bg-slate-600"></span>
                    <span>{{ $post->read_time }}</span>
                </div>

                {{-- Gradient divider --}}
                <div class="blog-show-divider mt-8"></div>

                {{-- Post body --}}
                <div class="blog-show-body mt-10">
                    {!! $post->body !!}
                </div>

                {{-- Gradient divider --}}
                <div class="blog-show-divider mt-10"></div>

                {{-- Back link --}}
                <div class="mt-10">
                    <a href="{{ route('blog') }}" class="blog-show-back">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>
                        Back to Blog
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
