@extends('layouts.app')

@section('title', $route->title . ' - Route Details')

@push('styles')
<style>
    .route-details-page {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #eff6ff 100%);
        min-height: 100vh;
    }

    .details-card {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 1.5rem;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(16px);
    }

    .details-header {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #0284c7 100%);
        color: white;
        border-radius: 1.5rem 1.5rem 0 0;
        position: relative;
        overflow: hidden;
    }

    .details-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: -100px;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .details-header::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: -50px;
        width: 250px;
        height: 250px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .details-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-package {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .badge-active {
        background: #10b981;
        color: white;
    }

    .details-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem;
        text-align: center;
    }

    .details-stat-value {
        font-size: 2rem;
        font-weight: 900;
        color: #2563eb;
        line-height: 1;
    }

    .details-stat-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 600;
        margin-top: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .details-stat-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .details-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    }

    .details-row:last-child {
        border-bottom: none;
    }

    .details-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .details-item-icon {
        flex-shrink: 0;
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        background: linear-gradient(135deg, #dbeafe, #e0f2fe);
        color: #0284c7;
        font-size: 1.5rem;
    }

    .details-item-content h4 {
        font-size: 0.875rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .details-item-content p {
        font-size: 1.125rem;
        font-weight: 900;
        color: #0f172a;
    }

    .route-path-visualization {
        background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);
        border-radius: 1rem;
        padding: 2rem;
        margin: 1.5rem 0;
        position: relative;
        overflow: hidden;
    }

    .path-dot {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        margin: 0 0.5rem;
        font-weight: 900;
        color: white;
        font-size: 0.875rem;
    }

    .path-dot.start {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .path-dot.waypoint {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .path-dot.end {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .path-connector {
        display: inline-block;
        width: 2rem;
        height: 2px;
        background: linear-gradient(90deg, #3b82f6, transparent);
        vertical-align: middle;
        margin: 0 -0.25rem;
    }

    .waypoints-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .waypoint-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background: #f8fafc;
        border-left: 4px solid #0284c7;
    }

    .waypoint-number {
        flex-shrink: 0;
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        background: #0284c7;
        color: white;
        font-weight: 900;
    }

    .waypoint-content h5 {
        font-size: 0.875rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .waypoint-content p {
        font-size: 1rem;
        font-weight: 600;
        color: #0f172a;
    }

    .price-box {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: white;
        border-radius: 1.5rem;
        padding: 2rem;
        text-align: center;
        margin: 1.5rem 0;
    }

    .price-label {
        font-size: 0.875rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .price-value {
        font-size: 3rem;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 1rem;
    }

    .price-includes {
        font-size: 0.875rem;
        opacity: 0.85;
        line-height: 1.6;
    }

    .cta-buttons {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-top: 2rem;
    }

    @media (min-width: 640px) {
        .cta-buttons {
            grid-template-columns: 1fr 1fr;
        }
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        border-radius: 1rem;
        font-weight: 900;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-start-practice {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-start-practice:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
    }

    .btn-buy {
        background: white;
        color: #2563eb;
        border: 2px solid #2563eb;
    }

    .btn-buy:hover {
        background: #f0fdfa;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin: 2rem 0;
    }

    .feature-box {
        padding: 1.5rem;
        background: #f8fafc;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .feature-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .feature-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #0f172a;
    }

    .section-title {
        font-size: 1.875rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 3rem;
        height: 4px;
        background: linear-gradient(90deg, #1e40af, #2563eb);
        border-radius: 2px;
    }

    @media (max-width: 640px) {
        .details-stat {
            padding: 1rem;
        }

        .details-stat-value {
            font-size: 1.5rem;
        }

        .price-value {
            font-size: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="route-details-page py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">

        <!-- Breadcrumbs -->
        <nav class="mb-6 flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-teal-700 transition">Home</a>
            <span class="text-slate-400">/</span>
            <a href="{{ route('driving-routes.index') }}" class="hover:text-teal-700 transition">Routes</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-900 font-bold truncate">{{ $route->title }}</span>
        </nav>

        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('driving-routes.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 font-bold transition border border-slate-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Routes
            </a>
        </div>

        <!-- Main Details Card -->
        <div class="details-card mb-8">
            
            <!-- Header with title and badges -->
            <div class="details-header p-6 sm:p-8">
                <div class="relative z-10">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="details-badge badge-package">
                            {{ $route->package_type === 'g1' ? 'G2 Test Route' : 'G Test Route' }}
                        </span>
                        @if($route->is_active)
                            <span class="details-badge badge-active">✓ Available</span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black mb-2 leading-tight">
                        {{ $route->title }}
                    </h1>
                    <p class="text-lg font-bold opacity-90">
                        📍 {{ $route->city }}, {{ $route->province }}
                    </p>
                </div>
            </div>

            <!-- Key Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 border-b border-slate-200">
                <div class="details-stat">
                    <div class="details-stat-icon">⏱️</div>
                    <div class="details-stat-value">{{ $route->route_duration_minutes ?: '20' }}</div>
                    <div class="details-stat-label">Minutes</div>
                </div>
                <div class="details-stat">
                    <div class="details-stat-icon">📏</div>
                    <div class="details-stat-value">{{ $route->route_length_km ?: '15' }}</div>
                    <div class="details-stat-label">KM</div>
                </div>
                <div class="details-stat">
                    <div class="details-stat-icon">📍</div>
                    <div class="details-stat-value">{{ count($waypointsList) }}</div>
                    <div class="details-stat-label">Waypoints</div>
                </div>
                <div class="details-stat">
                    <div class="details-stat-icon">✅</div>
                    <div class="details-stat-value">{{ $route->access_limit }}</div>
                    <div class="details-stat-label">Starts</div>
                </div>
            </div>

        </div>

        <div class="grid gap-8 lg:grid-cols-3">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Route Overview Section -->
                <section class="details-card p-6 sm:p-8">
                    <h2 class="section-title">Route Overview</h2>

                    @if($route->route_image && file_exists(public_path($route->route_image)))
                        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200/90 shadow-md">
                            <img src="{{ asset($route->route_image) }}"
                                 alt="{{ $route->title }} - {{ $route->city }}, {{ $route->province }} Drive Test Route Map Overview"
                                 class="w-full h-auto max-h-[500px] object-cover"
                                 loading="lazy">
                        </div>
                    @endif

                    @if($route->description)
                        <p class="text-base sm:text-lg leading-relaxed text-slate-700">
                            {{ $route->description }}
                        </p>
                    @else
                        <p class="text-base leading-relaxed text-slate-600">
                            Practice the official {{ $route->title }} drive test route in {{ $route->city }}, {{ $route->province }}. Follow turn-by-turn maneuvers and pass your road test with confidence.
                        </p>
                    @endif
                </section>

                <!-- Location Details -->
                <section class="details-card p-6 sm:p-8">
                    <h2 class="section-title">Route Locations</h2>
                    
                    <div class="details-row">
                        <div class="details-item">
                            <div class="details-item-icon">🟢</div>
                            <div class="details-item-content">
                                <h4>Start Point</h4>
                                <p>{{ $route->start_label ?: 'Test Center Start' }}</p>
                            </div>
                        </div>
                        <div class="details-item">
                            <div class="details-item-icon">🔴</div>
                            <div class="details-item-content">
                                <h4>End Point</h4>
                                <p>{{ $route->destination_label ?: 'Return to Start' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Route Path Visualization -->
                @if(count($waypointsList) > 0)
                    <section class="details-card p-6 sm:p-8">
                        <h2 class="section-title">Route Path</h2>
                        
                        <div class="route-path-visualization">
                            <div class="flex flex-wrap items-center justify-center gap-1 overflow-x-auto pb-4">
                                <div class="path-dot start" title="Start">S</div>
                                @foreach($waypointsList as $idx => $waypoint)
                                    <span class="path-connector"></span>
                                    <div class="path-dot waypoint" title="Waypoint {{ $idx + 1 }}">{{ $idx + 1 }}</div>
                                @endforeach
                                <span class="path-connector"></span>
                                <div class="path-dot end" title="End">E</div>
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Features Section -->
                <section class="details-card p-6 sm:p-8">
                    <h2 class="section-title">What's Included</h2>
                    
                    <div class="feature-grid">
                        <div class="feature-box">
                            <div class="feature-icon">🗺️</div>
                            <div class="feature-label">Interactive Map</div>
                        </div>
                        <div class="feature-box">
                            <div class="feature-icon">📍</div>
                            <div class="feature-label">GPS Navigation</div>
                        </div>
                        <div class="feature-box">
                            <div class="feature-icon">🎯</div>
                            <div class="feature-label">Demo Drive Mode</div>
                        </div>
                        <div class="feature-box">
                            <div class="feature-icon">🔊</div>
                            <div class="feature-label">Voice Guidance</div>
                        </div>
                        <div class="feature-box">
                            <div class="feature-icon">📱</div>
                            <div class="feature-label">Mobile Optimized</div>
                        </div>
                        <div class="feature-box">
                            <div class="feature-icon">💾</div>
                            <div class="feature-label">{{ $route->access_limit }} Practice Starts</div>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Price Card -->
                <div class="price-box">
                    <div class="price-label">Starting Price</div>
                    <div class="price-value">${{ number_format($route->price, 2) }}</div>
                    <div class="price-includes">
                        <div class="mb-2">✓ {{ $route->access_limit }} full route starts</div>
                    </div>
                </div>

                <!-- Purchase Status -->
                @auth
                    @if($isPurchased)
                        <div class="details-card p-6 text-center">
                            <div class="text-3xl mb-2">✅</div>
                            <h3 class="font-bold text-lg text-teal-700 mb-2">Route Purchased</h3>
                            <p class="text-sm text-slate-600 mb-4">
                                You have access to this route
                            </p>
                            <a href="{{ route('driving-routes.show', $route) }}" class="btn-primary btn-start-practice w-full">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Start Practice
                            </a>
                        </div>
                    @else
                        <a href="{{ route('driving-routes.checkout', $route) }}" class="btn-primary btn-buy w-full" style="display: flex; padding: 1rem 2rem;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Buy Route Now
                        </a>
                    @endif
                @else
                    <div class="details-card p-6 text-center">
                        <p class="text-sm text-slate-600 mb-4">Sign in to purchase this route</p>
                        <a href="{{ route('login') }}" class="btn-primary btn-buy w-full" style="display: flex; padding: 1rem 2rem;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Sign In
                        </a>
                    </div>
                @endauth

                <!-- Route Info Details -->
                <div class="details-card p-6 sm:p-8">
                    <h3 class="font-bold text-lg mb-4 pb-3 border-b border-slate-200">Route Information</h3>
                    
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="font-bold text-slate-600">Package</dt>
                            <dd class="font-bold text-slate-900">{{ $route->package_type === 'g1' ? 'G2' : 'G' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-bold text-slate-600">Duration</dt>
                            <dd class="font-bold text-slate-900">{{ $route->route_duration_minutes ?: '20' }} min</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-bold text-slate-600">Distance</dt>
                            <dd class="font-bold text-slate-900">{{ $route->route_length_km ?: '15' }} km</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-bold text-slate-600">Waypoints</dt>
                            <dd class="font-bold text-slate-900">{{ count($waypointsList) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-bold text-slate-600">Starts Included</dt>
                            <dd class="font-bold text-slate-900">{{ $route->access_limit }}</dd>
                        </div>
                    </dl>
                </div>

            </div>

        </div>

    </div>
</div>

@endsection
