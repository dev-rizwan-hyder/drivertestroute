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
                        sans: ['Inter', 'Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --public-bg: #f8f9fa;
            --public-bg-soft: #f1f3f5;
            --public-panel: rgba(255, 255, 255, .86);
            --public-border: rgba(203, 213, 225, .9);
            --public-text: #212529;
            --public-muted: #5c6675;
            --public-blue-deep: #1e40af;
            --public-blue: #2563eb;
            --public-cyan: #0891b2;
            --public-sky: #0284c7;
            --public-image-hero: url("{{ asset('images/home-hero.jpeg') }}");
            --public-image-section: url("{{ asset('images/section.png.png') }}");
            --public-image-route: url("{{ asset('images/route.png') }}");
            --public-image-about: url("{{ asset('images/about.png') }}");
            --public-image-pages: url("{{ asset('images/pages.png') }}");
        }

        .public-main-offset {
            background-color: var(--public-bg);
            background-image:
                linear-gradient(180deg, rgba(248, 249, 250, .92), rgba(241, 243, 245, .94)),
                var(--public-image-section);
            background-position: center top, center top;
            background-repeat: no-repeat;
            background-size: auto, cover;
            padding-top: 5rem;
        }

        .public-header-glass {
            position: fixed;
            inset: 0 0 auto;
            z-index: 50;
            border-bottom: 1px solid rgba(224, 224, 224, .72);
            background: rgba(255, 255, 255, .84);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            transition:
                background 220ms cubic-bezier(.16, 1, .3, 1),
                border-color 220ms ease-out,
                box-shadow 220ms cubic-bezier(.16, 1, .3, 1);
        }

        .public-header-glass[data-transparent-header="true"]:not(.public-header-scrolled) {
            background: rgba(255, 255, 255, .66);
            box-shadow: none;
        }

        .public-header-glass::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 1px;
            pointer-events: none;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, .34), rgba(8, 145, 178, .38), transparent);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
            opacity: .55;
            transition: opacity 220ms ease-out, box-shadow 220ms ease-out;
        }

        .public-header-scrolled {
            border-color: rgba(224, 224, 224, .95);
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 10px 28px rgba(15, 23, 42, .1);
        }

        .public-header-scrolled::after {
            opacity: 1;
            box-shadow: 0 2px 10px rgba(37, 99, 235, .12);
        }

        .public-header-nav {
            position: relative;
            z-index: 70;
        }

        .public-brand {
            color: var(--public-text);
            text-decoration: none;
        }

        .public-logo {
            background: linear-gradient(135deg, var(--public-blue-deep), var(--public-blue) 52%, var(--public-cyan));
            box-shadow: 0 8px 18px rgba(37, 99, 235, .22);
            transition: transform 220ms cubic-bezier(.16, 1, .3, 1), box-shadow 220ms ease-out;
        }

        .public-wordmark {
            display: block;
            color: transparent;
            background: linear-gradient(100deg, var(--public-blue-deep), var(--public-blue) 48%, var(--public-cyan));
            -webkit-background-clip: text;
            background-clip: text;
            transition: filter 200ms ease-out;
        }

        .public-brand-subtitle {
            color: var(--public-muted);
        }

        .public-brand:hover .public-logo {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(37, 99, 235, .28);
        }

        .public-brand:hover .public-wordmark {
            filter: drop-shadow(0 4px 10px rgba(37, 99, 235, .12));
        }

        .public-nav-list {
            position: relative;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .public-nav-link {
            position: relative;
            display: inline-flex;
            height: 2.7rem;
            align-items: center;
            color: var(--public-muted);
            font-size: .875rem;
            font-weight: 800;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
            transition: color 200ms ease-out;
        }

        .public-nav-link:hover,
        .public-nav-link:focus-visible,
        .public-nav-active {
            color: #1a1a1a;
        }

        .public-nav-link:focus-visible,
        .public-auth-link:focus-visible,
        .public-auth-cta:focus-visible,
        .public-avatar:focus-visible,
        .public-menu-button:focus-visible,
        .public-dropdown-link:focus-visible,
        .public-mobile-link:focus-visible {
            outline: 2px solid rgba(103, 232, 249, .8);
            outline-offset: 3px;
        }

        .public-nav-indicator {
            position: absolute;
            bottom: .35rem;
            left: 0;
            width: 0;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--public-blue), var(--public-cyan), var(--public-sky));
            box-shadow: 0 3px 10px rgba(37, 99, 235, .16);
            opacity: 0;
            transform: translate3d(0, 0, 0);
            transition:
                transform 220ms cubic-bezier(.16, 1, .3, 1),
                width 220ms cubic-bezier(.16, 1, .3, 1),
                opacity 180ms ease-out;
        }

        .public-nav-indicator.is-visible {
            opacity: 1;
        }

        .public-auth-link,
        .public-auth-cta {
            display: inline-flex;
            min-height: 2.55rem;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            padding: 0 .95rem;
            font-size: .875rem;
            font-weight: 800;
            line-height: 1;
            text-decoration: none;
            transition:
                transform 200ms cubic-bezier(.16, 1, .3, 1),
                border-color 200ms ease-out,
                background 200ms ease-out,
                box-shadow 200ms ease-out,
                color 200ms ease-out;
        }

        .public-auth-link {
            border: 1px solid rgba(37, 99, 235, .24);
            color: #1d4ed8;
            background: rgba(255, 255, 255, .74);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        }

        .public-auth-link:hover {
            border-color: rgba(37, 99, 235, .38);
            color: #1e40af;
            background: #eff6ff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, .12);
        }

        .public-auth-cta {
            color: #fff;
            background: linear-gradient(135deg, var(--public-blue-deep), var(--public-blue) 52%, var(--public-cyan));
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
        }

        .public-auth-cta:hover {
            transform: translateY(-1px) scale(1.03);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .28);
        }

        .public-user-menu {
            position: relative;
        }

        .public-user-menu::after {
            content: "";
            position: absolute;
            top: 100%;
            right: 0;
            width: 100%;
            height: .8rem;
        }

        .public-avatar,
        .public-mobile-avatar {
            display: grid;
            height: 2.55rem;
            width: 2.55rem;
            place-items: center;
            border: 1px solid transparent;
            border-radius: 999px;
            color: #fff;
            background: linear-gradient(135deg, var(--public-blue-deep), var(--public-blue) 56%, var(--public-cyan));
            box-shadow: 0 10px 24px rgba(37, 99, 235, .22);
            font-size: .8rem;
            font-weight: 900;
            line-height: 1;
            text-decoration: none;
            transition: transform 180ms cubic-bezier(.16, 1, .3, 1), box-shadow 180ms ease-out;
        }

        .public-avatar:hover,
        .public-mobile-avatar:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, .28);
        }

        .public-user-dropdown {
            position: absolute;
            top: calc(100% + .7rem);
            right: 0;
            width: 17rem;
            border: 1px solid #e0e0e0;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
            opacity: 0;
            pointer-events: none;
            transform: translate3d(0, -6px, 0);
            transition: opacity 170ms ease-out, transform 170ms cubic-bezier(.16, 1, .3, 1);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .public-user-menu:hover .public-user-dropdown,
        .public-user-menu:focus-within .public-user-dropdown {
            opacity: 1;
            pointer-events: auto;
            transform: translate3d(0, 0, 0);
        }

        .public-dropdown-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(203, 213, 225, .95), transparent);
        }

        .public-dropdown-link {
            display: flex;
            width: 100%;
            align-items: center;
            gap: .65rem;
            border-radius: .375rem;
            padding: .72rem .78rem;
            color: #334155;
            font-size: .875rem;
            font-weight: 800;
            text-align: left;
            text-decoration: none;
            transition: background 180ms ease-out, color 180ms ease-out, transform 180ms cubic-bezier(.16, 1, .3, 1);
        }

        .public-dropdown-link svg {
            height: 1rem;
            width: 1rem;
            color: var(--public-sky);
            transition: color 180ms ease-out;
        }

        .public-dropdown-link:hover {
            color: #1e40af;
            background: #eff6ff;
            transform: translateX(2px);
        }

        .public-dropdown-link:is(button),
        .public-mobile-link:is(button) {
            border: 0;
            background: transparent;
            cursor: pointer;
            font-family: inherit;
        }

        .public-dropdown-link-danger:hover {
            background: #f1f3f5;
        }

        .public-dropdown-link-danger:hover svg {
            color: #1e40af;
        }

        .public-menu-button {
            display: grid;
            height: 2.55rem;
            width: 2.55rem;
            place-items: center;
            border: 1px solid #d8dee6;
            border-radius: .5rem;
            color: #1f2937;
            background: rgba(255, 255, 255, .78);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
            transition: border-color 180ms ease-out, background 180ms ease-out, color 180ms ease-out, box-shadow 180ms ease-out;
        }

        .public-menu-button:hover,
        .public-menu-button.is-open {
            border-color: rgba(37, 99, 235, .34);
            color: #1e40af;
            background: #eff6ff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, .12);
        }

        .public-menu-icon span {
            display: block;
            height: 2px;
            width: 1.25rem;
            border-radius: 999px;
            background: currentColor;
            transition: transform 180ms cubic-bezier(.16, 1, .3, 1), opacity 180ms ease-out;
        }

        .public-menu-icon span+span {
            margin-top: .32rem;
        }

        .public-menu-button.is-open .public-menu-icon span:nth-child(1) {
            transform: translateY(.44rem) rotate(45deg);
        }

        .public-menu-button.is-open .public-menu-icon span:nth-child(2) {
            opacity: 0;
        }

        .public-menu-button.is-open .public-menu-icon span:nth-child(3) {
            transform: translateY(-.44rem) rotate(-45deg);
        }

        .public-mobile-scrim {
            position: fixed;
            inset: 0;
            z-index: 55;
            pointer-events: none;
            background: rgba(15, 23, 42, .35);
            opacity: 0;
            transition: opacity 200ms ease-out;
        }

        .public-mobile-scrim.is-open {
            pointer-events: auto;
            opacity: 1;
        }

        .public-mobile-panel {
            position: fixed;
            top: 0;
            right: 0;
            z-index: 60;
            height: 100svh;
            width: min(88vw, 24rem);
            overflow-y: auto;
            border-left: 1px solid #e0e0e0;
            background: rgba(255, 255, 255, .97);
            box-shadow: -18px 0 44px rgba(15, 23, 42, .14);
            transform: translate3d(104%, 0, 0);
            transition: transform 240ms cubic-bezier(.16, 1, .3, 1);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .public-mobile-panel.is-open {
            transform: translate3d(0, 0, 0);
        }

        .public-mobile-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: .5rem;
            padding: .88rem .95rem;
            color: var(--public-muted);
            font-size: .95rem;
            font-weight: 800;
            text-decoration: none;
            opacity: 0;
            transform: translate3d(14px, 0, 0);
            transition:
                opacity 200ms ease-out,
                transform 220ms cubic-bezier(.16, 1, .3, 1),
                background 180ms ease-out,
                color 180ms ease-out;
        }

        .public-mobile-panel.is-open .public-mobile-link {
            opacity: 1;
            transform: translate3d(0, 0, 0);
            transition-delay: calc(80ms + (var(--item-index, 0) * 45ms));
        }

        .public-mobile-link:hover,
        .public-mobile-link.is-active {
            color: #1e40af;
            background: #eff6ff;
        }

        .public-mobile-link.public-auth-link,
        .public-mobile-link.public-auth-cta {
            justify-content: center;
        }

        body.public-mobile-open {
            overflow: hidden;
        }

        .auth-shell {
            position: relative;
            isolation: isolate;
            display: grid;
            min-height: calc(100vh - 5rem);
            place-items: center;
            overflow: hidden;
            background-color: var(--public-bg);
            background-image:
                radial-gradient(circle at 16% 18%, rgba(37, 99, 235, .1), transparent 34%),
                radial-gradient(circle at 84% 14%, rgba(6, 182, 212, .08), transparent 30%),
                linear-gradient(180deg, rgba(248, 249, 250, .88), rgba(241, 243, 245, .94) 54%, rgba(248, 249, 250, .96)),
                var(--public-image-section);
            background-position: center, center, center, center;
            background-repeat: no-repeat;
            background-size: auto, auto, auto, cover;
            padding: 4rem 1rem;
            color: var(--public-text);
        }

        .auth-shell::before {
            content: "";
            position: absolute;
            inset: -18% -12% -10%;
            z-index: -1;
            opacity: .2;
            filter: blur(24px) saturate(1.08);
            background:
                conic-gradient(from 130deg at 48% 44%, rgba(30, 64, 175, .16), rgba(37, 99, 235, .14), rgba(8, 145, 178, .12), rgba(241, 243, 245, .2), rgba(30, 64, 175, .16));
            animation: auth-aurora 24s cubic-bezier(.45, 0, .2, 1) infinite alternate;
        }

        .auth-card {
            width: min(100%, 28rem);
            border: 1px solid #e0e0e0;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .9);
            box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
            padding: 1.5rem;
            backdrop-filter: blur(18px);
        }

        .auth-field {
            position: relative;
        }

        .auth-icon {
            position: absolute;
            left: .9rem;
            top: 50%;
            height: 1rem;
            width: 1rem;
            color: #38bdf8;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .auth-input {
            width: 100%;
            border: 1px solid #cfd8e3;
            border-radius: .5rem;
            background: #ffffff;
            padding: .82rem .9rem .82rem 2.55rem;
            color: var(--public-text);
            transition: border-color 200ms ease-out, box-shadow 200ms ease-out;
        }

        .auth-input:focus {
            border-color: rgba(37, 99, 235, .52);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: 0;
        }

        .auth-button {
            display: inline-flex;
            min-height: 3rem;
            width: 100%;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            background: linear-gradient(135deg, #1e3a8a, #2563eb 52%, #06b6d4);
            padding: .85rem 1.2rem;
            color: #fff;
            font-weight: 900;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), box-shadow 200ms ease-out;
        }

        .auth-button:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .28);
        }

        .auth-outline-button {
            display: inline-flex;
            min-height: 2.8rem;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(37, 99, 235, .24);
            border-radius: .5rem;
            background: #ffffff;
            padding: .75rem 1rem;
            color: #1d4ed8;
            font-weight: 800;
            transition: background 200ms ease-out, color 200ms ease-out, border-color 200ms ease-out;
        }

        .auth-outline-button:hover {
            border-color: rgba(37, 99, 235, .38);
            background: #eff6ff;
            color: #1e40af;
        }

        @keyframes auth-aurora {
            0% {
                transform: translate3d(-2%, -1%, 0) scale(1.02) rotate(0deg);
            }

            100% {
                transform: translate3d(2%, 2%, 0) scale(1.08) rotate(5deg);
            }
        }

        @media (min-width: 1280px) {
            .public-nav-list {
                gap: 2.35rem;
            }
        }

        @media (min-width: 1024px) {

            .public-mobile-avatar.lg\:hidden,
            .public-menu-button.lg\:hidden {
                display: none;
            }
        }

        @media (max-width: 420px) {
            .public-brand-subtitle {
                display: none;
            }

            .public-wordmark {
                font-size: .95rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .public-header-glass,
            .public-header-glass::after,
            .public-logo,
            .public-wordmark,
            .public-nav-link,
            .public-nav-indicator,
            .public-auth-link,
            .public-auth-cta,
            .public-avatar,
            .public-mobile-avatar,
            .public-user-dropdown,
            .public-dropdown-link,
            .public-menu-button,
            .public-menu-icon span,
            .public-mobile-scrim,
            .public-mobile-panel,
            .public-mobile-link,
            .auth-shell::before,
            .auth-button,
            .auth-outline-button,
            .auth-input {
                animation: none !important;
                transition: none !important;
            }
        }
        .dtr-logo-text {
            font-family: 'Inter', 'Instrument Sans', sans-serif;
            font-weight: 900;
            letter-spacing: -0.035em;
            background: linear-gradient(135deg, #1d4ed8, #2563eb 54%, #0891b2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
            display: inline-block;
        }
    </style>
    @stack('styles')
    <style>
        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page, .auth-shell) :is(.text-white) {
            color: #212529;
        }

        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page, .auth-shell) :is(.text-slate-300, .text-slate-400, .text-zinc-300, .text-zinc-400) {
            color: #5c6675;
        }

        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page, .auth-shell) :is(.text-slate-500, .text-slate-600, .text-zinc-500, .text-zinc-600) {
            color: #6b7280;
        }

        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page, .auth-shell) :is(.text-cyan-100, .text-cyan-200, .text-cyan-300, .text-sky-200, .text-sky-300) {
            color: #0e7490;
        }

        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page, .auth-shell) :is(.text-blue-200, .text-blue-300) {
            color: #1d4ed8;
        }

        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page, .auth-shell) [class~="hover:text-white"]:hover,
        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page, .auth-shell) [class~="hover:text-cyan-200"]:hover,
        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page, .auth-shell) [class~="hover:text-cyan-300"]:hover {
            color: #0e7490;
        }

        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page) [class~="border-white/10"],
        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page) [class~="border-white/15"] {
            border-color: #e0e0e0;
        }

        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page) [class~="divide-white/10"]> :not([hidden])~ :not([hidden]) {
            border-color: #e0e0e0;
        }

        .public-light-theme :where(.dtr-home, .routes-page, .checkout-page, .my-routes-page, .route-detail-page, .public-dark-page, .contact-page, .blog-page, .blog-show-page) :is([class~="bg-white/[.04]"], [class~="bg-white/[.055]"], [class~="bg-white/[.06]"], [class~="bg-white/[.08]"]) {
            background-color: #f1f3f5;
        }

        .public-light-theme :where(.dtr-btn-primary, .routes-button-primary, .route-detail-button-primary, .contact-button, .blog-load-button, .auth-button, .public-auth-cta, [class~="bg-blue-700"], [class~="bg-blue-800"], [class~="bg-emerald-700"], [class~="bg-emerald-800"], [class~="bg-gradient-to-br"], .public-logo),
        .public-light-theme :where(.dtr-btn-primary, .routes-button-primary, .route-detail-button-primary, .contact-button, .blog-load-button, .auth-button, .public-auth-cta, [class~="bg-blue-700"], [class~="bg-blue-800"], [class~="bg-emerald-700"], [class~="bg-emerald-800"], [class~="bg-gradient-to-br"], .public-logo) * {
            color: #ffffff !important;
        }
    </style>
</head>

<body class="public-light-theme min-h-screen bg-zinc-50 text-zinc-950 antialiased">
    @php
        $headerOverlaysContent = request()->routeIs('home');
        $currentUser = auth()->user();
        
        $logoPath = public_path('images/Drivetestroute.png');
        $logoSrc = file_exists($logoPath) ? asset('images/Drivetestroute.png') : asset('public/images/Drivetestroute.png');
        
        $footerLogoPath = public_path('images/Drivetestroute.png');
        $footerLogoSrc = file_exists($footerLogoPath) ? asset('images/Drivetestroute.png') : asset('public/images/Drivetestroute.png');

        $dashboardUrl = null;
        $userInitials = 'U';

        if ($currentUser) {
            $dashboardUrl = $currentUser->is_admin ? route('admin.dashboard') : route('driving-routes.my');
            $nameParts = collect(preg_split('/\s+/', trim($currentUser->name ?? '')))->filter();
            $userInitials = $nameParts
                ->take(2)
                ->map(fn($part) => \Illuminate\Support\Str::substr($part, 0, 1))
                ->join('');
            $userInitials = \Illuminate\Support\Str::upper(
                $userInitials ?: \Illuminate\Support\Str::substr($currentUser->email ?? 'U', 0, 1),
            );
        }

        $publicNavItems = [
            ['label' => 'Home', 'route' => 'home', 'active' => ['home']],
            ['label' => 'About', 'route' => 'about', 'active' => ['about']],
            [
                'label' => 'Routes',
                'route' => 'driving-routes.index',
                'active' => ['routes.index', 'driving-routes.index'],
            ],
            ['label' => 'Blog', 'route' => 'blog', 'active' => ['blog']],
            ['label' => 'Contact Us', 'route' => 'contact', 'active' => ['contact']],
        ];
    @endphp

    <header id="public-header" data-transparent-header="true" class="public-header-glass">
        <nav
            class="public-header-nav mx-auto flex min-h-[4.75rem] max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="public-brand flex shrink-0 items-center gap-2.5">
                <img src="{{ $logoSrc }}" alt="Driver Test Routes" class="h-12 w-auto">
                <div class="flex flex-col">
                    <span class="dtr-logo-text font-black text-xl tracking-tight leading-none">DriveTest Route</span>
                    <span class="dtr-logo-subtitle text-[9px] uppercase tracking-[0.15em] font-extrabold text-slate-500 mt-1 leading-none">Practice Platform</span>
                </div>
            </a>

            <div class="hidden flex-1 items-center justify-center lg:flex">
                <div class="public-nav-list" data-public-nav>
                    @foreach ($publicNavItems as $item)
                        @php($isActive = collect($item['active'])->contains(fn($pattern) => request()->routeIs($pattern)))
                        <a href="{{ route($item['route']) }}"
                            class="public-nav-link {{ $isActive ? 'public-nav-active' : '' }}" data-public-nav-link
                            @if ($isActive) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <span class="public-nav-indicator" data-public-nav-indicator aria-hidden="true"></span>
                </div>
            </div>

            <div class="flex shrink-0 items-center justify-end gap-2">
                @auth
                    <div class="public-user-menu hidden lg:block">
                        <button type="button" class="public-avatar" aria-label="Open user menu">
                            {{ $userInitials }}
                        </button>

                        <div class="public-user-dropdown p-3">
                            <div class="px-2 pb-3 pt-1">
                                <p class="truncate text-sm font-black text-zinc-950">{{ $currentUser->name }}</p>
                                <p class="mt-1 truncate text-xs font-medium text-slate-600">{{ $currentUser->email }}</p>
                            </div>

                            <div class="public-dropdown-divider mb-2"></div>

                            <a href="{{ $dashboardUrl }}" class="public-dropdown-link">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    aria-hidden="true">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5" />
                                    <rect x="14" y="3" width="7" height="7" rx="1.5" />
                                    <rect x="14" y="14" width="7" height="7" rx="1.5" />
                                    <rect x="3" y="14" width="7" height="7" rx="1.5" />
                                </svg>
                                Dashboard
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="public-dropdown-link public-dropdown-link-danger">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                        aria-hidden="true">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <path d="M16 17l5-5-5-5" />
                                        <path d="M21 12H9" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>

                    <a href="{{ $dashboardUrl }}" class="public-mobile-avatar lg:hidden" aria-label="Open dashboard">
                        {{ $userInitials }}
                    </a>
                @else
                    <div class="hidden items-center gap-2 lg:flex">
                        <a href="{{ route('login') }}" class="public-auth-link">Log In</a>
                        <a href="{{ route('register') }}" class="public-auth-cta">Sign Up</a>
                    </div>
                @endauth

                <button id="public-menu-toggle" type="button" class="public-menu-button lg:hidden"
                    aria-controls="public-mobile-menu" aria-expanded="false" aria-label="Open navigation">
                    <span class="public-menu-icon" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>
        </nav>

        <div id="public-mobile-scrim" class="public-mobile-scrim lg:hidden" aria-hidden="true"></div>

        <aside id="public-mobile-menu" class="public-mobile-panel lg:hidden" aria-hidden="true">
            <div class="px-5 pb-8 pt-24">
                <div class="border-b border-zinc-200 pb-5">
                    <p class="text-sm font-black text-zinc-950">Driver Test Routes</p>
                    <p class="mt-1 text-xs font-medium text-zinc-600">Practice maps for test day</p>
                </div>

                <nav class="mt-5 space-y-2" aria-label="Mobile navigation">
                    @foreach ($publicNavItems as $item)
                        @php($isActive = collect($item['active'])->contains(fn($pattern) => request()->routeIs($pattern)))
                        <a href="{{ route($item['route']) }}"
                            class="public-mobile-link {{ $isActive ? 'is-active' : '' }}"
                            style="--item-index: {{ $loop->index }};"
                            @if ($isActive) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="mt-6 border-t border-zinc-200 pt-5">
                    @auth
                        <div class="public-mobile-link mb-2 justify-start gap-3"
                            style="--item-index: {{ count($publicNavItems) }};">
                            <span class="public-avatar h-10 w-10">{{ $userInitials }}</span>
                            <span class="min-w-0">
                                <span
                                    class="block truncate text-sm font-black text-zinc-950">{{ $currentUser->name }}</span>
                                <span
                                    class="mt-1 block truncate text-xs font-medium text-zinc-600">{{ $currentUser->email }}</span>
                            </span>
                        </div>

                        <a href="{{ $dashboardUrl }}" class="public-mobile-link"
                            style="--item-index: {{ count($publicNavItems) + 1 }};">Dashboard</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="public-mobile-link w-full"
                                style="--item-index: {{ count($publicNavItems) + 2 }};">
                                Logout
                            </button>
                        </form>
                    @else
                        <div class="grid gap-3">
                            <a href="{{ route('login') }}" class="public-mobile-link public-auth-link"
                                style="--item-index: {{ count($publicNavItems) }};">Log In</a>
                            <a href="{{ route('register') }}" class="public-mobile-link public-auth-cta"
                                style="--item-index: {{ count($publicNavItems) + 1 }};">Sign Up</a>
                        </div>
                    @endauth
                </div>
            </div>
        </aside>
    </header>

    <main class="{{ $headerOverlaysContent ? '' : 'public-main-offset' }}">
        @if (session('success') || session('error') || $errors->any())
            <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-semibold">Please fix the highlighted fields.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-zinc-200 bg-[#f8f9fa] text-zinc-950">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1.5fr_1fr_1fr] lg:px-8">
            <div>
                <a href="{{ route('home') }}" class="public-brand flex shrink-0 items-center gap-2.5">
                    <img src="{{ $footerLogoSrc }}" alt="Driver Test Routes" class="h-14 w-auto">
                    <div class="flex flex-col">
                        <span class="dtr-logo-text font-black text-2xl tracking-tight leading-none">DriveTest Route</span>
                        <span class="text-[10px] uppercase tracking-[0.15em] font-extrabold text-slate-500 mt-1 leading-none">Practice Platform</span>
                    </div>
                </a>
                <p class="mt-4 max-w-md text-sm leading-6 text-zinc-600">
                    Paid driving test route maps built for focused practice, clear route planning, and confident
                    test-day preparation.
                </p>
            </div>

            <div>
                <h2 class="text-sm font-bold text-zinc-950">Pages</h2>
                <div class="mt-3 space-y-2 text-sm">
                    <a href="{{ route('home') }}" class="block text-zinc-600 transition hover:text-blue-700">Home</a>
                    <a href="{{ route('about') }}"
                        class="block text-zinc-600 transition hover:text-blue-700">About</a>
                    <a href="{{ route('driving-routes.index') }}"
                        class="block text-zinc-600 transition hover:text-blue-700">Routes</a>
                    <a href="{{ route('blog') }}" class="block text-zinc-600 transition hover:text-blue-700">Blog</a>
                    <a href="{{ route('contact') }}"
                        class="block text-zinc-600 transition hover:text-blue-700">Contact Us</a>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-bold text-zinc-950">Account</h2>
                <div class="mt-3 space-y-2 text-sm">
                    @auth
                        <a href="{{ route('driving-routes.my') }}"
                            class="block text-zinc-600 transition hover:text-blue-700">My Routes</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="block text-zinc-600 transition hover:text-blue-700">Login</a>
                        <a href="{{ route('register') }}"
                            class="block text-zinc-600 transition hover:text-blue-700">Create Account</a>
                    @endauth
                </div>
            </div>
        </div>
    </footer>
    <script>
        (() => {
            const publicHeader = document.getElementById('public-header');
            const publicMenuToggle = document.getElementById('public-menu-toggle');
            const publicMobileMenu = document.getElementById('public-mobile-menu');
            const publicMobileScrim = document.getElementById('public-mobile-scrim');
            const publicNav = document.querySelector('[data-public-nav]');
            const publicNavIndicator = document.querySelector('[data-public-nav-indicator]');
            const publicNavLinks = Array.from(document.querySelectorAll('[data-public-nav-link]'));
            const activePublicNavLink = publicNavLinks.find((link) => link.getAttribute('aria-current') === 'page');

            function isPublicMobileMenuOpen() {
                return Boolean(publicMobileMenu?.classList.contains('is-open'));
            }

            function syncPublicHeader() {
                if (!publicHeader) {
                    return;
                }

                publicHeader.classList.toggle('public-header-scrolled', window.scrollY > 12 ||
                    isPublicMobileMenuOpen());
            }

            function movePublicNavIndicator(link) {
                if (!publicNav || !publicNavIndicator || !link || publicNav.offsetParent === null) {
                    return;
                }

                const navRect = publicNav.getBoundingClientRect();
                const linkRect = link.getBoundingClientRect();

                publicNavIndicator.style.width = `${linkRect.width}px`;
                publicNavIndicator.style.transform = `translate3d(${linkRect.left - navRect.left}px, 0, 0)`;
                publicNavIndicator.classList.add('is-visible');
            }

            function resetPublicNavIndicator() {
                if (activePublicNavLink) {
                    movePublicNavIndicator(activePublicNavLink);
                    return;
                }

                publicNavIndicator?.classList.remove('is-visible');
            }

            publicNavLinks.forEach((link) => {
                link.addEventListener('mouseenter', () => movePublicNavIndicator(link));
                link.addEventListener('focus', () => movePublicNavIndicator(link));
            });

            publicNav?.addEventListener('mouseleave', resetPublicNavIndicator);
            publicNav?.addEventListener('focusout', (event) => {
                if (!publicNav.contains(event.relatedTarget)) {
                    resetPublicNavIndicator();
                }
            });

            function setPublicMobileMenu(open) {
                publicMobileMenu?.classList.toggle('is-open', open);
                publicMobileScrim?.classList.toggle('is-open', open);
                publicMenuToggle?.classList.toggle('is-open', open);
                publicMenuToggle?.setAttribute('aria-expanded', String(open));
                publicMobileMenu?.setAttribute('aria-hidden', String(!open));
                document.body.classList.toggle('public-mobile-open', open);
                syncPublicHeader();
            }

            publicMenuToggle?.addEventListener('click', () => {
                setPublicMobileMenu(!isPublicMobileMenuOpen());
            });

            publicMobileScrim?.addEventListener('click', () => setPublicMobileMenu(false));

            publicMobileMenu?.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => setPublicMobileMenu(false));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    setPublicMobileMenu(false);
                }
            });

            window.addEventListener('scroll', syncPublicHeader, {
                passive: true
            });
            window.addEventListener('resize', () => {
                resetPublicNavIndicator();

                if (window.innerWidth >= 1024 && isPublicMobileMenuOpen()) {
                    setPublicMobileMenu(false);
                }
            }, {
                passive: true
            });

            document.fonts?.ready.then(resetPublicNavIndicator);
            requestAnimationFrame(() => {
                resetPublicNavIndicator();
                syncPublicHeader();
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>
