<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Driver Test Route'))</title>

        <script>
            window.tailwind = window.tailwind || {};
            window.tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        },
                    },
                },
            };
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @keyframes hero-panel-rise {
                0% { opacity: 0; transform: translateY(18px) scale(.98); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }

            @keyframes hero-float {
                0%, 100% { transform: translate3d(0, 0, 0); }
                50% { transform: translate3d(0, -12px, 0); }
            }

            @keyframes hero-meter {
                0%, 100% { stroke-dashoffset: 138; }
                45%, 70% { stroke-dashoffset: 38; }
            }

            @keyframes hero-scan {
                0% { transform: translateX(-100%); opacity: 0; }
                12%, 82% { opacity: .28; }
                100% { transform: translateX(100%); opacity: 0; }
            }

            @keyframes hero-progress {
                0%, 100% { transform: scaleX(.38); }
                50% { transform: scaleX(.92); }
            }

            @keyframes hero-status-pulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, .38); }
                50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            }

            @keyframes section-rise {
                0% { opacity: 0; transform: translateY(18px); }
                100% { opacity: 1; transform: translateY(0); }
            }

            @keyframes soft-sheen {
                0% { transform: translateX(-120%) skewX(-16deg); }
                60%, 100% { transform: translateX(220%) skewX(-16deg); }
            }

            .hero-panel-rise {
                animation: hero-panel-rise .7s ease-out both;
            }

            .hero-float {
                animation: hero-float 5.2s ease-in-out infinite;
            }

            .hero-meter-ring {
                stroke-dasharray: 188;
                stroke-dashoffset: 138;
                animation: hero-meter 4.8s ease-in-out infinite;
            }

            .hero-progress-fill {
                transform: scaleX(.4);
                transform-origin: left center;
                animation: hero-progress 4.6s ease-in-out infinite;
            }

            .hero-scan-line {
                animation: hero-scan 5.5s ease-in-out infinite;
            }

            .hero-status-dot {
                animation: hero-status-pulse 2.4s ease-in-out infinite;
            }

            .section-rise {
                animation: section-rise .75s ease-out both;
            }

            .soft-sheen::after {
                content: "";
                position: absolute;
                inset: 0;
                width: 40%;
                pointer-events: none;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,.45), transparent);
                animation: soft-sheen 5.8s ease-in-out infinite;
            }

            .public-header-transparent {
                background: transparent;
                border-color: transparent;
                box-shadow: none;
            }

            .public-header-transparent .public-logo {
                background: rgba(255,255,255,.14);
                color: #fff;
                box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
            }

            .public-header-transparent .public-brand,
            .public-header-transparent .public-nav-link,
            .public-header-transparent .public-menu-button {
                color: #fff;
            }

            .public-header-transparent .public-brand-subtitle {
                color: rgba(244,244,245,.72);
            }

            .public-header-transparent .public-nav-link:hover,
            .public-header-transparent .public-nav-active {
                background: rgba(255,255,255,.14);
                color: #fff;
            }

            .public-header-transparent .public-menu-button {
                border-color: rgba(255,255,255,.24);
                background: rgba(255,255,255,.08);
            }

            @media (prefers-reduced-motion: reduce) {
                .hero-panel-rise,
                .hero-float,
                .hero-meter-ring,
                .hero-progress-fill,
                .hero-scan-line,
                .hero-status-dot,
                .section-rise,
                .soft-sheen::after {
                    animation: none;
                }
            }
        </style>
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-950 antialiased">
        @php
            $transparentHeader = request()->routeIs('home');
            $publicNavItems = [
                ['label' => 'Home', 'route' => 'home', 'active' => ['home']],
                ['label' => 'Routes', 'route' => 'driving-routes.index', 'active' => ['routes.index', 'driving-routes.index']],
                ['label' => 'About', 'route' => 'about', 'active' => ['about']],
                ['label' => 'Contact', 'route' => 'contact', 'active' => ['contact']],
            ];
        @endphp

        <header
            id="public-header"
            data-transparent-header="{{ $transparentHeader ? 'true' : 'false' }}"
            class="{{ $transparentHeader ? 'public-header-transparent fixed inset-x-0 border-transparent' : 'sticky border-zinc-200 bg-white/95' }} top-0 z-30 border-b backdrop-blur transition-all duration-300"
        >
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="public-brand flex items-center gap-3 text-zinc-950 transition-colors">
                    <span class="public-logo grid h-10 w-10 place-items-center rounded-md bg-zinc-950 text-sm font-black text-white transition">DTR</span>
                    <span class="leading-tight">
                        <span class="block text-base font-bold tracking-normal">Driver Test Routes</span>
                        <span class="public-brand-subtitle block text-xs font-medium text-zinc-500 transition-colors">Practice maps for test day</span>
                    </span>
                </a>

                <div class="hidden items-center gap-1 text-sm lg:flex">
                    @foreach($publicNavItems as $item)
                        @php($isActive = collect($item['active'])->contains(fn ($pattern) => request()->routeIs($pattern)))
                        <a href="{{ route($item['route']) }}" class="public-nav-link {{ $isActive ? 'public-nav-active bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950' }} rounded-md px-3 py-2 font-semibold transition">
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    @auth
                        <a href="{{ route('driving-routes.my') }}" class="public-nav-link rounded-md px-3 py-2 font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950">
                            My Routes
                        </a>

                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="public-nav-link rounded-md px-3 py-2 font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950">
                                Dashboard
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="public-nav-link rounded-md border border-zinc-300 px-3 py-2 font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="public-nav-link rounded-md px-3 py-2 font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="rounded-md bg-emerald-700 px-3 py-2 font-semibold text-white shadow-sm transition hover:bg-emerald-800">
                            Create Account
                        </a>
                    @endauth
                </div>

                <button id="public-menu-toggle" type="button" class="public-menu-button grid h-10 w-10 place-items-center rounded-md border border-zinc-300 text-zinc-800 transition hover:bg-zinc-100 lg:hidden" aria-controls="public-mobile-menu" aria-expanded="false" aria-label="Open navigation">
                    <span class="space-y-1.5">
                        <span class="block h-0.5 w-5 rounded-full bg-current"></span>
                        <span class="block h-0.5 w-5 rounded-full bg-current"></span>
                        <span class="block h-0.5 w-5 rounded-full bg-current"></span>
                    </span>
                </button>
            </nav>

            <div id="public-mobile-menu" class="hidden border-t border-zinc-200 bg-white lg:hidden">
                <div class="mx-auto max-w-7xl space-y-2 px-4 py-4 sm:px-6">
                    @foreach($publicNavItems as $item)
                        @php($isActive = collect($item['active'])->contains(fn ($pattern) => request()->routeIs($pattern)))
                        <a href="{{ route($item['route']) }}" class="block rounded-md px-3 py-2 text-sm font-semibold transition {{ $isActive ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <div class="border-t border-zinc-200 pt-3">
                        @auth
                            <a href="{{ route('driving-routes.my') }}" class="block rounded-md px-3 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950">
                                My Routes
                            </a>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950">
                                    Dashboard
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-left text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950">
                                    Logout
                                </button>
                            </form>
                        @else
                            <div class="grid gap-2 sm:grid-cols-2">
                                <a href="{{ route('login') }}" class="rounded-md border border-zinc-300 px-3 py-2 text-center text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-950">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="rounded-md bg-emerald-700 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800">
                                    Create Account
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main>
            @if(session('success') || session('error') || $errors->any())
                <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <p class="font-semibold">Please fix the highlighted fields.</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-zinc-200 bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1.5fr_1fr_1fr] lg:px-8">
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-3 text-zinc-950">
                        <span class="grid h-10 w-10 place-items-center rounded-md bg-zinc-950 text-sm font-black text-white">DTR</span>
                        <span class="font-bold">Driver Test Routes</span>
                    </a>
                    <p class="mt-4 max-w-md text-sm leading-6 text-zinc-600">
                        Paid driving test route maps built for focused practice, clear route planning, and confident test-day preparation.
                    </p>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-zinc-950">Pages</h2>
                    <div class="mt-3 space-y-2 text-sm">
                        <a href="{{ route('driving-routes.index') }}" class="block text-zinc-600 hover:text-emerald-700">Routes</a>
                        <a href="{{ route('about') }}" class="block text-zinc-600 hover:text-emerald-700">About</a>
                        <a href="{{ route('contact') }}" class="block text-zinc-600 hover:text-emerald-700">Contact</a>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-zinc-950">Account</h2>
                    <div class="mt-3 space-y-2 text-sm">
                        @auth
                            <a href="{{ route('driving-routes.my') }}" class="block text-zinc-600 hover:text-emerald-700">My Routes</a>
                        @else
                            <a href="{{ route('login') }}" class="block text-zinc-600 hover:text-emerald-700">Login</a>
                            <a href="{{ route('register') }}" class="block text-zinc-600 hover:text-emerald-700">Create Account</a>
                        @endauth
                    </div>
                </div>
            </div>
        </footer>
        <script>
            const publicHeader = document.getElementById('public-header');
            const publicMenuToggle = document.getElementById('public-menu-toggle');
            const publicMobileMenu = document.getElementById('public-mobile-menu');

            function syncPublicHeader() {
                if (!publicHeader || publicHeader.dataset.transparentHeader !== 'true') {
                    return;
                }

                const menuOpen = publicMobileMenu && !publicMobileMenu.classList.contains('hidden');
                const transparent = window.scrollY < 12 && !menuOpen;

                publicHeader.classList.toggle('public-header-transparent', transparent);
                publicHeader.classList.toggle('bg-white/95', !transparent);
                publicHeader.classList.toggle('border-zinc-200', !transparent);
                publicHeader.classList.toggle('shadow-sm', !transparent);
            }

            publicMenuToggle?.addEventListener('click', () => {
                const isOpen = !publicMobileMenu.classList.contains('hidden');
                publicMobileMenu.classList.toggle('hidden', isOpen);
                publicMenuToggle.setAttribute('aria-expanded', String(!isOpen));
                syncPublicHeader();
            });

            window.addEventListener('scroll', syncPublicHeader, { passive: true });
            syncPublicHeader();
        </script>
    </body>
</html>
