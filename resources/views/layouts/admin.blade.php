<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Driver Test Route') }}</title>

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
        @stack('styles')
        <style>
            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is(.text-white) {
                color: #212529;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is(.text-slate-300, .text-slate-400, .text-slate-500, .text-stone-400) {
                color: #5c6675;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is(.text-cyan-100, .text-cyan-200) {
                color: #0e7490;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) :is(.text-emerald-300) {
                color: #047857;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) [class~="hover:text-white"]:hover,
            .admin-light-theme :where(.admin-blog-page, .admin-city-page) [class~="hover:text-cyan-200"]:hover {
                color: #0e7490;
            }

            .admin-light-theme :where(.admin-blog-page, .admin-city-page) [class~="border-white/10"],
            .admin-light-theme :where(.admin-blog-page, .admin-city-page) [class~="divide-white/10"] > :not([hidden]) ~ :not([hidden]) {
                border-color: #e0e0e0;
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
    <body class="admin-light-theme min-h-screen bg-stone-100 text-stone-950 antialiased">
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

        <div id="admin-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-stone-700/20 lg:hidden"></div>

        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-stone-200 bg-white text-stone-950 shadow-sm transition-transform duration-200 lg:translate-x-0">
            <div class="border-b border-stone-200 px-5 py-5">
                <a href="{{ route('admin.dashboard') }}" class="block text-xl font-bold tracking-normal text-stone-950">Admin Panel</a>
                <p class="mt-1 text-sm text-stone-600">Driver Test Routes</p>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                @foreach($navItems as $item)
                    @php($isActive = collect($item['active'])->contains(fn ($pattern) => request()->routeIs($pattern)))
                    <a href="{{ route($item['route']) }}" class="group flex items-center justify-between gap-3 rounded-md px-3 py-3 text-sm font-semibold transition {{ $isActive ? 'bg-emerald-700 text-white shadow-sm' : 'text-stone-700 hover:bg-stone-100 hover:text-stone-950' }}">
                        <span>{{ $item['label'] }}</span>
                        <span class="text-xs font-medium {{ $isActive ? 'text-emerald-50' : 'text-stone-500 group-hover:text-stone-700' }}">{{ $item['hint'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-stone-200 p-4">
                <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100 hover:text-stone-950">
                    Public Site
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full rounded-md border border-stone-300 px-3 py-2 text-left text-sm font-semibold text-stone-700 hover:bg-stone-100 hover:text-stone-950">
                        Logout
                    </button>
                </form>
                <div class="mt-4 rounded-md bg-stone-50 p-3 text-xs text-stone-600 ring-1 ring-stone-200">
                    Signed in as
                    <span class="mt-1 block truncate font-semibold text-stone-950">{{ auth()->user()->email }}</span>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-stone-200 bg-white/95 backdrop-blur">
                <div class="flex min-h-16 items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button id="admin-sidebar-toggle" type="button" class="rounded-md border border-stone-300 px-3 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100 lg:hidden">
                            Menu
                        </button>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-normal text-stone-500">Admin</p>
                            <h1 class="truncate text-lg font-bold text-stone-950">@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('driving-routes.index') }}" class="hidden rounded-md border border-stone-300 px-3 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100 sm:inline-flex">
                            Browse Site
                        </a>
                        <a href="{{ route('admin.driving-routes.create') }}" class="rounded-md bg-emerald-700 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                            Add Route
                        </a>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                @if(session('success') || session('error') || $errors->any())
                    <div class="mb-6 space-y-3">
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
