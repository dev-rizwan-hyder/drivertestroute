<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Driver Test Route') }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

        <script>
            window.tailwind = window.tailwind || {};
            window.tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            stone: {
                                50: '#f8f9fa',
                                100: '#f1f3f5',
                                200: '#e2e8f0',
                                300: '#cbd5e1',
                                400: '#94a3b8',
                                500: '#64748b',
                                600: '#475569',
                                700: '#334155',
                                800: '#1e293b',
                                900: '#0f172a',
                                950: '#090d16',
                            },
                            emerald: {
                                50: '#eff6ff',
                                100: '#dbeafe',
                                200: '#bfdbfe',
                                300: '#93c5fd',
                                400: '#60a5fa',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#1e40af',
                                900: '#1e3a8a',
                                950: '#172554',
                            }
                        },
                        fontFamily: {
                            sans: ['Inter', 'Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        },
                    },
                },
            };
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
        @stack('styles')
        <style>
            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is(.text-white) {
                color: #0f172a;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is(.text-slate-300, .text-slate-400, .text-slate-500, .text-stone-400) {
                color: #64748b;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is(.text-cyan-100, .text-cyan-200) {
                color: #0891b2;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is(.text-emerald-300) {
                color: #2563eb;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) [class~="hover:text-white"]:hover,
            .admin-light-theme :where(.admin-blog-page, .admin-city-page) [class~="hover:text-cyan-200"]:hover {
                color: #2563eb;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) [class~="border-white/10"],
            .admin-light-theme :where(.admin-blog-page, .admin-city-page) [class~="divide-white/10"] > :not([hidden]) ~ :not([hidden]) {
                border-color: #cbd5e1;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is([class~="bg-white/[.04]"], [class~="bg-white/[.06]"]) {
                background-color: #f1f3f5;
            }

            .admin-light-theme :where(.admin-blog-primary, .admin-city-primary, [class~="bg-emerald-700"]),
            .admin-light-theme :where(.admin-blog-primary, .admin-city-primary, [class~="bg-emerald-700"]) * {
                color: #ffffff !important;
            }
        </style>
    </head>
    <body class="admin-light-theme min-h-screen bg-slate-50 text-slate-900 antialiased">
        @php
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => ['admin.dashboard'], 'hint' => 'Overview'],
                ['label' => 'Cities', 'route' => 'admin.cities.index', 'active' => ['admin.cities.*'], 'hint' => 'Locations'],
                ['label' => 'Routes', 'route' => 'admin.driving-routes.index', 'active' => ['admin.driving-routes.index', 'admin.driving-routes.edit', 'admin.driving-routes.show'], 'hint' => 'Manage maps'],
                ['label' => 'Add Route', 'route' => 'admin.driving-routes.create', 'active' => ['admin.driving-routes.create'], 'hint' => 'New map'],
                ['label' => 'Blog Posts', 'route' => 'admin.blog-posts.index', 'active' => ['admin.blog-posts.index', 'admin.blog-posts.edit', 'admin.blog-posts.show'], 'hint' => 'Manage posts'],
                ['label' => 'Add Post', 'route' => 'admin.blog-posts.create', 'active' => ['admin.blog-posts.create'], 'hint' => 'New post'],
                ['label' => 'Purchases', 'route' => 'admin.purchases.index', 'active' => ['admin.purchases.*'], 'hint' => 'Checkout records'],
                ['label' => 'Users', 'route' => 'admin.users.index', 'active' => ['admin.users.*'], 'hint' => 'Customers'],
            ];
        @endphp

        <div id="admin-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/10 lg:hidden"></div>

        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white/95 text-slate-900 shadow-sm transition-transform duration-200 lg:translate-x-0 backdrop-blur-md">
            <div class="border-b border-slate-100 px-6 py-5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-lg text-xs font-black text-white bg-gradient-to-br from-blue-850 via-blue-600 to-cyan-500 shadow-md">DTR</span>
                    <span class="leading-tight">
                        <span class="block text-base font-black bg-gradient-to-r from-blue-800 via-blue-600 to-cyan-500 bg-clip-text text-transparent">Admin Panel</span>
                        <span class="block text-xs font-medium text-slate-500">Driver Test Routes</span>
                    </span>
                </a>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3.5 py-5">
                @foreach($navItems as $item)
                    @php($isActive = collect($item['active'])->contains(fn ($pattern) => request()->routeIs($pattern)))
                    <a href="{{ route($item['route']) }}" class="group flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $isActive ? 'bg-gradient-to-r from-blue-700 to-cyan-600 text-white shadow-md shadow-blue-500/10' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span>{{ $item['label'] }}</span>
                        <span class="text-xs font-medium {{ $isActive ? 'text-blue-50/90' : 'text-slate-400 group-hover:text-slate-500' }}">{{ $item['hint'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-slate-100 p-4">
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                    Public Site
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-left text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        Logout
                    </button>
                </form>
                <div class="mt-4 rounded-lg bg-slate-50/50 p-3 text-xs text-slate-500 ring-1 ring-slate-100">
                    Signed in as
                    <span class="mt-1 block truncate font-semibold text-slate-900">{{ auth()->user()->email }}</span>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-slate-100 bg-white/80 backdrop-blur-md">
                <div class="flex min-h-16 items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button id="admin-sidebar-toggle" type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 lg:hidden">
                            Menu
                        </button>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-normal text-slate-400">Admin</p>
                            <h1 class="truncate text-lg font-bold text-slate-900">@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('driving-routes.index') }}" class="hidden rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 sm:inline-flex">
                            Browse Site
                        </a>
                        <a href="{{ route('admin.driving-routes.create') }}" class="rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 hover:from-blue-800 hover:to-cyan-700 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-blue-500/10 transition">
                            Add Route
                        </a>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                @if(session('success') || session('error') || $errors->any())
                    <div class="mb-6 space-y-3">
                        @if(session('success'))
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
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
        </div>

        <script>
            const adminSidebar = document.getElementById('admin-sidebar');
            const adminSidebarBackdrop = document.getElementById('admin-sidebar-backdrop');
            const adminSidebarToggle = document.getElementById('admin-sidebar-toggle');

            function setAdminSidebar(open) {
                adminSidebar.classList.toggle('-translate-x-full', !open);
                adminSidebarBackdrop.classList.toggle('hidden', !open);
            }

            adminSidebarToggle?.addEventListener('click', () => setAdminSidebar(true));
            adminSidebarBackdrop?.addEventListener('click', () => setAdminSidebar(false));
        </script>
        @stack('scripts')
    </body>
</html>
