@extends('layouts.app')

@section('title', 'Driver Test Routes')

@push('styles')
    <style>
        .dtr-home {
            --dtr-bg: #f8f9fa;
            --dtr-bg-soft: #f1f3f5;
            --dtr-panel: rgba(255, 255, 255, .86);
            --dtr-panel-strong: rgba(255, 255, 255, .94);
            --dtr-border: rgba(203, 213, 225, .9);
            --dtr-text: #212529;
            --dtr-muted: #5c6675;
            --dtr-blue-deep: #1e40af;
            --dtr-blue: #2563eb;
            --dtr-indigo: #4338ca;
            --dtr-cyan: #0891b2;
            --dtr-sky: #0284c7;
            min-height: 100vh;
            overflow: hidden;
            background: var(--dtr-bg);
            color: var(--dtr-text);
            font-family: Inter, Instrument Sans, ui-sans-serif, system-ui, sans-serif;
            animation: dtr-page-in .46s cubic-bezier(.16, 1, .3, 1) both;
        }

        .dtr-home * {
            letter-spacing: 0;
        }

        .dtr-section {
            --section-bg: linear-gradient(180deg, rgba(248, 249, 250, .9), rgba(241, 243, 245, .94));
            --section-glow-a: radial-gradient(circle at 14% 18%, rgba(37, 99, 235, .08), transparent 34%);
            --section-glow-b: radial-gradient(circle at 86% 14%, rgba(6, 182, 212, .06), transparent 30%);
            --section-grid-opacity: .13;
            --section-image: none;
            position: relative;
            isolation: isolate;
            overflow: hidden;
            content-visibility: auto;
            contain-intrinsic-size: 900px;
            background-color: var(--dtr-bg);
            background-image:
                var(--section-bg),
                linear-gradient(90deg, rgba(248, 249, 250, .58), rgba(255, 255, 255, .28), rgba(241, 243, 245, .64));
            background-position: center, center;
            background-repeat: no-repeat;
            background-size: auto, auto;
        }

        .dtr-section::before,
        .dtr-section::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .dtr-section::before {
            background: var(--section-glow-a), var(--section-glow-b);
        }

        .dtr-section::after {
            opacity: var(--section-grid-opacity);
            background-image:
                linear-gradient(rgba(148, 163, 184, .16) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, .16) 1px, transparent 1px);
            background-size: 68px 68px;
            mask-image: linear-gradient(180deg, transparent 0, #000 18%, #000 78%, transparent 100%);
        }

        .dtr-section > * {
            position: relative;
            z-index: 1;
        }

        .dtr-section--dashboard {
            --section-bg:
                linear-gradient(160deg, rgba(248, 249, 250, .94) 0%, rgba(255, 255, 255, .82) 48%, rgba(241, 243, 245, .92) 100%);
            --section-glow-a: radial-gradient(ellipse at 12% 18%, rgba(37, 99, 235, .11), transparent 36%);
            --section-glow-b: radial-gradient(ellipse at 88% 20%, rgba(34, 211, 238, .08), transparent 34%);
            --section-grid-opacity: .16;
        }

        .dtr-section--workflow {
            --section-bg:
                linear-gradient(135deg, rgba(248, 249, 250, .94) 0%, rgba(255, 255, 255, .84) 44%, rgba(241, 243, 245, .92) 100%);
            --section-glow-a: radial-gradient(circle at 50% 0%, rgba(56, 189, 248, .08), transparent 32%);
            --section-glow-b: linear-gradient(115deg, transparent 0%, rgba(30, 64, 175, .07) 42%, transparent 72%);
            --section-grid-opacity: .1;
        }

        .dtr-section--routes {
            --section-image: var(--public-image-route);
            --section-bg:
                linear-gradient(180deg, rgba(248, 249, 250, .92) 0%, rgba(255, 255, 255, .82) 52%, rgba(241, 243, 245, .94) 100%);
            --section-glow-a: radial-gradient(ellipse at 8% 46%, rgba(59, 130, 246, .09), transparent 36%);
            --section-glow-b: radial-gradient(ellipse at 92% 58%, rgba(6, 182, 212, .08), transparent 34%);
            --section-grid-opacity: .14;
        }

        .dtr-section--cta {
            --section-bg:
                linear-gradient(145deg, rgba(248, 249, 250, .94) 0%, rgba(255, 255, 255, .84) 52%, rgba(241, 243, 245, .94) 100%);
            --section-glow-a: radial-gradient(circle at 24% 38%, rgba(37, 99, 235, .1), transparent 34%);
            --section-glow-b: radial-gradient(circle at 78% 44%, rgba(34, 211, 238, .08), transparent 32%);
            --section-grid-opacity: .08;
        }

        .dtr-hero {
            position: relative;
            isolation: isolate;
            min-height: 80vh;
            background-color: #0f172a;
        }

        /* Override light theme overrides inside dtr-hero */
        .public-light-theme .dtr-hero .text-white {
            color: #ffffff !important;
        }
        .public-light-theme .dtr-hero .text-slate-300 {
            color: #cbd5e1 !important;
        }
        .public-light-theme .dtr-hero .text-slate-400 {
            color: #94a3b8 !important;
        }
        .public-light-theme .dtr-hero .dtr-kpi-card--dark dt {
            color: #cbd5e1 !important;
        }
        .public-light-theme .dtr-hero .dtr-kpi-card--dark dd {
            color: #ffffff !important;
        }

        /* Enforce header transparency at the top of the Homepage */
        .public-light-theme #public-header[data-transparent-header="true"]:not(.public-header-scrolled) {
            background: transparent !important;
            border-bottom-color: transparent !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        /* Enforce white links when transparent at the top */
        .public-light-theme #public-header:not(.public-header-scrolled) .public-nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .public-light-theme #public-header:not(.public-header-scrolled) .public-nav-link:hover,
        .public-light-theme #public-header:not(.public-header-scrolled) .public-nav-active {
            color: #ffffff !important;
        }

        /* Style Log In button when transparent */
        .public-light-theme #public-header:not(.public-header-scrolled) .public-auth-link {
            border-color: rgba(255, 255, 255, 0.3) !important;
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.1) !important;
            box-shadow: none !important;
        }
        .public-light-theme #public-header:not(.public-header-scrolled) .public-auth-link:hover {
            border-color: rgba(255, 255, 255, 0.6) !important;
            background: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
        }

        /* Style Mobile Hamburger toggle when transparent */
        .public-light-theme #public-header:not(.public-header-scrolled) .public-menu-button {
            border-color: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.1) !important;
            box-shadow: none !important;
        }
        .public-light-theme #public-header:not(.public-header-scrolled) .public-menu-button:hover {
            border-color: rgba(255, 255, 255, 0.5) !important;
            background: rgba(255, 255, 255, 0.2) !important;
        }

        /* Logo visibility enhancement against dark hero video */
        .public-light-theme #public-header:not(.public-header-scrolled) .public-brand img {
            filter: drop-shadow(0 0 1px rgba(255, 255, 255, 0.8)) drop-shadow(0 1px 2px rgba(255, 255, 255, 0.5));
        }

        /* Enforce bright blue-cyan gradient logo text when transparent at the top of homepage */
        .public-light-theme #public-header:not(.public-header-scrolled) .dtr-logo-text {
            background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 52%, #22d3ee 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            text-fill-color: transparent !important;
        }

        /* Enforce bright cyan color for logo subtitle when transparent at the top of homepage */
        .public-light-theme #public-header:not(.public-header-scrolled) .dtr-logo-subtitle {
            color: #22d3ee !important;
            opacity: 0.9 !important;
        }

        .dtr-aurora {
            position: absolute;
            inset: -18% -12% -10%;
            z-index: -3;
            opacity: .28;
            filter: blur(40px) saturate(1.15);
            background:
                conic-gradient(from 130deg at 48% 44%, rgba(37, 99, 235, .20), rgba(6, 182, 212, .18), rgba(99, 102, 241, .14), rgba(241, 243, 245, .18), rgba(37, 99, 235, .20)),
                linear-gradient(115deg, rgba(6, 182, 212, .12), transparent 34%, rgba(37, 99, 235, .15) 58%, transparent 82%, rgba(56, 189, 248, .10));
            animation: dtr-aurora 24s cubic-bezier(.45, 0, .2, 1) infinite alternate;
        }

        .dtr-hero::before,
        .dtr-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .dtr-hero::before {
            z-index: -2;
            opacity: .24;
            background-image:
                linear-gradient(rgba(148, 163, 184, .18) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, .18) 1px, transparent 1px);
            background-size: 76px 76px;
            mask-image: linear-gradient(180deg, transparent 0, #000 20%, #000 72%, transparent 100%);
            animation: dtr-grid-drift 34s linear infinite;
        }

        .dtr-hero::after {
            z-index: -1;
            opacity: .25;
            background-image: radial-gradient(circle, rgba(37, 99, 235, .18) 1px, transparent 1.5px);
            background-size: 86px 86px;
            mask-image: linear-gradient(180deg, #000 0, transparent 78%);
            animation: dtr-particle-drift 42s linear infinite;
        }

        .dtr-gradient-text {
            display: inline-block;
            color: transparent;
            background: linear-gradient(100deg, #1e40af 0%, #2563eb 42%, #0891b2 72%, #1e40af 100%);
            background-size: 220% auto;
            -webkit-background-clip: text;
            background-clip: text;
            animation: dtr-gradient-shift 7s ease-in-out infinite;
        }

        .dtr-kicker {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            border: 1px solid rgba(37, 99, 235, .24);
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.95);
            padding: .4rem .9rem;
            color: #2563eb;
            font-size: .72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .dtr-btn {
            display: inline-flex;
            min-height: 2.9rem;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            border-radius: .5rem;
            padding: .78rem 1.05rem;
            font-weight: 800;
            line-height: 1;
            transition:
                transform 280ms cubic-bezier(.16, 1, .3, 1),
                box-shadow 280ms cubic-bezier(.16, 1, .3, 1),
                border-color 280ms ease-out,
                background 280ms ease-out;
            will-change: transform;
        }

        .dtr-btn:hover {
            transform: translateY(-2px) scale(1.02);
        }

        .dtr-btn:focus-visible,
        .dtr-dashboard-tab:focus-visible,
        .dtr-flip-toggle:focus-visible {
            outline: 2px solid rgba(103, 232, 249, .8);
            outline-offset: 3px;
        }

        .dtr-btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--dtr-blue-deep), var(--dtr-blue) 48%, var(--dtr-cyan));
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
        }

        .dtr-btn-primary:hover {
            box-shadow: 0 16px 34px rgba(37, 99, 235, .28);
        }

        .dtr-btn-secondary {
            border: 1px solid rgba(37, 99, 235, .24);
            color: #1d4ed8;
            background: rgba(255, 255, 255, .86);
            backdrop-filter: blur(16px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        }

        .dtr-btn-secondary:hover {
            border-color: rgba(37, 99, 235, .34);
            background: #eff6ff;
            box-shadow: 0 10px 24px rgba(37, 99, 235, .12);
        }

        .dtr-btn-secondary-light {
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .dtr-btn-secondary-light:hover {
            border-color: rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25);
        }

        .dtr-gradient-text-light {
            display: inline-block;
            color: transparent;
            background: linear-gradient(100deg, #38bdf8 0%, #22d3ee 45%, #a5b4fc 80%, #38bdf8 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            animation: dtr-gradient-shift 7s ease-in-out infinite;
        }

        .dtr-kpi-card--dark {
            background: rgba(15, 23, 42, 0.4) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
            transition: all 280ms cubic-bezier(.16, 1, .3, 1) !important;
        }

        .dtr-kpi-card--dark::before {
            background: rgba(255, 255, 255, 0.15) !important;
        }

        .dtr-kpi-card--dark:hover {
            transform: translateY(-5px) !important;
            background: rgba(15, 23, 42, 0.65) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.3) !important;
        }

        .dtr-city-combobox {
            position: relative;
            max-width: 42rem;
            width: 100%;
        }

        .dtr-city-input-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: .75rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.92);
            padding: .5rem;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            backdrop-filter: blur(16px);
            transition: border-color 220ms ease-out, box-shadow 220ms ease-out;
        }

        .dtr-city-combobox.is-open .dtr-city-input-wrap,
        .dtr-city-input-wrap:focus-within {
            border-color: rgba(37, 99, 235, 0.5);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .dtr-city-input {
            min-width: 0;
            border: 0;
            background: transparent;
            padding: .7rem .8rem .7rem 0;
            color: #1e293b;
            font-weight: 800;
            outline: 0;
            cursor: pointer;
        }

        .dtr-city-input::placeholder {
            color: #64748b;
        }

        .dtr-city-panel {
            position: absolute;
            right: 0;
            left: 0;
            z-index: 20;
            margin-top: .55rem;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-6px);
            transition: opacity 180ms ease-out, transform 180ms cubic-bezier(.16, 1, .3, 1);
            backdrop-filter: blur(18px);
        }

        .dtr-city-combobox.is-open .dtr-city-panel {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .dtr-city-option {
            display: block;
            width: 100%;
            border: 0;
            border-radius: 0.375rem;
            background: transparent;
            padding: .75rem 1rem;
            text-align: left;
            transition: background 180ms ease-out, transform 180ms cubic-bezier(.16, 1, .3, 1);
            color: #1e293b;
        }

        .dtr-city-option:hover,
        .dtr-city-option:focus-visible {
            background: #eff6ff;
            outline: 0;
            transform: translateX(2px);
        }

        .dtr-icon {
            height: 1.1rem;
            width: 1.1rem;
            flex: 0 0 auto;
        }

        .dtr-glass {
            border: 1px solid var(--dtr-border);
            background: var(--dtr-panel);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
        }

        .dtr-gradient-border {
            border: 1px solid transparent;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .94), rgba(248, 249, 250, .9)) padding-box,
                linear-gradient(135deg, rgba(37, 99, 235, .28), rgba(8, 145, 178, .18) 42%, rgba(203, 213, 225, .8) 78%) border-box;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(14px);
            transition: border-color 260ms ease-out, box-shadow 260ms ease-out, transform 260ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-gradient-border:hover {
            box-shadow: 0 14px 32px rgba(15, 23, 42, .12);
        }

        .dtr-tilt-panel {
            perspective: 1300px;
        }

        .dtr-tilt-inner {
            --tilt-x: 0deg;
            --tilt-y: 0deg;
            transform: rotateX(var(--tilt-x)) rotateY(var(--tilt-y)) translate3d(0, var(--tilt-z, 0), 0);
            transform-style: preserve-3d;
            transition: transform 320ms cubic-bezier(.16, 1, .3, 1), box-shadow 320ms ease-out;
            will-change: transform;
        }

        .dtr-tilt-panel:hover .dtr-tilt-inner {
            box-shadow:
                0 18px 44px rgba(15, 23, 42, .12),
                0 8px 22px rgba(37, 99, 235, .1);
        }

        .dtr-float {
            animation: dtr-float 7s ease-in-out infinite;
        }

        .dtr-dashboard-preview {
            overflow: hidden;
            border-radius: .5rem;
            background-color: #ffffff;
        }

        .dtr-route-map {
            position: relative;
            overflow: hidden;
            border-radius: .5rem;
            background-color: #f1f3f5;
            background-image:
                linear-gradient(135deg, rgba(255, 255, 255, .44), rgba(248, 249, 250, .2)),
                linear-gradient(90deg, rgba(248, 249, 250, .58), rgba(255, 255, 255, .28), rgba(241, 243, 245, .64)),
                var(--public-image-route);
            background-position: center, center, center;
            background-repeat: no-repeat;
            background-size: auto, auto, cover;
        }

        .dtr-route-map::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: .2;
            background-image:
                linear-gradient(rgba(148, 163, 184, .18) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, .18) 1px, transparent 1px);
            background-size: 38px 38px;
            animation: none;
        }

        /* High-fidelity responsive video dashboard mockup, 3D tilt, and ambient glows */
        .dtr-blur-blob {
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
            filter: blur(100px);
            opacity: 0.6;
            z-index: 1;
        }
        
        .dtr-blur-blob--blue {
            background: radial-gradient(circle, rgba(59, 130, 246, 0.32) 0%, rgba(37, 99, 235, 0.05) 70%, transparent 100%);
        }
        
        .dtr-blur-blob--cyan {
            background: radial-gradient(circle, rgba(6, 182, 212, 0.28) 0%, rgba(8, 145, 178, 0.05) 70%, transparent 100%);
        }

        .dtr-blur-blob--indigo {
            background: radial-gradient(circle, rgba(99, 102, 241, 0.24) 0%, rgba(79, 70, 229, 0.05) 70%, transparent 100%);
        }

        .dtr-perspective-wrap {
            perspective: 1200px;
            width: 100%;
        }

        .dtr-3d-mockup {
            transform: rotateY(-10deg) rotateX(6deg) rotateZ(1deg);
            transform-style: preserve-3d;
            transition: all 600ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-perspective-wrap:hover .dtr-3d-mockup {
            transform: rotateY(-1deg) rotateX(2deg) rotateZ(0deg) translateY(-8px);
        }

        .dtr-video-glow-wrap {
            position: relative;
            padding: 1px;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.3));
            box-shadow: 
                0 30px 60px -15px rgba(15, 23, 42, 0.15),
                0 12px 24px -10px rgba(15, 23, 42, 0.08),
                0 0 50px -5px rgba(37, 99, 235, 0.04);
            transition: box-shadow 600ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-perspective-wrap:hover .dtr-video-glow-wrap {
            box-shadow: 
                0 45px 90px -15px rgba(15, 23, 42, 0.25),
                0 20px 35px -10px rgba(15, 23, 42, 0.12),
                0 0 70px 0 rgba(37, 99, 235, 0.18);
        }

        .dtr-video-mockup {
            position: relative;
            border-radius: 1rem;
            background: #0f172a;
            overflow: hidden;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .dtr-mockup-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1e293b;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.7rem 1.1rem;
            border-top-left-radius: 1.2rem;
            border-top-right-radius: 1.2rem;
        }

        .dtr-mockup-dots {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .dtr-mockup-dot {
            width: 0.65rem;
            height: 0.65rem;
            border-radius: 9999px;
        }

        .dtr-mockup-dot--red { background-color: #ef4444; }
        .dtr-mockup-dot--yellow { background-color: #f59e0b; }
        .dtr-mockup-dot--green { background-color: #10b981; }

        .dtr-mockup-address {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.375rem;
            padding: 0.22rem 0.8rem;
            font-family: monospace;
            font-size: 0.65rem;
            color: #cbd5e1;
            max-width: 15rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dtr-mockup-address svg {
            width: 0.75rem;
            height: 0.75rem;
            color: #38bdf8;
            flex-shrink: 0;
        }

        .dtr-mockup-body {
            position: relative;
            aspect-ratio: 16 / 12.5;
            background-color: #0f172a;
            border-bottom-left-radius: 1.2rem;
            border-bottom-right-radius: 1.2rem;
            overflow: hidden;
        }

        .dtr-mockup-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .dtr-mockup-overlay-hud {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.1rem;
            pointer-events: none;
            z-index: 10;
        }

        .dtr-hud-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
        }

        .dtr-hud-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(16px) saturate(1.2);
            -webkit-backdrop-filter: blur(16px) saturate(1.2);
            border-radius: 0.75rem;
            padding: 0.65rem 0.95rem;
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.5);
            pointer-events: auto;
            transition: all 250ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-hud-card:hover {
            transform: translateY(-2px) scale(1.04);
            background: rgba(15, 23, 42, 0.85);
            border-color: rgba(255, 255, 255, 0.35);
            box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.6);
        }

        .dtr-hud-card-title {
            font-size: 0.55rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #38bdf8;
        }

        .dtr-hud-card-value {
            font-size: 0.75rem;
            font-weight: 900;
            color: #ffffff;
            margin-top: 0.1rem;
        }

        .dtr-hud-badge-live {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 2rem;
            padding: 0.35rem 0.65rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15);
        }

        .dtr-hud-dot-pulse {
            width: 0.45rem;
            height: 0.45rem;
            background-color: #10b981;
            border-radius: 9999px;
            position: relative;
        }

        .dtr-hud-dot-pulse::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 9999px;
            background-color: #10b981;
            opacity: 0.6;
            animation: dtr-pulse-live 1.8s infinite ease-out;
        }

        .dtr-hud-badge-text {
            font-size: 0.65rem;
            font-weight: 900;
            color: #34d399;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .dtr-hud-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.75) 100%);
            margin: -1.1rem;
            padding: 1.6rem 1.1rem 1.1rem 1.1rem;
            border-bottom-left-radius: 1.2rem;
            border-bottom-right-radius: 1.2rem;
        }

        .dtr-hud-status {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .dtr-hud-status-text {
            font-size: 0.65rem;
            font-weight: 800;
            color: #e2e8f0;
        }

        .dtr-hud-metric {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.65rem;
            font-weight: 800;
            color: #ffffff;
        }

        @keyframes dtr-pulse-live {
            0% { transform: scale(1); opacity: 0.6; }
            100% { transform: scale(2.4); opacity: 0; }
        }

        /* Light theme text adjustments inside the HUD (force text readability) */
        .public-light-theme .dtr-video-mockup :is(.dtr-hud-card-value, .dtr-hud-status-text, .dtr-hud-metric) {
            color: #ffffff !important;
        }
        
        .public-light-theme .dtr-video-mockup .dtr-hud-card-title {
            color: #38bdf8 !important;
        }

        /* KPI Premium Stats Cards Styling */
        .dtr-kpi-card {
            position: relative;
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 
                0 4px 20px -2px rgba(15, 23, 42, 0.04),
                0 2px 4px -1px rgba(15, 23, 42, 0.02);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: all 350ms cubic-bezier(.16, 1, .3, 1);
            overflow: hidden;
        }

        .dtr-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: var(--kpi-accent, #2563eb);
            border-top-left-radius: 0.75rem;
            border-bottom-left-radius: 0.75rem;
        }

        .dtr-kpi-card:hover {
            transform: translateY(-5px);
            background: #ffffff;
            border-color: rgba(37, 99, 235, 0.2);
            box-shadow: 
                0 20px 30px -10px rgba(15, 23, 42, 0.08),
                0 10px 15px -5px rgba(15, 23, 42, 0.03);
        }

        .dtr-kpi-card--blue { --kpi-accent: #2563eb; }
        .dtr-kpi-card--cyan { --kpi-accent: #0891b2; }
        .dtr-kpi-card--indigo { --kpi-accent: #4f46e5; }

        .dtr-kpi-icon-wrap {
            transition: transform 350ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-kpi-card:hover .dtr-kpi-icon-wrap {
            transform: scale(1.12) rotate(3deg);
        }

        .dtr-map-route {
            stroke-dasharray: 520;
            stroke-dashoffset: 520;
            animation: dtr-route-draw 3.4s cubic-bezier(.16, 1, .3, 1) 1s forwards;
        }

        .dtr-meter-ring {
            stroke-dasharray: 188;
            stroke-dashoffset: 188;
            animation: dtr-meter 3.2s cubic-bezier(.16, 1, .3, 1) 1.1s forwards;
        }

        .dtr-pulse-dot {
            animation: dtr-status-pulse 2.2s ease-in-out infinite;
        }

        [data-reveal] {
            opacity: 0;
            transform: translate3d(0, 28px, 0);
            transition:
                opacity 680ms cubic-bezier(.16, 1, .3, 1),
                transform 680ms cubic-bezier(.16, 1, .3, 1);
            transition-delay: var(--delay, 0ms);
        }

        [data-reveal="slide-left"] {
            transform: translate3d(-26px, 0, 0);
        }

        [data-reveal="slide-right"] {
            transform: translate3d(26px, 0, 0);
        }

        [data-reveal].is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        .dtr-stat-card {
            min-height: 11.8rem;
            border-radius: .5rem;
            padding: 1.05rem;
        }

        .dtr-mini-chart path,
        .dtr-mini-chart polyline {
            stroke-dasharray: 180;
            stroke-dashoffset: 180;
            transition: stroke-dashoffset 1150ms cubic-bezier(.16, 1, .3, 1);
            transition-delay: var(--delay, 0ms);
        }

        .is-visible .dtr-mini-chart path,
        .is-visible .dtr-mini-chart polyline {
            stroke-dashoffset: 0;
        }

        .dtr-dashboard-shell {
            overflow: hidden;
            border-radius: .5rem;
            background-color: #ffffff;
        }

        .dtr-dashboard-tab {
            position: relative;
            z-index: 1;
            display: flex;
            height: 2.65rem;
            width: 100%;
            align-items: center;
            gap: .65rem;
            border-radius: .375rem;
            padding: 0 .75rem;
            color: #6b7280;
            font-size: .875rem;
            font-weight: 800;
            transition: color 260ms ease-out, transform 260ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-dashboard-tab:hover,
        .dtr-dashboard-tab.is-active {
            color: var(--dtr-text);
            transform: translateX(2px);
        }

        .dtr-dashboard-indicator {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 2.65rem;
            border-radius: .375rem;
            background: linear-gradient(135deg, rgba(30, 58, 138, .92), rgba(6, 182, 212, .72));
            box-shadow: 0 10px 24px rgba(37, 99, 235, .14);
            transition: transform 300ms cubic-bezier(.16, 1, .3, 1), height 300ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-area-line {
            stroke-dasharray: 680;
            stroke-dashoffset: 680;
            transition: stroke-dashoffset 1350ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-area-fill {
            opacity: 0;
            transition: opacity 900ms ease-out 240ms;
        }

        .is-visible .dtr-area-line {
            stroke-dashoffset: 0;
        }

        .is-visible .dtr-area-fill {
            opacity: 1;
        }

        .dtr-bar {
            transform: scaleY(.16);
            transform-origin: bottom;
            transition: transform 780ms cubic-bezier(.16, 1, .3, 1);
            transition-delay: var(--delay, 0ms);
        }

        .is-visible .dtr-bar {
            transform: scaleY(1);
        }

        .dtr-route-row {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) repeat(3, minmax(0, .7fr)) auto;
            gap: 1rem;
            align-items: center;
            border-top: 1px solid #e0e0e0;
            padding: .95rem 1rem;
            transition: background 240ms ease-out, transform 240ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-route-row:hover {
            background: #f1f3f5;
            transform: translateX(3px);
        }

        .dtr-flip-card {
            min-height: 23.5rem;
            perspective: 1200px;
        }

        .dtr-flip-inner {
            position: relative;
            min-height: inherit;
            transform-style: preserve-3d;
            transition: transform 760ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-flip-card:hover .dtr-flip-inner,
        .dtr-flip-card.is-flipped .dtr-flip-inner {
            transform: rotateY(180deg);
        }

        .dtr-flip-face {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: .5rem;
            backface-visibility: hidden;
        }

        .dtr-flip-back {
            transform: rotateY(180deg);
        }

        .dtr-flip-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            border-radius: .375rem;
            border: 1px solid rgba(37, 99, 235, .24);
            background: rgba(255, 255, 255, .86);
            padding: .6rem .8rem;
            color: #1d4ed8;
            font-size: .875rem;
            font-weight: 800;
            transition: transform 240ms cubic-bezier(.16, 1, .3, 1), background 240ms ease-out;
        }

        .dtr-flip-toggle:hover {
            transform: translateY(-1px);
            background: #eff6ff;
        }

        .dtr-badge {
            display: inline-flex;
            align-items: center;
            border-radius: .375rem;
            border: 1px solid rgba(37, 99, 235, .2);
            background: #eff6ff;
            padding: .35rem .55rem;
            color: #0e7490;
            font-size: .72rem;
            font-weight: 800;
        }

        .dtr-callout {
            border-radius: .5rem;
            background:
                linear-gradient(135deg, rgba(37, 99, 235, .1), rgba(37, 99, 235, .08) 46%, rgba(6, 182, 212, .06)),
                rgba(255, 255, 255, .92);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .12);
        }

        @keyframes dtr-page-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes dtr-aurora {
            0% { transform: translate3d(-2%, -1%, 0) scale(1.02) rotate(0deg); }
            100% { transform: translate3d(2%, 2%, 0) scale(1.08) rotate(5deg); }
        }

        @keyframes dtr-grid-drift {
            from { background-position: 0 0, 0 0; }
            to { background-position: 76px 76px, 76px 76px; }
        }

        @keyframes dtr-particle-drift {
            from { background-position: 0 0; }
            to { background-position: 86px 172px; }
        }

        @keyframes dtr-gradient-shift {
            0%, 100% { background-position: 0% center; }
            50% { background-position: 100% center; }
        }

        @keyframes dtr-float {
            0%, 100% { transform: translate3d(0, 0, 0); }
            50% { transform: translate3d(0, -14px, 0); }
        }

        @keyframes dtr-route-draw {
            to { stroke-dashoffset: 0; }
        }

        @keyframes dtr-meter {
            to { stroke-dashoffset: 42; }
        }

        @keyframes dtr-status-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34, 211, 238, .32); }
            50% { box-shadow: 0 0 0 10px rgba(34, 211, 238, 0); }
        }

        /* Dynamic Background Shapes & Geometry Animations for Home Sections */
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
            50% { transform: translateY(-18px) rotate(4deg); }
        }

        @keyframes dtr-float-reverse {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(16px) rotate(-5deg); }
        }

        @keyframes dtr-pulse-glow {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 0.95; transform: scale(1.1); }
        }

        @keyframes dtr-dash-flow {
            from { stroke-dashoffset: 200; }
            to { stroke-dashoffset: 0; }
        }

        /* Shape Container & Elements */
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
            filter: blur(55px);
        }

        .dtr-shape-diamond {
            position: absolute;
            border: 2px solid rgba(56, 189, 248, 0.5);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.18), rgba(6, 182, 212, 0.14));
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            transform: rotate(45deg);
            pointer-events: none;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.25);
        }

        .dtr-shape-pill {
            position: absolute;
            border: 2px solid rgba(56, 189, 248, 0.4);
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.16), rgba(99, 102, 241, 0.16));
            border-radius: 9999px;
            pointer-events: none;
            box-shadow: 0 8px 24px rgba(6, 182, 212, 0.2);
        }

        .dtr-shape-grid-dots {
            position: absolute;
            background-image: radial-gradient(circle, rgba(56, 189, 248, 0.65) 2.5px, transparent 2.5px);
            background-size: 26px 26px;
            pointer-events: none;
            mask-image: radial-gradient(circle at center, black 40%, transparent 85%);
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 85%);
        }

        .dtr-shape-hex-pattern {
            position: absolute;
            opacity: 0.22;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0l25.98 15v30L30 60 4.02 45V15z' fill-opacity='0' stroke='%2338bdf8' stroke-width='1.75'/%3E%3C/svg%3E");
            background-size: 60px 60px;
            pointer-events: none;
        }

        @media (max-width: 900px) {
            .dtr-route-row {
                grid-template-columns: 1fr;
                gap: .55rem;
            }
        }

        @media (hover: none) {
            .dtr-flip-card:hover .dtr-flip-inner {
                transform: none;
            }

            .dtr-flip-card.is-flipped .dtr-flip-inner {
                transform: rotateY(180deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .dtr-home,
            .dtr-aurora,
            .dtr-hero::before,
            .dtr-hero::after,
            .dtr-gradient-text,
            .dtr-float,
            .dtr-route-map::before,
            .dtr-map-route,
            .dtr-meter-ring,
            .dtr-pulse-dot,
            .dtr-mini-chart path,
            .dtr-mini-chart polyline,
            .dtr-area-line,
            .dtr-area-fill,
            .dtr-bar,
            .dtr-city-panel,
            .dtr-city-option,
            [data-reveal],
            .dtr-tilt-inner,
            .dtr-flip-inner {
                animation: none !important;
                transition: none !important;
            }

            [data-reveal] {
                opacity: 1;
                transform: none;
            }
        }

        /* 3D Animated Layout & Interactive Component Styles */
        .dtr-3d-grid {
            perspective: 1200px;
        }

        .dtr-3d-card-v2 {
            position: relative;
            border-radius: 1.25rem;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: transform 400ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 400ms cubic-bezier(0.16, 1, 0.3, 1), border-color 300ms ease;
            transform-style: preserve-3d;
            will-change: transform, box-shadow;
        }

        .dtr-3d-card-v2:hover {
            transform: translateY(-8px) rotateX(4deg) rotateY(-2deg);
            box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.22), 0 10px 20px -5px rgba(6, 182, 212, 0.15);
            border-color: rgba(37, 99, 235, 0.4);
        }

        .dtr-3d-dark-glow {
            position: relative;
            border-radius: 1.25rem;
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            overflow: hidden;
            transition: transform 400ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 400ms cubic-bezier(0.16, 1, 0.3, 1), border-color 300ms ease;
        }

        .dtr-3d-dark-glow:hover {
            transform: translateY(-6px) scale(1.01);
            border-color: rgba(56, 189, 248, 0.4);
            box-shadow: 0 30px 70px -15px rgba(37, 99, 235, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .dtr-step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #1e40af, #2563eb 50%, #0891b2);
            color: #ffffff !important;
            font-size: 1.15rem;
            font-weight: 900;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
            transition: transform 350ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dtr-3d-card-v2:hover .dtr-step-badge {
            transform: scale(1.12) rotate(6deg);
        }

        .dtr-faq-box {
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: all 300ms ease;
            overflow: hidden;
        }

        .dtr-faq-box.is-open {
            border-color: rgba(37, 99, 235, 0.4);
            background: #ffffff;
            box-shadow: 0 16px 36px -10px rgba(37, 99, 235, 0.14);
        }

        .dtr-faq-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.6rem;
            text-align: left;
            font-size: 1.05rem;
            font-weight: 800;
            color: #1e293b;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .dtr-faq-chevron {
            width: 1.35rem;
            height: 1.35rem;
            color: #2563eb;
            flex-shrink: 0;
            transition: transform 300ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dtr-faq-box.is-open .dtr-faq-chevron {
            transform: rotate(180deg);
        }

        .dtr-faq-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 350ms cubic-bezier(0.16, 1, 0.3, 1), padding 350ms ease;
            padding: 0 1.6rem;
        }

        .dtr-faq-box.is-open .dtr-faq-body {
            max-height: 350px;
            padding: 0 1.6rem 1.4rem 1.6rem;
        }

        .dtr-check-item {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 250ms ease;
        }

        .dtr-check-item:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(56, 189, 248, 0.3);
            transform: translateX(4px);
        }

        .dtr-check-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.6rem;
            height: 1.6rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff !important;
            flex-shrink: 0;
            margin-top: 0.1rem;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        /* Light theme readability overrides for dark elements */
        .public-light-theme .dtr-3d-dark-glow :is(.text-white, h2, h3, h4, p, dt, dd, span, button, a) {
            color: #ffffff !important;
        }
        .public-light-theme .dtr-3d-dark-glow .text-slate-300 {
            color: #cbd5e1 !important;
        }
        .public-light-theme .dtr-3d-dark-glow .text-slate-400 {
            color: #94a3b8 !important;
        }
        .public-light-theme .dtr-3d-dark-glow .text-cyan-400 {
            color: #38bdf8 !important;
        }
        .public-light-theme .dtr-3d-dark-glow .text-emerald-400 {
            color: #34d399 !important;
        }
    </style>
@endpush

@section('content')
    @php
        $routeCount = (int) ($stats['routes'] ?? 0);
        $cityCount = (int) ($stats['cities'] ?? 0);
        $startCount = (int) ($stats['starts'] ?? 0);
        $practiceScore = min(98, 82 + min(16, $routeCount));
    @endphp

    <div class="dtr-home">
        <section class="dtr-hero relative overflow-hidden flex items-center min-h-[92vh]">
            <div class="dtr-aurora"></div>

            <!-- Full Width Background Video -->
            <div class="absolute inset-0 z-0 overflow-hidden">
                <video 
                    class="w-full h-full object-cover" 
                    src="{{ asset('images/hero.mp4') }}" 
                    autoplay 
                    muted 
                    loop 
                    playsinline
                    poster="{{ asset('images/home-hero.jpeg') }}"
                >
                    Your browser does not support the video tag.
                </video>
                <!-- Dark Gradient Overlays (Full Width - Reduced Opacity) -->
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/65 to-slate-950/15"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/30 via-transparent to-slate-950/60"></div>
                <!-- Subtle Grid Mask -->
                <div class="absolute inset-0 opacity-[0.06] bg-[linear-gradient(rgba(255,255,255,0.07)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.07)_1px,transparent_1px)] bg-[size:30px_30px]"></div>
            </div>

            <!-- Floating Ambient Blur Blobs (Behind Text but in front of Video) -->
            <div class="dtr-blur-blob dtr-blur-blob--blue top-[15%] left-[5%] w-[320px] h-[320px] opacity-30" style="z-index: 1;"></div>
            <div class="dtr-blur-blob dtr-blur-blob--cyan top-[35%] right-[5%] w-[420px] h-[420px] opacity-25" style="z-index: 1;"></div>

            <!-- Content Container (Safe Padded under Header) -->
            <div class="relative z-10 mx-auto max-w-7xl w-full px-4 pt-36 pb-16 sm:px-6 sm:pt-40 lg:px-8 lg:pt-48 lg:pb-24">
                <div class="max-w-3xl w-full" data-reveal>
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-cyan-500/30 bg-cyan-950/70 px-4 py-1.5 text-xs font-extrabold uppercase tracking-wider text-cyan-400 backdrop-blur-md">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <path d="M4 19V5" />
                            <path d="M4 7c2.5-2 5-2 7.5 0s5 2 8.5 0v10c-3.5 2-6 2-8.5 0s-5-2-7.5 0" />
                        </svg>
                        Paid route practice platform
                    </span>

                    <h1 class="mt-6 text-4xl font-extrabold tracking-tight leading-[1.1] text-white sm:text-5xl lg:text-6xl">
                        Driver Test Routes 2026
                        <span class="dtr-gradient-text-light block mt-1">practice with precision.</span>
                    </h1>

                    <p class="mt-6 text-sm leading-7 text-slate-300 sm:text-base font-medium">
                        Pass Your Ontario G2 & G Road Test on the 1st Try — In Your Own Car.Turn your phone into your personal driving guide. Navigate live 2026 G & G2 Drive Test routes, download official examiner marking sheets, and practice with total confidence — no expensive driving school car required
                    </p>

                    @if($cities->isNotEmpty())
                        <div class="dtr-city-combobox mt-8" data-routes-search-combobox>
                            <div class="dtr-city-input-wrap">
                                <div class="flex items-center gap-2.5 flex-1 pl-3">
                                    <!-- Search Icon -->
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input
                                        type="text"
                                        id="routes-search-input"
                                        class="dtr-city-input flex-1 pl-0 py-2.5"
                                        placeholder="Select package, city & route..."
                                        readonly
                                        autocomplete="off"
                                    >
                                </div>
                                <a href="{{ route('driving-routes.index') }}" id="routes-search-btn" class="dtr-btn dtr-btn-primary min-h-11 px-5 py-3 flex items-center justify-center gap-2 rounded-lg">
                                    <span>Routes</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>

                            <div id="routes-search-panel" class="dtr-city-panel w-full" style="max-height: none;">
                                <!-- Phase 1: Select Package -->
                                <div id="phase-package" class="p-5">
                                    <span class="block text-xs uppercase tracking-wider font-extrabold text-cyan-600 mb-3">Step 1: Choose Package Type</span>
                                    <div class="grid grid-cols-2 gap-4">
                                        <button type="button" data-select-pkg="g1" class="group relative overflow-hidden rounded-lg border border-slate-200 bg-slate-50 p-4 text-left transition-all duration-200 hover:bg-slate-100 hover:border-cyan-500/50 focus:outline-none">
                                            <span class="block text-lg font-black text-slate-800 group-hover:text-cyan-600">G2 Test Routes</span>
                                            <span class="mt-1 block text-xs font-bold text-slate-500">For G2 exit road test prep</span>
                                        </button>
                                        <button type="button" data-select-pkg="g2" class="group relative overflow-hidden rounded-lg border border-slate-200 bg-slate-50 p-4 text-left transition-all duration-200 hover:bg-slate-100 hover:border-cyan-500/50 focus:outline-none">
                                            <span class="block text-lg font-black text-slate-800 group-hover:text-cyan-600">G Test Routes</span>
                                            <span class="mt-1 block text-xs font-bold text-slate-500">For G exit road test prep</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Phase 2: Select City -->
                                <div id="phase-city" class="hidden p-5">
                                    <div class="mb-4 flex items-center justify-between border-b border-slate-200 pb-3">
                                        <span class="text-xs font-black text-cyan-600">Package: <span class="text-slate-800 font-black" id="badge-pkg">G2 Test Routes</span></span>
                                        <button type="button" data-reset-to="package" class="text-xs text-cyan-600 hover:text-cyan-800 font-extrabold flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                            Back
                                        </button>
                                    </div>
                                    <span class="block text-xs uppercase tracking-wider font-extrabold text-cyan-600 mb-3">Step 2: Select Your City</span>
                                    <div class="relative mb-3">
                                        <input
                                            type="text"
                                            id="city-search-input"
                                            class="w-full rounded-lg border border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 text-sm font-bold focus:border-cyan-500 focus:outline-none transition-colors"
                                            placeholder="Search city..."
                                            autocomplete="off"
                                        >
                                    </div>
                                    <div class="space-y-1 max-h-52 overflow-y-auto" id="city-search-list">
                                        <!-- Cities list populated dynamically -->
                                    </div>
                                </div>

                                <!-- Phase 3: Select Route -->
                                <div id="phase-route" class="hidden p-5">
                                    <div class="mb-4 flex flex-col gap-1.5 border-b border-slate-200 pb-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-black text-cyan-600">Package: <span class="text-slate-800 font-black" id="badge-pkg-2">G2 Test Routes</span></span>
                                            <button type="button" data-reset-to="package" class="text-xs text-cyan-600 hover:text-cyan-800 font-extrabold flex items-center gap-1">
                                                Change Package
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-black text-cyan-600">City: <span class="text-slate-800 font-black" id="badge-city">Clinton</span></span>
                                            <button type="button" data-reset-to="city" class="text-xs text-cyan-600 hover:text-cyan-800 font-extrabold flex items-center gap-1">
                                                Change City
                                            </button>
                                        </div>
                                    </div>
                                    <span class="block text-xs uppercase tracking-wider font-extrabold text-cyan-600 mb-3">Step 3: Choose Available Route</span>
                                    <div class="space-y-1 max-h-52 overflow-y-auto" id="route-search-list">
                                        <!-- Routes list populated dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 flex flex-col gap-3.5 sm:flex-row">
                        <a href="{{ route('driving-routes.index') }}" class="dtr-btn dtr-btn-primary px-6">
                            Browse Routes
                            <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </a>
                        @auth
                            <a href="{{ route('driving-routes.my') }}" class="dtr-btn dtr-btn-secondary-light px-6">
                                <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                    <path d="M8 6h13" />
                                    <path d="M8 12h13" />
                                    <path d="M8 18h13" />
                                    <path d="M3 6h.01" />
                                    <path d="M3 12h.01" />
                                    <path d="M3 18h.01" />
                                </svg>
                                My Routes
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="dtr-btn dtr-btn-secondary-light px-6">
                                <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M19 8v6" />
                                    <path d="M22 11h-6" />
                                </svg>
                                Create Account
                            </a>
                        @endauth
                    </div>

                    <dl class="mt-10 grid max-w-2xl gap-4 grid-cols-3">
                        <!-- Premium KPI Card: Active Routes -->
                        <div class="dtr-kpi-card dtr-kpi-card--dark rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3.5">
                                <span class="dtr-kpi-icon-wrap bg-blue-500/20 text-blue-300 rounded-lg p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold text-blue-300 uppercase tracking-wider bg-blue-950/60 border border-blue-500/30 px-2.5 py-0.5 rounded-full">Active</span>
                            </div>
                            <dt class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Active routes</dt>
                            <dd class="mt-1 text-2xl font-black text-white" data-counter data-target="{{ $routeCount }}">{{ number_format($routeCount) }}</dd>
                        </div>

                        <!-- Premium KPI Card: Cities Covered -->
                        <div class="dtr-kpi-card dtr-kpi-card--dark rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3.5">
                                <span class="dtr-kpi-icon-wrap bg-cyan-500/20 text-cyan-300 rounded-lg p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-10.5h16.5m-16.5 3h16.5m-16.5 3h16.5M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-9h.75m-.75 3h.75m-.75 3h.75m3-9h.75m-.75 3h.75m-.75 3h.75" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold text-cyan-300 uppercase tracking-wider bg-cyan-950/60 border border-cyan-500/30 px-2.5 py-0.5 rounded-full">Cities</span>
                            </div>
                            <dt class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Cities covered</dt>
                            <dd class="mt-1 text-2xl font-black text-white" data-counter data-target="{{ $cityCount }}">{{ number_format($cityCount) }}</dd>
                        </div>

                        <!-- Premium KPI Card: Starts Used -->
                        <div class="dtr-kpi-card dtr-kpi-card--dark rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3.5">
                                <span class="dtr-kpi-icon-wrap bg-indigo-500/20 text-indigo-300 rounded-lg p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653z" />
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider bg-indigo-950/60 border border-indigo-500/30 px-2.5 py-0.5 rounded-full">Sims</span>
                            </div>
                            <dt class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Starts used</dt>
                            <dd class="mt-1 text-2xl font-black text-white" data-counter data-target="{{ $startCount }}">{{ number_format($startCount) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <!-- SECTION 1: Pass Test Value Proposition & Savings Callout -->
        <section class="dtr-section py-20 px-4 sm:px-6 lg:px-8 border-t border-white/10">
            <!-- Background Shape Design Layer -->
            <div class="dtr-bg-shapes">
                <!-- Concentric Rotating Orbital Rings (Top-Left) -->
                <div class="absolute -top-16 -left-16 w-[440px] h-[440px] opacity-80 dtr-spin-slow">
                    <svg viewBox="0 0 400 400" fill="none" class="w-full h-full text-cyan-400">
                        <circle cx="200" cy="200" r="180" stroke="currentColor" stroke-width="2.5" stroke-dasharray="16 12" />
                        <circle cx="200" cy="200" r="130" stroke="rgba(37, 99, 235, 0.7)" stroke-width="2" stroke-dasharray="10 8" />
                        <circle cx="200" cy="200" r="80" stroke="rgba(56, 189, 248, 0.6)" stroke-width="3" />
                        <circle cx="200" cy="20" r="8" fill="#38bdf8" />
                        <circle cx="380" cy="200" r="6" fill="#60a5fa" />
                    </svg>
                </div>

                <!-- Sweeping Curved Road Vector Line (Right background) -->
                <svg class="absolute top-1/2 right-0 -translate-y-1/2 w-full max-w-3xl h-[420px] opacity-75 pointer-events-none" viewBox="0 0 600 400" fill="none">
                    <path d="M50 350 C 200 350, 250 50, 400 50 C 500 50, 550 200, 600 200" stroke="url(#sec1RouteGrad)" stroke-width="9" stroke-linecap="round" class="dtr-dash-flow" />
                    <path d="M50 350 C 200 350, 250 50, 400 50 C 500 50, 550 200, 600 200" stroke="rgba(56, 189, 248, 0.3)" stroke-width="28" stroke-linecap="round" />
                    <circle cx="400" cy="50" r="10" fill="#22d3ee" />
                    <circle cx="50" cy="350" r="10" fill="#3b82f6" />
                    <defs>
                        <linearGradient id="sec1RouteGrad" x1="0%" y1="100%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#3b82f6" />
                            <stop offset="50%" stop-color="#06b6d4" />
                            <stop offset="100%" stop-color="#38bdf8" />
                        </linearGradient>
                    </defs>
                </svg>

                <!-- Floating Ambient Glow Spheres -->
                <div class="dtr-shape-blob top-10 right-10 w-[420px] h-[420px] bg-cyan-500/25 dtr-pulse-glow"></div>
                <div class="dtr-shape-blob bottom-10 left-10 w-[360px] h-[360px] bg-blue-600/25 dtr-pulse-glow" style="animation-delay: 3s;"></div>

                <!-- Floating 3D Diamond Ring Accent -->
                <div class="dtr-shape-diamond bottom-12 right-1/4 w-32 h-32 opacity-90 dtr-float-slow"></div>
            </div>
            <div class="mx-auto max-w-7xl">
                <div class="text-center max-w-4xl mx-auto mb-14" data-reveal>
                    <span class="inline-flex items-center gap-2 rounded-full border border-blue-500/30 bg-blue-950/70 px-4 py-1.5 text-xs font-extrabold uppercase tracking-wider text-cyan-400 backdrop-blur-md">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pass Your Road Test on the 1st Attempt
                    </span>
                    <h2 class="mt-6 text-3xl font-black tracking-tight leading-tight text-white sm:text-5xl lg:text-6xl">
                        Pass Your Ontario G2 & G Road Test on the 1st Try — <span class="dtr-gradient-text-light">In Your Own Car</span>
                    </h2>
                    <p class="mt-6 text-base leading-8 text-slate-300 font-medium max-w-3xl mx-auto sm:text-lg">
                        Turn your phone into your personal driving guide. Navigate live 2026 G & G2 DriveTest routes, download official examiner marking sheets, and practice with total confidence — no expensive driving school car required.
                    </p>
                </div>

                <!-- 3D Banner Callout: Why pay $200+ -->
                <div class="dtr-3d-dark-glow p-8 sm:p-12 relative overflow-hidden" data-reveal data-tilt>
                    <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>
                    <div class="absolute -left-20 -top-20 w-80 h-80 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>
                    
                    <div class="relative z-10 grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                        <div class="space-y-4">
                            <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-500/15 border border-emerald-500/30 px-3.5 py-1 text-xs font-black uppercase tracking-wider text-emerald-400">
                                💰 Save Hundreds on Test Day
                            </div>
                            <h3 class="text-2xl sm:text-4xl font-black text-white leading-snug">
                                Why pay <span class="text-cyan-400 underline decoration-cyan-500/40 underline-offset-8">$200+ to rent a driving school car</span>?
                            </h3>
                            <p class="text-slate-300 text-base leading-relaxed max-w-3xl font-normal">
                                Practice the exact routes around your local DriveTest centre in your own car, master every tricky intersection, and pass your test on the very first attempt without paying exorbitant instructor vehicle rental fees.
                            </p>
                        </div>
                        <div class="flex flex-col sm:flex-row lg:flex-col gap-4 shrink-0">
                            <a href="{{ route('driving-routes.index') }}" class="dtr-btn dtr-btn-primary px-8 py-4 text-base rounded-xl shadow-xl hover:shadow-cyan-500/20">
                                Explore Local Routes
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                            <div class="text-center sm:text-left lg:text-center text-xs text-slate-400 font-bold">
                                ⚡ Instant Mobile GPS Access
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 2: Everything You Need to Pass Your Road Test (3D Animated Grid) -->
        <section class="dtr-section py-20 px-4 sm:px-6 lg:px-8 bg-slate-900/40 border-t border-white/10">
            <!-- Background Shape Design Layer -->
            <div class="dtr-bg-shapes">
                <!-- Floating Dot Matrix Clusters -->
                <div class="dtr-shape-grid-dots top-8 right-12 w-96 h-96 opacity-85"></div>
                <div class="dtr-shape-grid-dots bottom-8 left-12 w-96 h-96 opacity-75"></div>

                <!-- 3D Wireframe Cube/Polygon Left -->
                <div class="absolute top-1/4 left-6 w-52 h-52 opacity-75 dtr-float-slow">
                    <svg viewBox="0 0 200 200" fill="none" class="w-full h-full stroke-cyan-400">
                        <polygon points="100,20 170,60 170,140 100,180 30,140 30,60" stroke-width="2.5" stroke-dasharray="6 4" />
                        <polygon points="100,50 140,75 140,125 100,150 60,125 60,75" stroke-width="2" />
                        <line x1="100" y1="20" x2="100" y2="50" stroke-width="2" />
                        <line x1="170" y1="60" x2="140" y2="75" stroke-width="2" />
                        <line x1="170" y1="140" x2="140" y2="125" stroke-width="2" />
                        <line x1="100" y1="180" x2="100" y2="150" stroke-width="2" />
                        <line x1="30" y1="140" x2="60" y2="125" stroke-width="2" />
                        <line x1="30" y1="60" x2="60" y2="75" stroke-width="2" />
                    </svg>
                </div>

                <!-- 3D Wireframe Polygon Right -->
                <div class="absolute bottom-1/4 right-8 w-48 h-48 opacity-70 dtr-float-reverse">
                    <svg viewBox="0 0 200 200" fill="none" class="w-full h-full stroke-indigo-400">
                        <circle cx="100" cy="100" r="80" stroke-width="2.5" stroke-dasharray="8 6" />
                        <polygon points="100,30 160,140 40,140" stroke-width="2.5" />
                        <polygon points="100,170 160,60 40,60" stroke-width="2" />
                    </svg>
                </div>

                <!-- Glowing Deep Indigo Center Orb -->
                <div class="dtr-shape-blob top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-600/25 dtr-pulse-glow"></div>
            </div>
            <div class="mx-auto max-w-7xl">
                <div class="text-center max-w-3xl mx-auto mb-16" data-reveal>
                    <span class="text-xs font-black uppercase tracking-widest text-cyan-400">Complete Feature Toolkit</span>
                    <h2 class="mt-3 text-3xl font-black text-white sm:text-5xl">Everything You Need to Pass Your Road Test</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300 sm:text-base">Designed specifically for Ontario drivers preparing for G2 and G exit examinations.</p>
                </div>

                <div class="dtr-3d-grid grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between" data-reveal data-tilt style="--delay: 0ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-600 mb-6">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">Latest 2026 DriveTest Routes (G & G2)</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">Access turn-by-turn route maps for every DriveTest location across Ontario, updated for current testing paths.</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-600">
                            <span>All Ontario Locations</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14m-7-7l7 7-7 7"/></svg>
                        </div>
                    </article>

                    <!-- Feature 2 -->
                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between" data-reveal data-tilt style="--delay: 80ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-600 mb-6">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">Turn-by-Turn GPS Phone Navigation</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">Open test routes directly on your phone's GPS map app and drive them as many times as you need before test day.</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-cyan-600">
                            <span>One-Tap Mobile GPS</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14m-7-7l7 7-7"/></svg>
                        </div>
                    </article>

                    <!-- Feature 3 -->
                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between" data-reveal data-tilt style="--delay: 160ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-600 mb-6">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">Official Examiner Marking Sheets</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">See the exact checklist examiners use to score your turns, parallel parking, lane changes, and highway merges.</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-indigo-600">
                            <span>2026 Checklist Download</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14m-7-7l7 7-7 7"/></svg>
                        </div>
                    </article>

                    <!-- Feature 4 -->
                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between" data-reveal data-tilt style="--delay: 240ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-600 mb-6">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">Proven Pass Tips & Tricks</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">Learn the most common automatic fails and instant point-deductions so you can avoid them completely.</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-sky-600">
                            <span>Avoid Instant Fails</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14m-7-7l7 7-7 7"/></svg>
                        </div>
                    </article>

                    <!-- Feature 5 -->
                    <article class="dtr-3d-card-v2 p-7 flex flex-col justify-between md:col-span-2 lg:col-span-2" data-reveal data-tilt style="--delay: 320ms;">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-600 mb-6">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-3">Practice in Your Own Car</h3>
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">Build muscle memory in the vehicle you are most comfortable driving, on your own schedule with family or friends — zero stress, zero extra rental fees.</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-600">
                            <span>Personal Vehicle Ready</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14m-7-7l7 7-7 7"/></svg>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- SECTION 3: How It Works: Practice Smarter, Save Hundreds (3D Step Layout) -->
        <section class="dtr-section py-20 px-4 sm:px-6 lg:px-8 border-t border-white/10">
            <!-- Background Shape Design Layer -->
            <div class="dtr-bg-shapes">
                <!-- Interactive Road Track Connector Path SVG (Behind Cards) -->
                <svg class="hidden lg:block absolute top-[45%] left-0 w-full h-36 opacity-85 pointer-events-none" viewBox="0 0 1200 120" fill="none">
                    <path d="M 150,60 Q 300,110 450,60 T 750,60 T 1050,60" stroke="url(#stepRouteGrad)" stroke-width="6" stroke-linecap="round" class="dtr-dash-flow" />
                    <path d="M 150,60 Q 300,110 450,60 T 750,60 T 1050,60" stroke="rgba(56, 189, 248, 0.25)" stroke-width="18" stroke-linecap="round" />
                    <!-- Step Node Dots -->
                    <circle cx="150" cy="60" r="9" fill="#2563eb" stroke="#38bdf8" stroke-width="3" />
                    <circle cx="450" cy="60" r="9" fill="#0891b2" stroke="#38bdf8" stroke-width="3" />
                    <circle cx="750" cy="60" r="9" fill="#4338ca" stroke="#a5b4fc" stroke-width="3" />
                    <circle cx="1050" cy="60" r="9" fill="#059669" stroke="#34d399" stroke-width="3" />
                    <defs>
                        <linearGradient id="stepRouteGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#2563eb" />
                            <stop offset="33%" stop-color="#0891b2" />
                            <stop offset="66%" stop-color="#4338ca" />
                            <stop offset="100%" stop-color="#059669" />
                        </linearGradient>
                    </defs>
                </svg>

                <!-- Floating Translucent Pill Accent Shapes -->
                <div class="dtr-shape-pill top-10 right-16 w-72 h-20 rotate-12 opacity-80 dtr-float-slow"></div>
                <div class="dtr-shape-pill bottom-10 left-12 w-80 h-24 -rotate-12 opacity-75 dtr-float-reverse"></div>
                <div class="dtr-shape-blob top-1/3 right-1/4 w-96 h-96 bg-cyan-500/25 dtr-pulse-glow"></div>
            </div>
            <div class="mx-auto max-w-7xl">
                <div class="text-center max-w-3xl mx-auto mb-16" data-reveal>
                    <span class="text-xs font-black uppercase tracking-widest text-cyan-400">Step-by-Step Blueprint</span>
                    <h2 class="mt-3 text-3xl font-black text-white sm:text-5xl">How It Works: Practice Smarter, Save Hundreds</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300 sm:text-base">Four simple steps from selecting your centre to mastering your test routes.</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Step 1 -->
                    <div class="dtr-3d-card-v2 p-6 flex flex-col justify-between" data-reveal style="--delay: 0ms;">
                        <div>
                            <div class="flex items-center justify-between mb-5">
                                <span class="dtr-step-badge">1</span>
                                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Step 01</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Choose Your Centre</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-semibold mb-4">Select your Ontario DriveTest location (Brampton, Mississauga, Toronto, Oakville, etc.).</p>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-50 border border-blue-100 text-blue-900 text-xs font-bold">
                            💡 <span class="text-blue-700 font-extrabold">How It Helps:</span> Focus exclusively on the roads & speed limits where you will be tested.
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="dtr-3d-card-v2 p-6 flex flex-col justify-between" data-reveal style="--delay: 90ms;">
                        <div>
                            <div class="flex items-center justify-between mb-5">
                                <span class="dtr-step-badge">2</span>
                                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Step 02</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Launch Route on Phone</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-semibold mb-4">Click to open turn-by-turn navigation on your mobile GPS app.</p>
                        </div>
                        <div class="p-3 rounded-lg bg-cyan-50 border border-cyan-100 text-cyan-900 text-xs font-bold">
                            💡 <span class="text-cyan-700 font-extrabold">How It Helps:</span> No guessing — experience exact test turns, merge lanes, and residential zones.
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="dtr-3d-card-v2 p-6 flex flex-col justify-between" data-reveal style="--delay: 180ms;">
                        <div>
                            <div class="flex items-center justify-between mb-5">
                                <span class="dtr-step-badge">3</span>
                                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Step 03</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Drive & Master</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-semibold mb-4">Practice in your own car with a friend or family member on your time.</p>
                        </div>
                        <div class="p-3 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-900 text-xs font-bold">
                            💡 <span class="text-indigo-700 font-extrabold">How It Helps:</span> Master tricky speed changes, school zones, and multi-lane intersections early.
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="dtr-3d-card-v2 p-6 flex flex-col justify-between" data-reveal style="--delay: 270ms;">
                        <div>
                            <div class="flex items-center justify-between mb-5">
                                <span class="dtr-step-badge">4</span>
                                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Step 04</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Check Marking Sheet</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-semibold mb-4">Download the 2026 examiner marking checklist and score yourself.</p>
                        </div>
                        <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-900 text-xs font-bold">
                            💡 <span class="text-emerald-700 font-extrabold">How It Helps:</span> Know precisely how examiners grade mirror checks, blind spots, & parking.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 4: Examiner Marking Sheets & What Examiners Look For (3D Interactive Preview) -->
        <section class="dtr-section py-20 px-4 sm:px-6 lg:px-8 bg-slate-900/60 border-t border-white/10">
            <!-- Background Shape Design Layer -->
            <div class="dtr-bg-shapes">
                <!-- Scorecard Concentric Radar / Target SVG behind Graphic Mockup -->
                <div class="absolute top-1/2 right-10 -translate-y-1/2 w-[560px] h-[560px] opacity-80 dtr-spin-reverse pointer-events-none">
                    <svg viewBox="0 0 500 500" fill="none" class="w-full h-full text-cyan-400">
                        <circle cx="250" cy="250" r="230" stroke="currentColor" stroke-width="2.5" stroke-dasharray="12 8" />
                        <circle cx="250" cy="250" r="170" stroke="rgba(16, 185, 129, 0.6)" stroke-width="2" stroke-dasharray="6 6" />
                        <circle cx="250" cy="250" r="110" stroke="rgba(56, 189, 248, 0.5)" stroke-width="3" />
                        <line x1="250" y1="20" x2="250" y2="480" stroke="rgba(255, 255, 255, 0.25)" stroke-width="1.5" />
                        <line x1="20" y1="250" x2="480" y2="250" stroke="rgba(255, 255, 255, 0.25)" stroke-width="1.5" />
                        <circle cx="250" cy="20" r="7" fill="#34d399" />
                        <circle cx="480" cy="250" r="7" fill="#38bdf8" />
                    </svg>
                </div>

                <!-- Floating Geometric Shield Outline (Left Behind Text) -->
                <div class="absolute top-16 left-12 w-56 h-64 opacity-70 dtr-float-slow">
                    <svg viewBox="0 0 200 240" fill="none" class="w-full h-full stroke-cyan-400">
                        <path d="M100 20 L180 50 V130 C180 180 100 220 100 220 C100 220 20 180 20 130 V50 L100 20 Z" stroke-width="3" stroke-dasharray="8 4" />
                        <path d="M100 45 L160 70 V125 C160 165 100 195 100 195 C100 195 40 165 40 125 V70 L100 45 Z" stroke-width="2" />
                    </svg>
                </div>

                <div class="dtr-shape-blob top-1/4 left-1/3 w-[450px] h-[450px] bg-emerald-500/25 dtr-pulse-glow"></div>
            </div>
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <!-- Left: Explanation & Checklist -->
                    <div data-reveal class="space-y-6">
                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-950/60 px-3.5 py-1 text-xs font-extrabold uppercase tracking-wider text-emerald-400">
                            📄 2026 Official Examiner Standards
                        </span>
                        <h2 class="text-3xl font-black text-white sm:text-5xl leading-tight">
                            Download Official 2026 DriveTest Examiner Marking Sheets
                        </h2>
                        <p class="text-slate-300 text-base leading-relaxed font-medium">
                            Don't go into your test blind. Know exactly how many points you can afford to lose and what gets marked as a major vs. minor error before sitting behind the wheel.
                        </p>

                        <div class="pt-4 space-y-3">
                            <h3 class="text-sm font-black uppercase tracking-widest text-cyan-400 mb-2">What Examiners Look For:</h3>
                            
                            <div class="dtr-check-item">
                                <div class="dtr-check-icon">✓</div>
                                <div>
                                    <h4 class="text-sm font-black text-white">Mirror & Blindspot Checks</h4>
                                    <p class="text-xs text-slate-300 font-medium">Checked every 5 to 7 seconds and before lane changes</p>
                                </div>
                            </div>

                            <div class="dtr-check-item">
                                <div class="dtr-check-icon">✓</div>
                                <div>
                                    <h4 class="text-sm font-black text-white">Speed Management</h4>
                                    <p class="text-xs text-slate-300 font-medium">Maintaining posted speed without trailing or speeding</p>
                                </div>
                            </div>

                            <div class="dtr-check-item">
                                <div class="dtr-check-icon">✓</div>
                                <div>
                                    <h4 class="text-sm font-black text-white">Full Stops & Right of Way</h4>
                                    <p class="text-xs text-slate-300 font-medium">Complete stops behind stop lines; yield rules</p>
                                </div>
                            </div>

                            <div class="dtr-check-item">
                                <div class="dtr-check-icon">✓</div>
                                <div>
                                    <h4 class="text-sm font-black text-white">Highway Merging (G Test)</h4>
                                    <p class="text-xs text-slate-300 font-medium">Matching flow of traffic smoothly on the on-ramp</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <a href="{{ route('driving-routes.index') }}" class="dtr-btn dtr-btn-primary px-8 py-3.5 text-sm rounded-xl">
                                Browse Routes & Download Sheets
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right: 3D Scorecard Graphic Mockup -->
                    <div data-reveal="slide-left" class="dtr-3d-dark-glow p-6 sm:p-8 border border-cyan-500/30 relative">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-cyan-500/20 border border-cyan-400/40 flex items-center justify-center text-cyan-300 font-black">
                                    ON
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-white">DriveTest Ontario Marking Sheet</h4>
                                    <p class="text-xs text-slate-400 font-mono">Form 2026-G/G2-VERIFIED</p>
                                </div>
                            </div>
                            <span class="text-xs font-black text-emerald-400 bg-emerald-950/80 border border-emerald-500/40 px-3 py-1 rounded-full uppercase">PASSED</span>
                        </div>

                        <div class="space-y-4 text-xs font-mono text-slate-300">
                            <div class="flex justify-between p-2.5 rounded bg-white/5 border border-white/5">
                                <span>[1] Observation & Mirror Check</span>
                                <span class="text-emerald-400 font-bold">PASS (0 DEDUCTION)</span>
                            </div>
                            <div class="flex justify-between p-2.5 rounded bg-white/5 border border-white/5">
                                <span>[2] 3-Point Turn & Parallel Park</span>
                                <span class="text-emerald-400 font-bold">PASS (0 DEDUCTION)</span>
                            </div>
                            <div class="flex justify-between p-2.5 rounded bg-white/5 border border-white/5">
                                <span>[3] Highway Merge (100 km/h)</span>
                                <span class="text-emerald-400 font-bold">PASS (0 DEDUCTION)</span>
                            </div>
                            <div class="flex justify-between p-2.5 rounded bg-white/5 border border-white/5">
                                <span>[4] Speed Limit Adherence</span>
                                <span class="text-emerald-400 font-bold">PASS (0 DEDUCTION)</span>
                            </div>
                        </div>

                        <div class="mt-6 p-4 rounded-xl bg-cyan-950/50 border border-cyan-500/30 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-black uppercase text-cyan-300 block">Overall Test Score</span>
                                <span class="text-xl font-black text-white">100 / 100 Perfect Pass</span>
                            </div>
                            <div class="w-12 h-12 rounded-full border-2 border-emerald-400 flex items-center justify-center text-emerald-400 font-black text-sm">
                                100%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 5: Top 5 Tips to Pass Your Ontario Road Test (3D Tilt Cards) -->
        <section class="dtr-section py-20 px-4 sm:px-6 lg:px-8 border-t border-white/10">
            <!-- Background Shape Design Layer -->
            <div class="dtr-bg-shapes">
                <!-- Hexagonal Mesh Grid Background -->
                <div class="dtr-shape-hex-pattern inset-0 w-full h-full"></div>

                <!-- Floating 3D Polyhedron Diamond Right -->
                <div class="absolute top-12 right-12 w-44 h-44 opacity-75 dtr-float-slow">
                    <svg viewBox="0 0 200 200" fill="none" class="w-full h-full stroke-cyan-400">
                        <polygon points="100,10 180,70 150,180 50,180 20,70" stroke-width="2.5" stroke-dasharray="6 4" />
                        <line x1="100" y1="10" x2="150" y2="180" stroke-width="2" />
                        <line x1="100" y1="10" x2="50" y2="180" stroke-width="2" />
                        <line x1="20" y1="70" x2="180" y2="70" stroke-width="2" />
                    </svg>
                </div>

                <!-- Floating Diamond Ring Left -->
                <div class="dtr-shape-diamond bottom-16 left-10 w-36 h-36 opacity-90 dtr-float-reverse"></div>
                <div class="dtr-shape-blob top-1/2 right-1/4 w-[450px] h-[450px] bg-sky-500/25 dtr-pulse-glow"></div>
            </div>
            <div class="mx-auto max-w-7xl">
                <div class="text-center max-w-3xl mx-auto mb-16" data-reveal>
                    <span class="text-xs font-black uppercase tracking-widest text-cyan-400">Expert Guidance</span>
                    <h2 class="mt-3 text-3xl font-black text-white sm:text-5xl">Top 5 Tips to Pass Your Ontario Road Test on the 1st Attempt</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300 sm:text-base">Master these key requirements to eliminate deductions and avoid instant disqualification.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Tip 1 -->
                    <div class="dtr-3d-card-v2 p-7 flex flex-col justify-between" data-reveal data-tilt style="--delay: 0ms;">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="w-9 h-9 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-600 font-black text-sm flex items-center justify-center">#1</span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full">Observation</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Exaggerate Your Head Movements</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-medium">Examiners track your eye and head movement. Make mirror and blindspot checks obvious every time you signal, turn, or change lanes.</p>
                        </div>
                    </div>

                    <!-- Tip 2 -->
                    <div class="dtr-3d-card-v2 p-7 flex flex-col justify-between" data-reveal data-tilt style="--delay: 80ms;">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="w-9 h-9 rounded-lg bg-red-500/10 border border-red-500/20 text-red-600 font-black text-sm flex items-center justify-center">#2</span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-600 bg-red-50 px-2.5 py-0.5 rounded-full">Critical Rule</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Master the Complete Stop</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-medium">Rolling through a stop sign is an automatic fail. Feel your car settle back completely behind the line for 2 full seconds before moving forward.</p>
                        </div>
                    </div>

                    <!-- Tip 3 -->
                    <div class="dtr-3d-card-v2 p-7 flex flex-col justify-between" data-reveal data-tilt style="--delay: 160ms;">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="w-9 h-9 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-600 font-black text-sm flex items-center justify-center">#3</span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full">Speed Limits</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Know the Route Speed Limits</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-medium">Residential streets are typically 40 or 50 km/h unless posted. School zones drop to 30 or 40 km/h — know these exact spots on your route!</p>
                        </div>
                    </div>

                    <!-- Tip 4 -->
                    <div class="dtr-3d-card-v2 p-7 flex flex-col justify-between" data-reveal data-tilt style="--delay: 240ms;">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="w-9 h-9 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 font-black text-sm flex items-center justify-center">#4</span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full">G Test Highway</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Match Speed on Highway On-Ramps</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-medium">Accelerate to 100 km/h on the ramp before merging onto expressways like the 401, 403, or QEW to avoid dangerous speed gaps.</p>
                        </div>
                    </div>

                    <!-- Tip 5 -->
                    <div class="dtr-3d-card-v2 p-7 flex flex-col justify-between md:col-span-2 lg:col-span-2" data-reveal data-tilt style="--delay: 320ms;">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="w-9 h-9 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 font-black text-sm flex items-center justify-center">#5</span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">Maneuvers</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Practice Parking Drills Off-Peak</h3>
                            <p class="text-slate-600 text-xs leading-relaxed font-medium">Spend 15 minutes practicing parallel parking, three-point turns, and reverse parking at your target test location prior to your test date.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 6: Featured Coverage (Database Dynamic Routes Grid) -->
        <section class="dtr-section dtr-section--routes border-t border-white/10 px-4 py-20 sm:px-6 lg:px-8">
            <!-- Background Shape Design Layer -->
            <div class="dtr-bg-shapes">
                <!-- Highway Multi-Lane Curved Vector SVG -->
                <svg class="absolute top-1/3 left-0 w-full h-96 opacity-80 pointer-events-none" viewBox="0 0 1400 300" fill="none">
                    <path d="M -100 200 Q 300 50 700 220 T 1500 100" stroke="url(#highwayGrad)" stroke-width="20" stroke-linecap="round" />
                    <path d="M -100 200 Q 300 50 700 220 T 1500 100" stroke="#ffffff" stroke-width="3.5" stroke-dasharray="16 12" stroke-linecap="round" class="dtr-dash-flow" />
                    <defs>
                        <linearGradient id="highwayGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#1e40af" />
                            <stop offset="50%" stop-color="#0284c7" />
                            <stop offset="100%" stop-color="#06b6d4" />
                        </linearGradient>
                    </defs>
                </svg>

                <!-- Floating GPS Location Pin / Compass Geometry -->
                <div class="absolute bottom-10 right-10 w-64 h-64 opacity-70 dtr-spin-slow">
                    <svg viewBox="0 0 200 200" fill="none" class="w-full h-full stroke-cyan-400">
                        <circle cx="100" cy="100" r="90" stroke-width="2.5" stroke-dasharray="10 6" />
                        <circle cx="100" cy="100" r="60" stroke-width="2" />
                        <polygon points="100,20 115,85 180,100 115,115 100,180 85,115 20,100 85,85" stroke-width="2" fill="rgba(56, 189, 248, 0.12)" />
                    </svg>
                </div>

                <div class="dtr-shape-blob top-10 left-10 w-[450px] h-[450px] bg-blue-600/30 dtr-pulse-glow"></div>
            </div>
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div data-reveal>
                        <p class="text-sm font-black uppercase text-sky-300">Featured coverage</p>
                        <h2 class="mt-3 text-3xl font-black text-white sm:text-5xl">Premium route cards built for inspection.</h2>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-zinc-400">Flip a route card for details, then continue straight to checkout or login.</p>
                    </div>
                    <a href="{{ route('driving-routes.index') }}" class="dtr-btn dtr-btn-secondary" data-reveal="slide-left" style="--delay: 90ms;">View all routes</a>
                </div>

                @if($featuredRoutes->isEmpty())
                    <div class="dtr-glass rounded-lg px-6 py-12 text-center" data-reveal>
                        <h3 class="text-xl font-black text-white">No routes available</h3>
                        <p class="mt-2 text-sm text-zinc-400">Add active routes from the admin dashboard.</p>
                    </div>
                @else
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($featuredRoutes as $drivingRoute)
                            <article class="dtr-flip-card" data-flip-card data-reveal style="--delay: {{ $loop->index * 70 }}ms;">
                                <div class="dtr-flip-inner">
                                    <div class="dtr-flip-face dtr-gradient-border p-5">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <span class="dtr-badge">Available now</span>
                                                <h3 class="mt-4 text-2xl font-black text-white">{{ $drivingRoute->title }}</h3>
                                                <p class="mt-1 text-sm font-semibold text-zinc-400">{{ $drivingRoute->city }}, {{ $drivingRoute->province }}</p>
                                            </div>
                                            <div class="rounded-md bg-white/[.08] px-3 py-2 text-right">
                                                <p class="text-xs font-bold text-zinc-500">Price</p>
                                                <p class="font-black text-white">${{ number_format((float) $drivingRoute->price, 2) }}</p>
                                            </div>
                                        </div>

                                        <div class="dtr-route-map mt-5 h-32">
                                            <svg class="relative h-full w-full" viewBox="0 0 360 130" fill="none" aria-hidden="true">
                                                <path d="M22 92 C64 44 95 110 135 74 C178 36 205 47 240 77 C276 107 300 71 338 37" stroke="rgba(255,255,255,.14)" stroke-width="14" stroke-linecap="round" />
                                                <path d="M22 92 C64 44 95 110 135 74 C178 36 205 47 240 77 C276 107 300 71 338 37" stroke="url(#miniRoute{{ $loop->index }})" stroke-width="5" stroke-linecap="round" />
                                                <circle cx="22" cy="92" r="7" fill="#38bdf8" />
                                                <circle cx="338" cy="37" r="7" fill="#06b6d4" />
                                                <defs>
                                                    <linearGradient id="miniRoute{{ $loop->index }}" x1="22" x2="338" y1="92" y2="37">
                                                        <stop stop-color="#1e3a8a" />
                                                        <stop offset=".5" stop-color="#2563eb" />
                                                        <stop offset="1" stop-color="#06b6d4" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>

                                        <p class="mt-5 line-clamp-3 text-sm leading-7 text-zinc-400">
                                            {{ $drivingRoute->description ?: 'Practice with a paid route map, live location tracking, and route confidence before test day.' }}
                                        </p>

                                        <div class="mt-auto pt-5">
                                            <button type="button" class="dtr-flip-toggle" data-flip-toggle>
                                                Route details
                                                <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path d="M5 12h14" />
                                                    <path d="m13 6 6 6-6 6" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="dtr-flip-face dtr-flip-back dtr-glass p-5">
                                        <span class="dtr-badge">Route intelligence</span>
                                        <h3 class="mt-4 text-2xl font-black text-white">{{ $drivingRoute->title }}</h3>
                                        <dl class="mt-5 divide-y divide-white/10 text-sm">
                                            <div class="flex items-center justify-between gap-3 py-3">
                                                <dt class="text-zinc-500">Starts included</dt>
                                                <dd class="font-black text-white">{{ $drivingRoute->access_limit ?? 1 }}</dd>
                                            </div>
                                            <div class="flex items-center justify-between gap-3 py-3">
                                                <dt class="text-zinc-500">Estimated time</dt>
                                                <dd class="font-black text-white">{{ $drivingRoute->route_duration_minutes ? $drivingRoute->route_duration_minutes.' mins' : 'Ready' }}</dd>
                                            </div>
                                            <div class="flex items-center justify-between gap-3 py-3">
                                                <dt class="text-zinc-500">Route points</dt>
                                                <dd class="font-black text-white">{{ $drivingRoute->points_count }}</dd>
                                            </div>
                                        </dl>

                                        <div class="mt-auto grid gap-3 pt-5">
                                            @auth
                                                <a href="{{ route('driving-routes.checkout', $drivingRoute) }}" class="dtr-btn dtr-btn-primary">Buy Route</a>
                                            @else
                                                <a href="{{ route('login') }}" class="dtr-btn dtr-btn-primary">Login to Buy</a>
                                            @endauth
                                            <button type="button" class="dtr-flip-toggle" data-flip-toggle>Back to summary</button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <!-- SECTION 7: Final Call to Action ("Ready to Pass Your Road Test on the First Try?") -->
        <section class="dtr-section dtr-section--gradient-e px-4 py-20 sm:px-6 lg:px-8 border-t border-white/10">
            <!-- Background Shape Design Layer -->
            <div class="dtr-bg-shapes">
                <!-- Concentric Aurora Glow Rings -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[750px] h-[750px] opacity-80 dtr-spin-slow">
                    <svg viewBox="0 0 600 600" fill="none" class="w-full h-full text-cyan-400">
                        <circle cx="300" cy="300" r="280" stroke="currentColor" stroke-width="2" stroke-dasharray="20 12" />
                        <circle cx="300" cy="300" r="210" stroke="rgba(37, 99, 235, 0.6)" stroke-width="2.5" stroke-dasharray="12 8" />
                        <circle cx="300" cy="300" r="140" stroke="rgba(56, 189, 248, 0.7)" stroke-width="3" />
                    </svg>
                </div>
                <div class="dtr-shape-blob top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[650px] bg-cyan-500/30 dtr-pulse-glow"></div>
            </div>
            <div class="dtr-callout dtr-gradient-border mx-auto grid max-w-7xl gap-8 p-8 sm:p-12 lg:grid-cols-[1fr_auto] lg:items-center" data-reveal>
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-950/70 px-3.5 py-1 text-xs font-extrabold uppercase tracking-wider text-cyan-400">
                        🚗 Pass on the 1st Attempt
                    </span>
                    <h2 class="mt-4 max-w-3xl text-3xl font-black text-white sm:text-5xl leading-tight">
                        Ready to Pass Your Road Test on the First Try?
                    </h2>
                    <p class="mt-4 max-w-2xl text-base leading-relaxed text-zinc-300">
                        Select your DriveTest centre, load your 2026 G or G2 test route onto your phone, and start practicing today!
                    </p>
                </div>
                <a href="{{ route('driving-routes.index') }}" class="dtr-btn dtr-btn-primary px-8 py-4 text-base rounded-xl">
                    Select Your DriveTest Centre
                    <svg class="dtr-icon w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </a>
            </div>
        </section>

         <!-- SECTION 7: Frequently Asked Questions (3D Accordion) -->
        <section class="dtr-section dtr-section--gradient-c py-20 px-4 sm:px-6 lg:px-8 border-t border-white/10">
            <!-- Background Shape Design Layer -->
            <div class="dtr-bg-shapes">
                <!-- Floating Question Badge Geometry -->
                <div class="absolute top-12 left-12 w-48 h-48 opacity-75 dtr-float-slow">
                    <svg viewBox="0 0 200 200" fill="none" class="w-full h-full stroke-cyan-400">
                        <circle cx="100" cy="100" r="85" stroke-width="2.5" stroke-dasharray="8 6" />
                        <path d="M 80,75 C 80,55 120,55 120,75 C 120,95 100,95 100,115" stroke-width="4" stroke-linecap="round" />
                        <circle cx="100" cy="140" r="6" fill="#38bdf8" />
                    </svg>
                </div>
                <div class="dtr-shape-grid-dots bottom-10 right-10 w-80 h-80 opacity-80"></div>
                <div class="dtr-shape-blob bottom-10 right-10 w-[450px] h-[450px] bg-blue-600/30 dtr-pulse-glow"></div>
            </div>
            <div class="mx-auto max-w-6xl">
                <div class="text-center mb-16" data-reveal>
                    <span class="text-xs font-black uppercase tracking-widest text-cyan-400">Got Questions?</span>
                    <h2 class="mt-3 text-3xl font-black text-white sm:text-5xl">Frequently Asked Questions</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300 sm:text-base">Everything you need to know about taking your test in your own car.</p>
                </div>

                <div class="space-y-4" data-reveal>
                    <!-- FAQ 1 -->
                    <div class="dtr-faq-box is-open" data-faq-box>
                        <button type="button" class="dtr-faq-btn" data-faq-btn>
                            <span>Do I really need a driving school car to take my test?</span>
                            <svg class="dtr-faq-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="dtr-faq-body">
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">
                                No! As long as your personal vehicle is in safe working order (functional lights, working seatbelts, horn, clear windshield, valid insurance, and license plates), you can take your test in your own car.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="dtr-faq-box" data-faq-box>
                        <button type="button" class="dtr-faq-btn" data-faq-btn>
                            <span>How accurate are these test routes?</span>
                            <svg class="dtr-faq-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="dtr-faq-body">
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">
                                Our routes are updated for 2026 based on real test feedback across Ontario DriveTest centres. While examiners may adjust routes slightly for traffic or roadwork, practicing these primary paths ensures you know the surrounding neighborhood inside out.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="dtr-faq-box" data-faq-box>
                        <button type="button" class="dtr-faq-btn" data-faq-btn>
                            <span>Can I open the route on my phone's GPS?</span>
                            <svg class="dtr-faq-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="dtr-faq-body">
                            <p class="text-slate-600 text-sm leading-relaxed font-medium">
                                Yes! One tap loads the route directly into your mobile GPS app so your passenger or co-driver can guide you smoothly through the route.
                            </p>
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
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const revealItems = document.querySelectorAll('[data-reveal], [data-animate-chart]');

            const formatNumber = (value) => new Intl.NumberFormat().format(Math.round(value));

            function animateCounter(counter) {
                if (counter.dataset.counted === 'true') {
                    return;
                }

                counter.dataset.counted = 'true';
                const target = Number(counter.dataset.target || counter.textContent.replace(/,/g, '')) || 0;

                if (prefersReducedMotion) {
                    counter.textContent = formatNumber(target);
                    return;
                }

                const duration = 1300;
                const start = performance.now();

                function tick(now) {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    counter.textContent = formatNumber(target * eased);

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    } else {
                        counter.textContent = formatNumber(target);
                    }
                }

                counter.textContent = '0';
                requestAnimationFrame(tick);
            }

            if (!('IntersectionObserver' in window) || prefersReducedMotion) {
                revealItems.forEach((item) => item.classList.add('is-visible'));
                document.querySelectorAll('[data-counter]').forEach(animateCounter);
            } else {
                const revealObserver = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) {
                            return;
                        }

                        entry.target.classList.add('is-visible');
                        revealObserver.unobserve(entry.target);
                    });
                }, { threshold: .18, rootMargin: '0px 0px -8% 0px' });

                revealItems.forEach((item) => revealObserver.observe(item));

                const counterObserver = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) {
                            return;
                        }

                        animateCounter(entry.target);
                        counterObserver.unobserve(entry.target);
                    });
                }, { threshold: .45 });

                document.querySelectorAll('[data-counter]').forEach((counter) => counterObserver.observe(counter));
            }

            document.querySelectorAll('[data-tilt]').forEach((panel) => {
                const inner = panel.querySelector('.dtr-tilt-inner');

                if (!inner || prefersReducedMotion) {
                    return;
                }

                panel.addEventListener('pointermove', (event) => {
                    const bounds = panel.getBoundingClientRect();
                    const x = (event.clientX - bounds.left) / bounds.width;
                    const y = (event.clientY - bounds.top) / bounds.height;
                    const rotateY = (x - .5) * 13;
                    const rotateX = (y - .5) * -10;

                    inner.style.setProperty('--tilt-x', `${rotateX.toFixed(2)}deg`);
                    inner.style.setProperty('--tilt-y', `${rotateY.toFixed(2)}deg`);
                    inner.style.setProperty('--tilt-z', '-4px');
                });

                panel.addEventListener('pointerleave', () => {
                    inner.style.setProperty('--tilt-x', '0deg');
                    inner.style.setProperty('--tilt-y', '0deg');
                    inner.style.setProperty('--tilt-z', '0');
                });
            });

            document.querySelectorAll('[data-flip-toggle]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const card = button.closest('[data-flip-card]');

                    if (card) {
                        card.classList.toggle('is-flipped');
                    }
                });
            });

            // Step-by-step Route Search Wizard
            const citiesData = @json($cities);
            let selectedPackage = null;
            let selectedCity = null;
            let selectedRoute = null;

            const searchCombobox = document.querySelector('[data-routes-search-combobox]');
            const searchInput = document.getElementById('routes-search-input');
            const searchBtn = document.getElementById('routes-search-btn');
            const searchPanel = document.getElementById('routes-search-panel');

            const phasePackage = document.getElementById('phase-package');
            const phaseCity = document.getElementById('phase-city');
            const phaseRoute = document.getElementById('phase-route');

            const citySearchInput = document.getElementById('city-search-input');
            const citySearchList = document.getElementById('city-search-list');
            const routeSearchList = document.getElementById('route-search-list');

            const badgePkg = document.getElementById('badge-pkg');
            const badgePkg2 = document.getElementById('badge-pkg-2');
            const badgeCity = document.getElementById('badge-city');

            function openPanel() {
                if (searchCombobox) {
                    searchCombobox.classList.add('is-open');
                    searchInput?.setAttribute('aria-expanded', 'true');
                }
            }

            function closePanel() {
                if (searchCombobox) {
                    searchCombobox.classList.remove('is-open');
                    searchInput?.setAttribute('aria-expanded', 'false');
                }
            }

            // Click input to open panel
            if (searchInput) {
                searchInput.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openPanel();
                });
            }

            // Close panel when clicking outside
            document.addEventListener('click', (e) => {
                if (searchCombobox && !searchCombobox.contains(e.target)) {
                    closePanel();
                }
            });

            // Reset back functions
            document.querySelectorAll('[data-reset-to]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const target = btn.dataset.resetTo;
                    if (target === 'package') {
                        selectedPackage = null;
                        selectedCity = null;
                        selectedRoute = null;
                        if (searchInput) {
                            searchInput.value = '';
                        }
                        if (searchBtn) {
                            searchBtn.href = "{{ route('driving-routes.index') }}";
                            const span = searchBtn.querySelector('span');
                            if (span) span.textContent = 'Routes';
                        }
                        
                        phasePackage?.classList.remove('hidden');
                        phaseCity?.classList.add('hidden');
                        phaseRoute?.classList.add('hidden');
                    } else if (target === 'city') {
                        selectedCity = null;
                        selectedRoute = null;
                        
                        phasePackage?.classList.add('hidden');
                        phaseCity?.classList.remove('hidden');
                        phaseRoute?.classList.add('hidden');
                        renderCities();
                    }
                });
            });

            // Phase 1: Click G1 / G2
            document.querySelectorAll('[data-select-pkg]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectedPackage = btn.dataset.selectPkg;
                    const val = selectedPackage === 'g1' ? 'G2 Test Routes' : 'G Test Routes';
                    if (badgePkg) badgePkg.textContent = val;
                    if (badgePkg2) badgePkg2.textContent = val;
                    
                    phasePackage?.classList.add('hidden');
                    phaseCity?.classList.remove('hidden');
                    renderCities();
                    if (citySearchInput) {
                        citySearchInput.value = '';
                        citySearchInput.focus();
                    }
                });
            });

            // Render Cities
            function renderCities() {
                if (!citySearchList) return;
                citySearchList.innerHTML = '';
                const query = citySearchInput ? citySearchInput.value.trim().toLowerCase() : '';

                const filtered = citiesData.filter(city => 
                    city.routes && city.routes.some(route => route.package_type === selectedPackage)
                ).filter(city => 
                    !query || city.name.toLowerCase().includes(query) || (city.address && city.address.toLowerCase().includes(query))
                );

                if (filtered.length === 0) {
                    citySearchList.innerHTML = '<p class="text-zinc-500 text-sm py-3 text-center">No matching cities found.</p>';
                    return;
                }

                filtered.forEach(city => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'dtr-city-option flex items-center justify-between gap-4';
                    
                    const count = city.routes ? city.routes.filter(r => r.package_type === selectedPackage).length : 0;
                    
                    btn.innerHTML = `
                        <span>
                            <span class="block font-black text-slate-800">${city.name}</span>
                            <span class="mt-0.5 block text-xs text-slate-500 text-left">${city.address || ''}</span>
                        </span>
                        <span class="shrink-0 rounded-md border border-cyan-500/20 bg-cyan-50 px-2 py-0.5 text-xs font-black text-cyan-600">
                            ${count} ${count === 1 ? 'route' : 'routes'}
                        </span>
                    `;

                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        selectCity(city);
                    });

                    citySearchList.appendChild(btn);
                });
            }

            if (citySearchInput) {
                citySearchInput.addEventListener('input', renderCities);
            }

            // Phase 2: Select City
            function selectCity(city) {
                selectedCity = city;
                if (badgeCity) badgeCity.textContent = city.name;
                
                phaseCity?.classList.add('hidden');
                phaseRoute?.classList.remove('hidden');
                renderRoutes();
            }

            // Render Routes
            function renderRoutes() {
                if (!routeSearchList) return;
                routeSearchList.innerHTML = '';
                if (!selectedCity) return;

                const filteredRoutes = selectedCity.routes ? selectedCity.routes.filter(r => r.package_type === selectedPackage) : [];

                if (filteredRoutes.length === 0) {
                    routeSearchList.innerHTML = '<p class="text-zinc-500 text-sm py-3 text-center">No routes available.</p>';
                    return;
                }

                filteredRoutes.forEach(route => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'dtr-city-option flex items-center justify-between gap-4';
                    
                    btn.innerHTML = `
                        <span class="text-left">
                            <span class="block font-black text-slate-800">${route.title}</span>
                            <span class="mt-0.5 block text-xs text-slate-500">
                                ${route.route_duration_minutes ? route.route_duration_minutes + ' mins' : 'N/A'} &bull; 
                                ${route.route_length_km ? route.route_length_km + ' km' : 'N/A'}
                            </span>
                        </span>
                        <span class="shrink-0 font-extrabold text-cyan-600 text-sm">
                            ${Number(route.price) > 0 ? '$' + Number(route.price).toFixed(2) : 'Free'}
                        </span>
                    `;

                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        selectRoute(route);
                    });

                    routeSearchList.appendChild(btn);
                });
            }

            // Phase 3: Select Route
            function selectRoute(route) {
                selectedRoute = route;
                
                // Set the value in the input box
                if (searchInput) {
                    const pkgLabel = selectedPackage === 'g1' ? 'G2 Test Routes' : 'G Test Routes';
                    searchInput.value = `${pkgLabel} - ${selectedCity.name} - ${route.title}`;
                }
                
                // Update search action button
                if (searchBtn) {
                    searchBtn.href = `/driving-routes/${route.id}`;
                    const span = searchBtn.querySelector('span');
                    if (span) span.textContent = 'Practice Route';
                }
                
                closePanel();
            }

            const dashboardTabs = document.querySelectorAll('[data-dashboard-tab]');
            const dashboardIndicator = document.querySelector('[data-dashboard-indicator]');

            function moveDashboardIndicator(activeTab) {
                if (!dashboardIndicator || !activeTab) {
                    return;
                }

                dashboardIndicator.style.height = `${activeTab.offsetHeight}px`;
                dashboardIndicator.style.transform = `translateY(${activeTab.offsetTop}px)`;
            }

            dashboardTabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    dashboardTabs.forEach((item) => item.classList.remove('is-active'));
                    tab.classList.add('is-active');
                    moveDashboardIndicator(tab);
                });
            });

            // FAQ Accordion Handlers
            document.querySelectorAll('[data-faq-btn]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const box = btn.closest('[data-faq-box]');
                    if (box) {
                        const isOpen = box.classList.contains('is-open');
                        document.querySelectorAll('[data-faq-box]').forEach((item) => item.classList.remove('is-open'));
                        if (!isOpen) {
                            box.classList.add('is-open');
                        }
                    }
                });
            });

            moveDashboardIndicator(document.querySelector('[data-dashboard-tab].is-active'));
            window.addEventListener('resize', () => moveDashboardIndicator(document.querySelector('[data-dashboard-tab].is-active')), { passive: true });
        })();
    </script>
@endpush
