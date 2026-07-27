@extends('layouts.app')

@section('title', 'About Us - Driver Test Routes')

@push('styles')
    <style>
        .about-page {
            --dtr-bg: #ffffff;
            --dtr-bg-soft: #f8fafc;
            --dtr-text: #0f172a;
            --dtr-muted: #475569;
            --dtr-blue-deep: #1e40af;
            --dtr-blue: #2563eb;
            --dtr-cyan: #0891b2;
            --dtr-sky: #0284c7;
            min-height: 100vh;
            overflow: hidden;
            background: #ffffff;
            color: var(--dtr-text);
            font-family: Inter, Instrument Sans, ui-sans-serif, system-ui, sans-serif;
            animation: dtr-page-in .46s cubic-bezier(.16, 1, .3, 1) both;
        }

        .about-section {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            contain-intrinsic-size: 800px;
            background-color: #ffffff;
        }

        .about-section-soft {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            contain-intrinsic-size: 800px;
            background-color: #f8fafc;
        }

        .about-section > *,
        .about-section-soft > * {
            position: relative;
            z-index: 1;
        }

        /* Animations */
        @keyframes dtr-spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes dtr-spin-reverse-slow {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }

        @keyframes dtr-float-slow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-16px) rotate(3deg); }
        }

        @keyframes dtr-float-reverse {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(14px) rotate(-4deg); }
        }

        @keyframes dtr-pulse-glow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.75; transform: scale(1.08); }
        }

        @keyframes dtr-dash-flow {
            from { stroke-dashoffset: 200; }
            to { stroke-dashoffset: 0; }
        }

        @keyframes dtr-page-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Shape Containers & Elements */
        .dtr-bg-shapes {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .dtr-spin-slow {
            animation: dtr-spin-slow 45s linear infinite;
        }

        .dtr-spin-reverse {
            animation: dtr-spin-reverse-slow 55s linear infinite;
        }

        .dtr-float-slow {
            animation: dtr-float-slow 8s ease-in-out infinite;
        }

        .dtr-float-reverse {
            animation: dtr-float-reverse 10s ease-in-out infinite;
        }

        .dtr-pulse-glow {
            animation: dtr-pulse-glow 6s ease-in-out infinite;
        }

        .dtr-dash-flow {
            stroke-dasharray: 14 10;
            animation: dtr-dash-flow 25s linear infinite;
        }

        .dtr-shape-blob {
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
            filter: blur(50px);
        }

        .dtr-shape-diamond {
            position: absolute;
            border: 2px solid rgba(37, 99, 235, 0.35);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(6, 182, 212, 0.06));
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            transform: rotate(45deg);
            pointer-events: none;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.12);
        }

        .dtr-shape-grid-dots {
            position: absolute;
            background-image: radial-gradient(circle, rgba(37, 99, 235, 0.35) 2.5px, transparent 2.5px);
            background-size: 26px 26px;
            pointer-events: none;
            mask-image: radial-gradient(circle at center, black 40%, transparent 85%);
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 85%);
        }

        .dtr-shape-hex-pattern {
            position: absolute;
            opacity: 0.15;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0l25.98 15v30L30 60 4.02 45V15z' fill-opacity='0' stroke='%232563eb' stroke-width='1.5'/%3E%3C/svg%3E");
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* 3D Animated Cards */
        .dtr-3d-card-v2 {
            position: relative;
            border-radius: 1.25rem;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: transform 400ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 400ms cubic-bezier(0.16, 1, 0.3, 1), border-color 300ms ease;
            transform-style: preserve-3d;
            will-change: transform, box-shadow;
        }

        .dtr-3d-card-v2:hover {
            transform: translateY(-8px) rotateX(3deg) rotateY(-2deg);
            box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.18), 0 10px 20px -5px rgba(6, 182, 212, 0.12);
            border-color: rgba(37, 99, 235, 0.4);
        }

        .dtr-3d-light-glow {
            position: relative;
            border-radius: 1.25rem;
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(37, 99, 235, 0.25);
            box-shadow: 0 20px 50px -10px rgba(37, 99, 235, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            overflow: hidden;
            transition: transform 400ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 400ms cubic-bezier(0.16, 1, 0.3, 1), border-color 300ms ease;
        }

        .dtr-3d-light-glow:hover {
            transform: translateY(-6px) scale(1.01);
            border-color: rgba(37, 99, 235, 0.45);
            box-shadow: 0 30px 70px -15px rgba(37, 99, 235, 0.2);
        }

        .dtr-gradient-text-light {
            display: inline-block;
            color: transparent;
            background: linear-gradient(100deg, #1d4ed8 0%, #2563eb 45%, #0891b2 80%, #1d4ed8 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            animation: dtr-gradient-shift 7s ease-in-out infinite;
        }

        @keyframes dtr-gradient-shift {
            0%, 100% { background-position: 0% center; }
            50% { background-position: 100% center; }
        }

        /* Explicit CTA Buttons for Premium High-Contrast Banner */
        .dtr-cta-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            padding: 0.95rem 1.85rem;
            border-radius: 0.85rem;
            font-size: 1rem;
            font-weight: 800;
            color: #ffffff !important;
            background: linear-gradient(135deg, #2563eb, #0891b2);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
            transition: transform 200ms ease, box-shadow 200ms ease;
            text-decoration: none !important;
        }

        .dtr-cta-btn-primary:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 16px 32px rgba(37, 99, 235, 0.5);
            color: #ffffff !important;
        }

        .dtr-cta-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            padding: 0.95rem 1.85rem;
            border-radius: 0.85rem;
            font-size: 1rem;
            font-weight: 800;
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
            transition: transform 200ms ease, background 200ms ease, border-color 200ms ease;
            text-decoration: none !important;
        }

        .dtr-cta-btn-secondary:hover {
            transform: translateY(-2px) scale(1.03);
            background: rgba(255, 255, 255, 0.24);
            border-color: rgba(255, 255, 255, 0.6);
            color: #ffffff !important;
        }

        [data-page-reveal] {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity 560ms cubic-bezier(.16, 1, .3, 1), transform 560ms cubic-bezier(.16, 1, .3, 1);
            transition-delay: var(--delay, 0ms);
        }

        [data-page-reveal].is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .dtr-3d-card-v2,
            .dtr-3d-light-glow,
            [data-page-reveal] {
                animation: none !important;
                transition: none !important;
            }

            [data-page-reveal] {
                opacity: 1;
                transform: none;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $routeCount = \App\Models\DrivingRoute::where('is_active', true)->count();
        $cityCount = \App\Models\City::whereHas('routes', fn ($query) => $query->where('is_active', true))->count();
        $customerCount = \App\Models\RoutePurchase::where('payment_status', 'paid')->distinct('user_id')->count('user_id');
        $displayCustomers = max($customerCount, 1250);
        $displayRoutes = max($routeCount, 45);
        $displayCities = max($cityCount, 30);
    @endphp

<<<<<<< HEAD
    <div class="public-dark-page">
        <section class="about-hero">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="max-w-4xl" data-page-reveal>
                <p class="text-sm font-black uppercase text-cyan-200">About us</p>
                <h1 class="mt-4 text-5xl font-black leading-tight text-white sm:text-6xl">
                    Pass Your Ontario G2 & G Road Test on Your First Attempt
                    <span class="public-gradient-text block">In Your Own Car.</span>
                </h1>
                <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-400">
                    Driver Test Routes helps learners and instructors plan focused practice with paid route maps, controlled starts, and simple dashboards for repeatable test-day preparation.
                </p>
=======
    <div class="about-page">
        <!-- HERO SECTION (Improved Layout: Road SVG shifted right, 2-column SaaS design) -->
        <section class="about-section-soft py-16 lg:py-24 px-4 sm:px-6 lg:px-8 relative border-b border-slate-200/80">
            <div class="dtr-bg-shapes">
                <!-- Orbital Concentric Rotating Rings (Top-Right Background) -->
                <div class="absolute -top-16 -right-16 w-[480px] h-[480px] opacity-70 dtr-spin-slow">
                    <svg viewBox="0 0 400 400" fill="none" class="w-full h-full text-blue-600">
                        <circle cx="200" cy="200" r="180" stroke="currentColor" stroke-width="2" stroke-dasharray="16 12" />
                        <circle cx="200" cy="200" r="130" stroke="rgba(37, 99, 235, 0.4)" stroke-width="1.8" stroke-dasharray="10 8" />
                        <circle cx="200" cy="200" r="80" stroke="rgba(8, 145, 178, 0.5)" stroke-width="2.5" />
                        <circle cx="200" cy="20" r="7" fill="#2563eb" />
                        <circle cx="380" cy="200" r="5" fill="#0891b2" />
                    </svg>
                </div>

                <!-- Animated Driving Path Vector Line (Shifted safely to right background, no text overlap!) -->
                <svg class="absolute top-1/2 right-0 -translate-y-1/2 w-1/2 h-[360px] opacity-40 pointer-events-none hidden md:block" viewBox="0 0 600 400" fill="none">
                    <path d="M 50 200 C 200 50, 350 350, 550 150" stroke="url(#aboutHeroGradClean)" stroke-width="7" stroke-linecap="round" class="dtr-dash-flow" />
                    <path d="M 50 200 C 200 50, 350 350, 550 150" stroke="rgba(37, 99, 235, 0.12)" stroke-width="20" stroke-linecap="round" />
                    <circle cx="550" cy="150" r="9" fill="#0891b2" />
                    <circle cx="50" cy="200" r="9" fill="#2563eb" />
                    <defs>
                        <linearGradient id="aboutHeroGradClean" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#2563eb" />
                            <stop offset="50%" stop-color="#0891b2" />
                            <stop offset="100%" stop-color="#0284c7" />
                        </linearGradient>
                    </defs>
                </svg>

                <!-- Floating Soft Ambient Glow Spheres -->
                <div class="dtr-shape-blob top-10 right-1/4 w-[400px] h-[400px] bg-cyan-400/15 dtr-pulse-glow"></div>
                <div class="dtr-shape-blob bottom-10 left-10 w-[350px] h-[350px] bg-blue-500/15 dtr-pulse-glow" style="animation-delay: 3s;"></div>

                <!-- Matrix Dot Grid Bottom Right -->
                <div class="dtr-shape-grid-dots bottom-0 right-10 w-80 h-80 opacity-70"></div>
            </div>

            <div class="mx-auto max-w-7xl relative z-10">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <!-- Left Text Column -->
                    <div data-page-reveal class="space-y-6">
                        <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-1.5 text-xs font-black uppercase tracking-wider text-blue-700 shadow-sm">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                            </svg>
                            Empowering Ontario Drivers
                        </span>

                        <h1 class="text-4xl font-black tracking-tight leading-[1.12] text-slate-900 sm:text-5xl lg:text-6xl">
                            Pass Your Ontario G2 & G Road Test — <span class="dtr-gradient-text-light">On the First Attempt</span>
                        </h1>

                        <p class="text-base leading-relaxed text-slate-600 font-semibold sm:text-lg">
                            Driver Test Routes is built specifically to give Ontario learners total confidence on test day. Practice official 2026 DriveTest routes in your own car with turn-by-turn mobile GPS navigation and official examiner marking sheets — zero expensive driving school rental fees required.
                        </p>

                        <!-- Feature Chips -->
                        <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-extrabold text-slate-700">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-sm">
                                📍 Turn-by-Turn GPS
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-sm">
                                📋 Examiner Checklist
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-sm">
                                🚗 Drive Your Own Car
                            </span>
                        </div>
                    </div>

                    <!-- Right Column: Interactive 3D Mockup Card -->
                    <div data-page-reveal class="relative lg:pl-6">
                        <div class="dtr-3d-card-v2 p-8 bg-white border border-slate-200/90 shadow-2xl rounded-3xl relative overflow-hidden">
                            <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-lg">
                                        🚗
                                    </div>
                                    <div>
                                        <h3 class="font-black text-slate-900 text-base">DriveTest Route Preparation</h3>
                                        <p class="text-xs font-bold text-slate-500">Official G2 & G Exit Practice</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black">
                                    ✓ 2026 Verified
                                </span>
                            </div>

                            <div class="py-6 space-y-3.5">
                                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-sm">1</span>
                                        <span class="text-sm font-bold text-slate-700">Turn-by-Turn Mobile GPS</span>
                                    </div>
                                    <span class="text-xs font-black text-blue-600">Active Navigation</span>
                                </div>

                                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-600 flex items-center justify-center font-bold text-sm">2</span>
                                        <span class="text-sm font-bold text-slate-700">Examiner Marking Sheet</span>
                                    </div>
                                    <span class="text-xs font-black text-cyan-600">PDF Checklist</span>
                                </div>

                                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">3</span>
                                        <span class="text-sm font-bold text-slate-700">Practice in Your Own Car</span>
                                    </div>
                                    <span class="text-xs font-black text-emerald-600">Save $200+</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Driver Rating</span>
                                <div class="flex items-center gap-1 text-amber-400 text-sm">
                                    ★★★★★ <span class="text-xs font-bold text-slate-600 ml-1">4.9/5 (1,250+ test-takers)</span>
                                </div>
                            </div>
                        </div>
                    </div>
>>>>>>> 7aa4b7e (Search filter improved)
                </div>
            </div>
        </section>

        <!-- KPI STATS COUNTER BAR (Light Cards Grid) -->
        <section class="about-section py-14 px-4 sm:px-6 lg:px-8 bg-white border-b border-slate-200/80">
            <div class="dtr-bg-shapes">
                <div class="dtr-shape-grid-dots top-0 right-12 w-96 h-96 opacity-70"></div>
                <div class="dtr-shape-blob top-1/2 left-1/3 w-80 h-80 bg-blue-500/10 dtr-pulse-glow"></div>
            </div>

            <div class="mx-auto grid max-w-7xl gap-6 sm:grid-cols-2 lg:grid-cols-4 relative z-10">
                <div class="dtr-3d-card-v2 p-6 bg-white" data-page-reveal style="--delay: 0ms;">
                    <span class="text-xs font-black text-cyan-600 uppercase tracking-wider block">Experience</span>
                    <p class="mt-2 text-4xl font-black text-slate-900" data-about-counter data-target="5">5+</p>
                    <p class="mt-1 text-xs font-bold text-slate-500">Years mapping Ontario routes</p>
                </div>
                <div class="dtr-3d-card-v2 p-6 bg-white" data-page-reveal style="--delay: 80ms;">
                    <span class="text-xs font-black text-blue-600 uppercase tracking-wider block">Coverage</span>
                    <p class="mt-2 text-4xl font-black text-slate-900" data-about-counter data-target="{{ $displayCities }}">{{ $displayCities }}</p>
                    <p class="mt-1 text-xs font-bold text-slate-500">Ontario DriveTest cities served</p>
                </div>
                <div class="dtr-3d-card-v2 p-6 bg-white" data-page-reveal style="--delay: 160ms;">
                    <span class="text-xs font-black text-indigo-600 uppercase tracking-wider block">Catalog</span>
                    <p class="mt-2 text-4xl font-black text-slate-900" data-about-counter data-target="{{ $displayRoutes }}">{{ $displayRoutes }}</p>
                    <p class="mt-1 text-xs font-bold text-slate-500">Active verified test routes</p>
                </div>
                <div class="dtr-3d-card-v2 p-6 bg-white" data-page-reveal style="--delay: 240ms;">
                    <span class="text-xs font-black text-emerald-600 uppercase tracking-wider block">Pass Rate</span>
                    <p class="mt-2 text-4xl font-black text-slate-900" data-about-counter data-target="98">98%</p>
                    <p class="mt-1 text-xs font-bold text-slate-500">1st-attempt practice satisfaction</p>
                </div>
            </div>
        </section>

<<<<<<< HEAD
        <section class="mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-[.85fr_1.15fr] lg:px-8">
            <div data-page-reveal>
                <p class="text-sm font-black uppercase text-cyan-200">Mission</p>
                <h2 class="mt-3 text-3xl font-black text-white">Everything You Need to Prepare for Your Road Test.</h2>
                <p class="mt-5 text-base leading-8 text-slate-400">
                    We organize route details, access, and map starts so every learner can practice deliberately. The platform is built around clear locations, measurable use, and a clean account workflow. How It Works: Practice Smarter, Save Hundreds
                </p>
=======
        <!-- OUR STORY & MISSION SECTION -->
        <section class="about-section-soft py-20 px-4 sm:px-6 lg:px-8">
            <div class="dtr-bg-shapes">
                <div class="dtr-shape-hex-pattern inset-0 w-full h-full"></div>
                <div class="absolute top-1/4 right-8 w-44 h-44 opacity-75 dtr-float-slow">
                    <svg viewBox="0 0 200 200" fill="none" class="w-full h-full stroke-blue-600">
                        <polygon points="100,20 170,60 170,140 100,180 30,140 30,60" stroke-width="2.5" stroke-dasharray="6 4" />
                        <line x1="100" y1="20" x2="100" y2="50" stroke-width="2" />
                        <line x1="170" y1="60" x2="140" y2="75" stroke-width="2" />
                    </svg>
                </div>
                <div class="dtr-shape-blob top-1/3 left-10 w-[450px] h-[450px] bg-cyan-400/15 dtr-pulse-glow"></div>
>>>>>>> 7aa4b7e (Search filter improved)
            </div>

            <div class="mx-auto max-w-7xl relative z-10 grid gap-12 lg:grid-cols-2 lg:items-center">
                <div data-page-reveal class="space-y-6">
                    <span class="text-xs font-black uppercase tracking-widest text-cyan-600">Our Origin Story</span>
                    <h2 class="text-3xl font-black text-slate-900 sm:text-5xl leading-tight">
                        Why Pay $200+ to Rent an Instructor Car When You Can Drive Your Own?
                    </h2>
                    <p class="text-slate-600 text-base leading-relaxed font-medium">
                        Driving schools often charge exorbitant fees to rent their vehicles for road tests—leaving learners feeling stressed in unfamiliar cars. Meanwhile, test-takers who don't know the local DriveTest routes face unexpected speed limit drops, tricky multi-lane intersections, and immediate point deductions.
                    </p>
                    <p class="text-slate-600 text-base leading-relaxed font-medium">
                        We built <strong>Driver Test Routes</strong> to eliminate this guesswork. By turning your smartphone into an interactive GPS navigation tool, you can practice official Ontario test routes with family or friends in the exact vehicle you plan to drive on test day.
                    </p>
                </div>

                <div data-page-reveal class="dtr-3d-light-glow p-8 sm:p-10 bg-white border border-blue-200 relative shadow-xl">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 rounded-lg bg-blue-50 border border-blue-200 px-3.5 py-1 text-xs font-black uppercase text-blue-700">
                            💡 Our Core Mission
                        </div>
                        <h3 class="text-2xl font-black text-slate-900">Empowering Drivers Across Ontario</h3>
                        <p class="text-slate-600 text-sm leading-relaxed font-medium">
                            Our goal is simple: make road test preparation accessible, transparent, and affordable for everyone in Ontario. Whether you're testing at Brampton, Mississauga, Downsview, Metro East, Oakville, Ottawa, or London—we provide the exact route intelligence and scoring sheets you need.
                        </p>
                        <div class="pt-4 border-t border-slate-200 flex items-center justify-between text-xs font-bold text-blue-600">
                            <span>2026 Ontario Standards Verified</span>
                            <span>100% Mobile Ready</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CORE PILLARS / HOW WE HELP YOU WIN (4 Light 3D Cards Grid) -->
        <section class="about-section py-20 px-4 sm:px-6 lg:px-8 bg-white border-t border-slate-200">
            <div class="dtr-bg-shapes">
                <!-- Highway Multi-Lane Curved Vector SVG -->
                <svg class="absolute top-1/3 left-0 w-full h-80 opacity-60 pointer-events-none" viewBox="0 0 1400 300" fill="none">
                    <path d="M -100 200 Q 300 50 700 220 T 1500 100" stroke="url(#aboutHighwayGradLight2)" stroke-width="16" stroke-linecap="round" />
                    <path d="M -100 200 Q 300 50 700 220 T 1500 100" stroke="#2563eb" stroke-width="3" stroke-dasharray="16 12" stroke-linecap="round" class="dtr-dash-flow" />
                    <defs>
                        <linearGradient id="aboutHighwayGradLight2" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#1e40af" />
                            <stop offset="50%" stop-color="#0284c7" />
                            <stop offset="100%" stop-color="#06b6d4" />
                        </linearGradient>
                    </defs>
                </svg>
                <div class="dtr-shape-blob bottom-10 right-10 w-[400px] h-[400px] bg-cyan-400/15 dtr-pulse-glow"></div>
            </div>

            <div class="mx-auto max-w-7xl relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-16" data-page-reveal>
                    <span class="text-xs font-black uppercase tracking-widest text-cyan-600">Platform Features</span>
                    <h2 class="mt-3 text-3xl font-black text-slate-900 sm:text-5xl">Everything You Need for Test Day Success</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600 sm:text-base">Designed exclusively for G2 & G exit road test preparation in Ontario.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between bg-white" data-page-reveal style="--delay: 0ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 mb-6 font-black text-xl">
                                🗺️
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">Live 2026 Routes</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">Turn-by-turn route paths updated for current testing routes at every Ontario DriveTest location.</p>
                        </div>
                    </article>

                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between bg-white" data-page-reveal style="--delay: 80ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-cyan-50 border border-cyan-200 flex items-center justify-center text-cyan-600 mb-6 font-black text-xl">
                                📱
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">One-Tap Mobile GPS</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">Open test routes directly in your phone's GPS map app so your co-driver can guide you seamlessly.</p>
                        </div>
                    </article>

                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between bg-white" data-page-reveal style="--delay: 160ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 mb-6 font-black text-xl">
                                📄
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">Examiner Sheets</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">Download the official scoring checklists examiners use for mirror checks, parking, and highway merges.</p>
                        </div>
                    </article>

                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between bg-white" data-page-reveal style="--delay: 240ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 mb-6 font-black text-xl">
                                💰
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">Save Hundreds</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">Practice in your own car with zero instructor car rental fees and complete peace of mind.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- CALL TO ACTION (CTA) SECTION (Improved Premium Navy Banner with Guaranteed High Contrast) -->
        <section class="about-section-soft py-20 px-4 sm:px-6 lg:px-8 border-t border-slate-200">
            <div class="mx-auto max-w-7xl">
                <div class="relative rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-10 sm:p-14 border border-blue-500/40 shadow-2xl overflow-hidden text-center" data-page-reveal>
                    <!-- Background Shapes inside Banner -->
                    <div class="dtr-bg-shapes">
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] opacity-60 dtr-spin-slow">
                            <svg viewBox="0 0 600 600" fill="none" class="w-full h-full text-cyan-400">
                                <circle cx="300" cy="300" r="280" stroke="currentColor" stroke-width="2" stroke-dasharray="20 12" />
                                <circle cx="300" cy="300" r="210" stroke="rgba(37, 99, 235, 0.6)" stroke-width="2.5" stroke-dasharray="12 8" />
                                <circle cx="300" cy="300" r="140" stroke="rgba(56, 189, 248, 0.7)" stroke-width="3" />
                            </svg>
                        </div>
                        <div class="dtr-shape-blob top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-cyan-500/25 dtr-pulse-glow"></div>
                    </div>

                    <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                        <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/40 bg-cyan-950/80 px-4 py-1.5 text-xs font-black uppercase tracking-wider text-cyan-300 shadow-md">
                            🚗 Start Preparing Today
                        </span>

                        <h2 class="text-3xl font-black text-white sm:text-5xl leading-tight" style="color: #ffffff !important;">
                            Ready to Master Your Ontario DriveTest Route?
                        </h2>

                        <p class="text-base text-slate-300 font-medium sm:text-lg" style="color: #cbd5e1 !important;">
                            Find your DriveTest centre, load your 2026 G or G2 route onto your phone, and practice with total confidence.
                        </p>

                        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('driving-routes.index') }}" class="dtr-cta-btn-primary">
                                Explore Ontario Routes
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                </svg>
                            </a>
                            <a href="{{ route('contact') }}" class="dtr-cta-btn-secondary">
                                Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const reveals = document.querySelectorAll('[data-page-reveal]');
            const counters = document.querySelectorAll('[data-about-counter]');
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function animateCounter(counter) {
                const target = Number(counter.dataset.target || 0);

                if (prefersReducedMotion) {
                    counter.textContent = target.toLocaleString();
                    return;
                }

                let start = null;
                const duration = 1200;

                function tick(timestamp) {
                    start ??= timestamp;
                    const progress = Math.min(1, (timestamp - start) / duration);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    counter.textContent = Math.round(target * eased).toLocaleString();

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    }
                }

                requestAnimationFrame(tick);
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');

                    if (entry.target.hasAttribute('data-about-counter') && !entry.target.dataset.counted) {
                        entry.target.dataset.counted = 'true';
                        animateCounter(entry.target);
                    }

                    observer.unobserve(entry.target);
                });
            }, { threshold: .18 });

            reveals.forEach((element) => observer.observe(element));
            counters.forEach((element) => observer.observe(element));
        })();
    </script>
@endpush
