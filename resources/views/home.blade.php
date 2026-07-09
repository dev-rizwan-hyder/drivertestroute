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
            --section-image: var(--public-image-section);
            position: relative;
            isolation: isolate;
            overflow: hidden;
            content-visibility: auto;
            contain-intrinsic-size: 900px;
            background-color: var(--dtr-bg);
            background-image:
                var(--section-bg),
                linear-gradient(90deg, rgba(248, 249, 250, .58), rgba(255, 255, 255, .28), rgba(241, 243, 245, .64)),
                var(--section-image);
            background-position: center, center, center;
            background-repeat: no-repeat;
            background-size: auto, auto, cover;
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
            min-height: 100svh;
            background-color: var(--dtr-bg);
            background-image:
                linear-gradient(90deg, rgba(248, 249, 250, .96) 0%, rgba(248, 249, 250, .72) 48%, rgba(248, 249, 250, .36) 100%),
                linear-gradient(180deg, rgba(248, 249, 250, .72) 0%, rgba(255, 255, 255, .34) 52%, rgba(241, 243, 245, .92) 100%),
                var(--public-image-hero);
            background-position: center, center, center right;
            background-repeat: no-repeat;
            background-size: auto, auto, cover;
        }

        .dtr-aurora {
            position: absolute;
            inset: -18% -12% -10%;
            z-index: -3;
            opacity: .18;
            filter: blur(24px) saturate(1.08);
            background:
                conic-gradient(from 130deg at 48% 44%, rgba(30, 64, 175, .16), rgba(37, 99, 235, .14), rgba(8, 145, 178, .12), rgba(241, 243, 245, .18), rgba(30, 64, 175, .16)),
                linear-gradient(115deg, rgba(6, 182, 212, .08), transparent 34%, rgba(37, 99, 235, .1) 58%, transparent 82%, rgba(56, 189, 248, .06));
            animation: dtr-aurora 28s cubic-bezier(.45, 0, .2, 1) infinite alternate;
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
            border: 1px solid rgba(37, 99, 235, .22);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .84);
            padding: .45rem .7rem;
            color: #1d4ed8;
            font-size: .75rem;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
            backdrop-filter: blur(16px);
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

        .dtr-city-combobox {
            position: relative;
            max-width: 42rem;
        }

        .dtr-city-input-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: .75rem;
            border: 1px solid #d8dee6;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .92);
            padding: .5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
            transition: border-color 220ms ease-out, box-shadow 220ms ease-out;
        }

        .dtr-city-combobox.is-open .dtr-city-input-wrap,
        .dtr-city-input-wrap:focus-within {
            border-color: rgba(37, 99, 235, .5);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12), 0 8px 20px rgba(15, 23, 42, .08);
        }

        .dtr-city-input {
            min-width: 0;
            border: 0;
            background: transparent;
            padding: .7rem .8rem;
            color: var(--dtr-text);
            font-weight: 800;
            outline: 0;
        }

        .dtr-city-input::placeholder {
            color: #6b7280;
        }

        .dtr-city-panel {
            position: absolute;
            right: 0;
            left: 0;
            z-index: 20;
            margin-top: .55rem;
            max-height: 19rem;
            overflow-y: auto;
            border: 1px solid #d8dee6;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .98);
            box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
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
            border-bottom: 1px solid #e0e0e0;
            background: transparent;
            padding: .9rem 1rem;
            text-align: left;
            transition: background 180ms ease-out, transform 180ms cubic-bezier(.16, 1, .3, 1);
        }

        .dtr-city-option:hover,
        .dtr-city-option:focus-visible {
            background: #eff6ff;
            outline: 0;
            transform: translateX(2px);
        }

        .dtr-city-option:last-child {
            border-bottom: 0;
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

        .dtr-hero-image-panel {
            position: relative;
            min-height: min(62vh, 38rem);
            overflow: hidden;
            background-image:
                linear-gradient(90deg, rgba(248, 249, 250, .34), rgba(248, 249, 250, 0) 48%, rgba(241, 243, 245, .38)),
                var(--public-image-route);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            filter: saturate(1.12) contrast(1.06);
            mask-image: radial-gradient(ellipse at center, #000 0%, #000 58%, rgba(0, 0, 0, .72) 76%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 0%, #000 58%, rgba(0, 0, 0, .72) 76%, transparent 100%);
        }

        .dtr-hero-image-panel::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 54% 48%, rgba(56, 189, 248, .24), transparent 28%),
                linear-gradient(180deg, transparent 0%, rgba(248, 249, 250, .28) 100%);
            pointer-events: none;
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

        @media (max-width: 900px) {
            .dtr-route-row {
                grid-template-columns: 1fr;
                gap: .55rem;
            }

            .dtr-hero {
                background-position: center, center, 62% center;
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
        <section class="dtr-hero">
            <div class="dtr-aurora"></div>

            <div class="relative mx-auto grid min-h-[100svh] max-w-7xl items-center gap-12 px-4 pb-16 pt-28 sm:px-6 lg:grid-cols-[.92fr_1.08fr] lg:px-8 lg:pt-32">
                <div data-reveal>
                    <span class="dtr-kicker">
                        <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4 19V5" />
                            <path d="M4 7c2.5-2 5-2 7.5 0s5 2 8.5 0v10c-3.5 2-6 2-8.5 0s-5-2-7.5 0" />
                        </svg>
                        Paid route practice platform
                    </span>

                    <h1 class="mt-6 max-w-3xl text-5xl font-black leading-[1.02] text-white sm:text-6xl lg:text-7xl">
                        Driver Test Routes
                        <span class="dtr-gradient-text block">practice with precision.</span>
                    </h1>

                    <p class="mt-6 max-w-2xl text-base leading-8 text-zinc-300 sm:text-lg">
                        Unlock professional test-route maps, track live practice starts, and keep every session organized in a polished route dashboard built for focused test-day preparation.
                    </p>

                    @if($cities->isNotEmpty())
                        <div class="dtr-city-combobox mt-8" data-city-combobox>
                            <div class="dtr-city-input-wrap">
                                <input
                                    type="text"
                                    class="dtr-city-input"
                                    placeholder="Select your city"
                                    autocomplete="off"
                                    role="combobox"
                                    aria-expanded="false"
                                    aria-controls="city-options"
                                    data-city-input
                                >
                                <a href="{{ route('driving-routes.index') }}" class="dtr-btn dtr-btn-primary min-h-11 px-4 py-3">
                                    Routes
                                </a>
                            </div>

                            <div id="city-options" class="dtr-city-panel" role="listbox" data-city-panel>
                                @foreach($cities as $city)
                                    <button
                                        type="button"
                                        class="dtr-city-option"
                                        role="option"
                                        data-city-option
                                        data-city-name="{{ \Illuminate\Support\Str::lower($city->name) }}"
                                        data-city-address="{{ \Illuminate\Support\Str::lower($city->address) }}"
                                        data-city-url="{{ route('driving-routes.index', ['city' => $city->id]) }}"
                                    >
                                        <span class="flex items-start justify-between gap-4">
                                            <span>
                                                <span class="block font-black text-white">{{ $city->name }}</span>
                                                <span class="mt-1 block text-sm leading-5 text-slate-400">{{ $city->address }}</span>
                                            </span>
                                            <span class="shrink-0 rounded-md border border-blue-500/20 bg-white/[.06] px-2 py-1 text-xs font-black text-cyan-100">
                                                {{ $city->active_routes_count }}
                                            </span>
                                        </span>
                                    </button>
                                @endforeach
                                <p class="hidden px-4 py-5 text-sm font-semibold text-slate-400" data-city-empty>No matching cities.</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('driving-routes.index') }}" class="dtr-btn dtr-btn-primary">
                            Browse Routes
                            <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </a>
                        @auth
                            <a href="{{ route('driving-routes.my') }}" class="dtr-btn dtr-btn-secondary">
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
                            <a href="{{ route('register') }}" class="dtr-btn dtr-btn-secondary">
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

                    <dl class="mt-10 grid max-w-2xl gap-3 sm:grid-cols-3">
                        <div class="dtr-glass rounded-lg p-4">
                            <dt class="text-sm font-semibold text-zinc-400">Active routes</dt>
                            <dd class="mt-2 text-3xl font-black text-white" data-counter data-target="{{ $routeCount }}">{{ number_format($routeCount) }}</dd>
                        </div>
                        <div class="dtr-glass rounded-lg p-4">
                            <dt class="text-sm font-semibold text-zinc-400">Cities covered</dt>
                            <dd class="mt-2 text-3xl font-black text-white" data-counter data-target="{{ $cityCount }}">{{ number_format($cityCount) }}</dd>
                        </div>
                        <div class="dtr-glass rounded-lg p-4">
                            <dt class="text-sm font-semibold text-zinc-400">Map starts used</dt>
                            <dd class="mt-2 text-3xl font-black text-white" data-counter data-target="{{ $startCount }}">{{ number_format($startCount) }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="dtr-float hidden lg:block" data-reveal="slide-right" style="--delay: 120ms;">
                    <div class="dtr-hero-image-panel" aria-hidden="true"></div>
                </div>
            </div>
        </section>

        <section class="dtr-section dtr-section--dashboard border-y border-white/10 px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div data-reveal>
                        <p class="text-sm font-black uppercase text-cyan-300">Dashboard section</p>
                        <h2 class="mt-3 max-w-3xl text-3xl font-black leading-tight text-white sm:text-5xl">Route performance, purchase activity, and practice readiness in one view.</h2>
                    </div>
                    <p class="max-w-xl text-sm leading-7 text-zinc-400 lg:text-right" data-reveal="slide-left" style="--delay: 90ms;">
                        A compact command center for route inventory, city coverage, student access, and practice usage with animated metrics and responsive data views.
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <article class="dtr-stat-card dtr-gradient-border" data-reveal data-animate-chart>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-zinc-400">Active routes</p>
                                <p class="mt-2 text-4xl font-black text-white" data-counter data-target="{{ $routeCount }}">{{ number_format($routeCount) }}</p>
                            </div>
                            <span class="grid h-10 w-10 place-items-center rounded-md bg-cyan-400/10 text-cyan-200">
                                <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M3 6h18" />
                                    <path d="M3 12h18" />
                                    <path d="M3 18h18" />
                                </svg>
                            </span>
                        </div>
                        <svg class="dtr-mini-chart mt-5 h-14 w-full" viewBox="0 0 220 58" fill="none" aria-hidden="true">
                            <path d="M4 48 C32 25 46 34 68 26 C94 16 113 40 139 27 C164 15 177 21 216 8" stroke="url(#sparkRoutes)" stroke-width="4" stroke-linecap="round" />
                            <defs>
                                <linearGradient id="sparkRoutes" x1="4" x2="216" y1="48" y2="8">
                                    <stop stop-color="#1e3a8a" />
                                    <stop offset="1" stop-color="#06b6d4" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </article>

                    <article class="dtr-stat-card dtr-gradient-border" data-reveal data-animate-chart style="--delay: 80ms;">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-zinc-400">Cities covered</p>
                                <p class="mt-2 text-4xl font-black text-white" data-counter data-target="{{ $cityCount }}">{{ number_format($cityCount) }}</p>
                            </div>
                            <span class="grid h-10 w-10 place-items-center rounded-md bg-blue-400/10 text-blue-200">
                                <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </span>
                        </div>
                        <svg class="dtr-mini-chart mt-5 h-14 w-full" viewBox="0 0 220 58" fill="none" aria-hidden="true">
                            <polyline points="5,48 35,42 63,44 91,28 122,32 151,20 182,24 215,10" stroke="url(#sparkCities)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <linearGradient id="sparkCities" x1="5" x2="215" y1="48" y2="10">
                                    <stop stop-color="#1e40af" />
                                    <stop offset="1" stop-color="#38bdf8" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </article>

                    <article class="dtr-stat-card dtr-gradient-border" data-reveal data-animate-chart style="--delay: 160ms;">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-zinc-400">Map starts used</p>
                                <p class="mt-2 text-4xl font-black text-white" data-counter data-target="{{ $startCount }}">{{ number_format($startCount) }}</p>
                            </div>
                            <span class="grid h-10 w-10 place-items-center rounded-md bg-sky-400/10 text-sky-200">
                                <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="m5 3 14 9-14 9V3Z" />
                                </svg>
                            </span>
                        </div>
                        <svg class="dtr-mini-chart mt-5 h-14 w-full" viewBox="0 0 220 58" fill="none" aria-hidden="true">
                            <path d="M5 42 C30 45 44 16 69 23 C90 29 98 51 124 42 C153 32 158 13 183 18 C199 21 207 14 216 9" stroke="url(#sparkStarts)" stroke-width="4" stroke-linecap="round" />
                            <defs>
                                <linearGradient id="sparkStarts" x1="5" x2="216" y1="42" y2="9">
                                    <stop stop-color="#1e3a8a" />
                                    <stop offset=".55" stop-color="#2563eb" />
                                    <stop offset="1" stop-color="#06b6d4" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </article>

                    <article class="dtr-stat-card dtr-gradient-border" data-reveal data-animate-chart style="--delay: 240ms;">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-zinc-400">Readiness score</p>
                                <p class="mt-2 text-4xl font-black text-white" data-counter data-target="{{ $practiceScore }}">{{ $practiceScore }}</p>
                            </div>
                            <span class="grid h-10 w-10 place-items-center rounded-md bg-cyan-400/10 text-cyan-200">
                                <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                        </div>
                        <svg class="dtr-mini-chart mt-5 h-14 w-full" viewBox="0 0 220 58" fill="none" aria-hidden="true">
                            <path d="M4 49 C33 47 45 37 66 38 C89 39 93 20 119 22 C142 24 153 36 174 23 C190 13 200 13 216 8" stroke="url(#sparkReady)" stroke-width="4" stroke-linecap="round" />
                            <defs>
                                <linearGradient id="sparkReady" x1="4" x2="216" y1="49" y2="8">
                                    <stop stop-color="#38bdf8" />
                                    <stop offset=".55" stop-color="#06b6d4" />
                                    <stop offset="1" stop-color="#1e40af" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </article>
                </div>
            </div>
        </section>

        <section class="dtr-section dtr-section--workflow px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mx-auto mb-10 max-w-3xl text-center" data-reveal>
                    <p class="text-sm font-black uppercase text-cyan-300">How it works</p>
                    <h2 class="mt-3 text-3xl font-black text-white sm:text-5xl">A crisp route workflow with interactive detail cards.</h2>
                    <p class="mt-4 text-sm leading-7 text-zinc-400 sm:text-base">Hover or tap each card to reveal the operational details behind the practice flow.</p>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    @foreach([
                        ['step' => '01', 'title' => 'Choose a route', 'copy' => 'Compare city, duration, starting area, price, included starts, and checkpoints before buying.', 'back' => 'Every listing is structured for quick scanning so learners can pick the exact practice area they need.', 'accent' => 'from-blue-950 via-blue-700 to-cyan-400'],
                        ['step' => '02', 'title' => 'Unlock access', 'copy' => 'Purchase the map and keep the route available in your account for focused practice sessions.', 'back' => 'The checkout flow keeps student details, billing, and access limits connected to the chosen route.', 'accent' => 'from-slate-950 via-blue-800 to-sky-400'],
                        ['step' => '03', 'title' => 'Practice live', 'copy' => 'Open the route, use controlled starts, and review progress without losing sight of access usage.', 'back' => 'Live starts are counted and surfaced in the dashboard so each practice attempt is intentional.', 'accent' => 'from-indigo-950 via-blue-700 to-cyan-300'],
                    ] as $card)
                        <article class="dtr-flip-card" data-flip-card data-reveal style="--delay: {{ $loop->index * 90 }}ms;">
                            <div class="dtr-flip-inner">
                                <div class="dtr-flip-face dtr-gradient-border p-6">
                                    <span class="grid h-12 w-12 place-items-center rounded-md bg-gradient-to-br {{ $card['accent'] }} text-base font-black text-white">{{ $card['step'] }}</span>
                                    <h3 class="mt-6 text-2xl font-black text-white">{{ $card['title'] }}</h3>
                                    <p class="mt-3 text-sm leading-7 text-zinc-400">{{ $card['copy'] }}</p>
                                    <div class="mt-auto pt-6">
                                        <button type="button" class="dtr-flip-toggle" data-flip-toggle>
                                            View details
                                            <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M5 12h14" />
                                                <path d="m13 6 6 6-6 6" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="dtr-flip-face dtr-flip-back dtr-glass p-6">
                                    <span class="dtr-badge">Workflow detail</span>
                                    <h3 class="mt-6 text-2xl font-black text-white">{{ $card['title'] }}</h3>
                                    <p class="mt-3 text-sm leading-7 text-zinc-300">{{ $card['back'] }}</p>
                                    <div class="mt-auto rounded-lg border border-white/10 bg-white/[.055] p-4">
                                        <p class="text-xs font-bold uppercase text-zinc-500">Result</p>
                                        <p class="mt-2 font-black text-white">Cleaner planning, fewer surprises, better sessions.</p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="dtr-section dtr-section--routes border-t border-white/10 px-4 py-20 sm:px-6 lg:px-8">
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

        <section class="dtr-section dtr-section--cta px-4 py-16 sm:px-6 lg:px-8">
            <div class="dtr-callout dtr-gradient-border mx-auto grid max-w-7xl gap-8 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-center" data-reveal>
                <div>
                    <p class="text-sm font-black uppercase text-cyan-200">Ready when you are</p>
                    <h2 class="mt-3 max-w-3xl text-3xl font-black text-white sm:text-4xl">Start from the catalog and move into practice with a cleaner route workflow.</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-zinc-300">Compare routes, unlock the map you need, and return to your purchased practice area whenever your session starts.</p>
                </div>
                <a href="{{ route('driving-routes.index') }}" class="dtr-btn dtr-btn-primary">
                    Browse Routes
                    <svg class="dtr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </a>
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

            document.querySelectorAll('[data-city-combobox]').forEach((combobox) => {
                const input = combobox.querySelector('[data-city-input]');
                const options = Array.from(combobox.querySelectorAll('[data-city-option]'));
                const empty = combobox.querySelector('[data-city-empty]');

                function openCityPanel() {
                    combobox.classList.add('is-open');
                    input?.setAttribute('aria-expanded', 'true');
                }

                function closeCityPanel() {
                    combobox.classList.remove('is-open');
                    input?.setAttribute('aria-expanded', 'false');
                }

                function filterCities() {
                    const query = (input?.value || '').trim().toLowerCase();
                    let visibleCount = 0;

                    options.forEach((option) => {
                        const matches = !query
                            || option.dataset.cityName.includes(query)
                            || option.dataset.cityAddress.includes(query);

                        option.hidden = !matches;
                        visibleCount += matches ? 1 : 0;
                    });

                    empty?.classList.toggle('hidden', visibleCount > 0);
                    openCityPanel();
                }

                input?.addEventListener('focus', openCityPanel);
                input?.addEventListener('click', openCityPanel);
                input?.addEventListener('input', filterCities);
                input?.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeCityPanel();
                    }

                    if (event.key === 'Enter') {
                        const firstVisibleOption = options.find((option) => !option.hidden);

                        if (firstVisibleOption) {
                            event.preventDefault();
                            window.location.href = firstVisibleOption.dataset.cityUrl;
                        }
                    }
                });

                options.forEach((option) => {
                    option.addEventListener('click', () => {
                        window.location.href = option.dataset.cityUrl;
                    });
                });

                document.addEventListener('click', (event) => {
                    if (!combobox.contains(event.target)) {
                        closeCityPanel();
                    }
                });
            });

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

            moveDashboardIndicator(document.querySelector('[data-dashboard-tab].is-active'));
            window.addEventListener('resize', () => moveDashboardIndicator(document.querySelector('[data-dashboard-tab].is-active')), { passive: true });
        })();
    </script>
@endpush
