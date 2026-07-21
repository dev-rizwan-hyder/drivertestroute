@extends('layouts.app')

@section('title', 'Checkout - '.$drivingRoute->title)

@push('styles')
    <style>
        .checkout-page {
            min-height: calc(100vh - 5rem);
            background-color: #f8f9fa;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .07), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .05), transparent 30%),
                linear-gradient(180deg, rgba(248, 249, 250, .9), rgba(241, 243, 245, .94) 48%, rgba(248, 249, 250, .96)),
                var(--public-image-pages);
            background-position: center, center, center, center top;
            background-repeat: no-repeat;
            background-size: auto, auto, auto, cover;
        }

        .checkout-route-head {
            background-color: #f1f3f5;
            background-image:
                linear-gradient(135deg, rgba(248, 249, 250, .78), rgba(255, 255, 255, .42), rgba(241, 243, 245, .82)),
                var(--public-image-route);
            background-position: center, center;
            background-repeat: no-repeat;
            background-size: auto, cover;
        }

        .premium-card {
            border: 1px solid rgba(228, 228, 231, 0.8);
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            backdrop-filter: blur(16px);
        }

        .checkout-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            background-color: #ffffff;
            padding: 0.65rem 0.85rem;
            color: #0f172a;
            font-size: 0.925rem;
            font-weight: 500;
            transition: all 180ms ease-out;
        }
        .checkout-input:hover {
            border-color: #94a3b8;
        }
        .checkout-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .payment-method-btn {
            border: 2px solid #e2e8f0;
            transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .payment-method-btn:hover {
            border-color: #3b82f6;
            background-color: rgba(239, 246, 255, 0.4);
            transform: translateY(-1px);
        }
        .payment-method-btn.selected-gateway {
            border-color: #2563eb;
            background-color: #f0f7ff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
        }

        /* Terms Modal Styling */
        .terms-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            background-color: rgba(9, 9, 11, 0.6);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 250ms ease-out;
        }
        .terms-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .terms-modal-container {
            background-color: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 1.25rem;
            max-width: 44rem;
            width: calc(100% - 2rem);
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.95) translateY(10px);
            transition: transform 250ms cubic-bezier(0.16, 1, 0.3, 1);
        }
        .terms-modal-overlay.active .terms-modal-container {
            transform: scale(1) translateY(0);
        }
    </style>
@endpush

@section('content')
    @php
        $startsIncluded = max(1, (int) ($drivingRoute->access_limit ?? 1));
        $remainingStarts = $purchase?->remainingStarts() ?? 0;
        $totalAfterPurchase = $remainingStarts + $startsIncluded;
    @endphp

    <div class="checkout-page py-10">
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-zinc-200 pb-6">
            <div>
                <a href="{{ route('driving-routes.index') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-600 hover:text-blue-700 transition">
                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to routes
                </a>
                <h1 class="mt-3 text-3xl font-black text-zinc-900 tracking-tight">Secure Checkout</h1>
                <p class="mt-1 text-sm text-zinc-500">Confirm student details, review route access, and complete payment to unlock the map.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3.5 py-1.5 text-xs font-bold text-blue-700">
                <span class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                {{ $stripeEnabled ? 'Stripe Card Payment' : 'Local Checkout Mode' }}
            </span>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_400px]">
            
            <!-- Left Column: Forms and Info -->
            <div class="space-y-6">
                
                <!-- Selected Route Overview Card -->
                <article class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm">
                    <div class="checkout-route-head border-b border-zinc-200 p-6">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded bg-emerald-100 px-2 py-0.5 text-xxs font-extrabold uppercase tracking-wider text-emerald-800">Active Selection</span>
                        </div>
                        <h2 class="mt-2.5 text-2xl font-black text-zinc-950 tracking-tight">{{ $drivingRoute->title }}</h2>
                        <p class="mt-1.5 text-sm text-zinc-500 font-bold flex items-center gap-1">
                            <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            {{ $drivingRoute->city }}, {{ $drivingRoute->province }}
                        </p>
                    </div>

                    <div class="grid gap-4 p-6 sm:grid-cols-2 bg-zinc-50/50">
                        <div class="rounded-xl border border-zinc-200/60 bg-white p-4">
                            <div class="text-xs font-bold uppercase tracking-wider text-zinc-400">Start Location</div>
                            <div class="mt-1.5 font-extrabold text-zinc-900">{{ $drivingRoute->start_label ?: 'Start Point' }}</div>
                        </div>
                        <div class="rounded-xl border border-zinc-200/60 bg-white p-4">
                            <div class="text-xs font-bold uppercase tracking-wider text-zinc-400">Midpoint / Destination</div>
                            <div class="mt-1.5 font-extrabold text-zinc-900">{{ $drivingRoute->destination_label ?: 'Midpoint' }}</div>
                        </div>
                        <div class="rounded-xl border border-zinc-200/60 bg-white p-4">
                            <div class="text-xs font-bold uppercase tracking-wider text-zinc-400">Included Map Starts</div>
                            <div class="mt-1.5 font-extrabold text-zinc-900">{{ $startsIncluded }} Drive Starts</div>
                        </div>
                        <div class="rounded-xl border border-zinc-200/60 bg-white p-4">
                            <div class="text-xs font-bold uppercase tracking-wider text-zinc-400">Total Starts Available</div>
                            <div class="mt-1.5 font-extrabold text-zinc-900">{{ $totalAfterPurchase }} starts available</div>
                        </div>
                    </div>
                </article>

                <form id="checkout-form" method="POST" action="{{ route('driving-routes.checkout.store', $drivingRoute) }}" class="space-y-6">
                    @csrf
                    <input id="payment-intent-id" type="hidden" name="payment_intent_id" value="{{ old('payment_intent_id') }}">
                    <input id="payment-provider" type="hidden" name="payment_provider" value="{{ $stripeEnabled ? 'stripe' : ($paypalEnabled ? 'paypal' : ($squareEnabled ? 'square' : 'local')) }}">

                    <!-- Student Details -->
                    <section class="premium-card p-6 sm:p-8 border-l-4 border-l-blue-600">
                        <div class="mb-6">
                            <h2 class="text-lg font-black text-zinc-900 tracking-tight">Student Details</h2>
                            <p class="mt-1 text-xs text-zinc-500">Provide details of the student who will access the route maps.</p>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2 grid gap-5 sm:grid-cols-2">
                                <label class="block">
                                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Student Full Name</span>
                                    <input type="text" name="student_name" value="{{ old('student_name', auth()->user()->name) }}" required class="checkout-input">
                                </label>

                                <label class="block">
                                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Student Email</span>
                                    <input type="email" name="student_email" value="{{ old('student_email', auth()->user()->email) }}" required class="checkout-input">
                                </label>
                            </div>

                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Student Phone</span>
                                <input type="tel" name="student_phone" value="{{ old('student_phone') }}" required class="checkout-input" placeholder="(555) 000-0000">
                            </label>

                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">City / Test Area</span>
                                <input type="text" name="student_city" value="{{ old('student_city', $drivingRoute->city) }}" class="checkout-input">
                            </label>

                            <label class="block sm:col-span-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Expected Test Date</span>
                                <input type="date" name="student_test_date" value="{{ old('student_test_date') }}" class="checkout-input">
                            </label>

                            <label class="block sm:col-span-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Notes / Special Instructions</span>
                                <textarea name="student_notes" rows="3" class="checkout-input" placeholder="Any specific notes or details for practice..."></textarea>
                            </label>
                        </div>
                    </section>

                    <!-- Payment Method Selector -->
                    <section class="premium-card p-6 sm:p-8 border-l-4 border-l-indigo-600">
                        <div class="mb-6">
                            <h2 class="text-lg font-black text-zinc-900 tracking-tight">Payment Method</h2>
                            <p class="mt-1 text-xs text-zinc-500">Select your preferred secure gateway for checkout.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            @if($stripeEnabled)
                                <label class="payment-method-btn relative flex cursor-pointer flex-col items-center justify-center rounded-xl p-5 text-center bg-white shadow-sm @if($stripeEnabled) selected-gateway @endif">
                                    <input type="radio" name="payment_provider_select" value="stripe" class="sr-only" checked>
                                    <svg class="h-6 w-6 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="5" width="20" height="14" rx="2" />
                                        <line x1="2" y1="10" x2="22" y2="10" />
                                    </svg>
                                    <span class="block text-sm font-bold text-zinc-900">Stripe Card</span>
                                    <span class="mt-1 block text-xxs text-zinc-400">Credit/Debit card securely via Stripe</span>
                                </label>
                            @endif

                            @if($paypalEnabled)
                                <label class="payment-method-btn relative flex cursor-pointer flex-col items-center justify-center rounded-xl p-5 text-center bg-white shadow-sm @if(!$stripeEnabled) selected-gateway @endif">
                                    <input type="radio" name="payment_provider_select" value="paypal" class="sr-only" @if(!$stripeEnabled) checked @endif>
                                    <svg class="h-6 w-6 text-amber-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="block text-sm font-bold text-zinc-900">PayPal</span>
                                    <span class="mt-1 block text-xxs text-zinc-400">Express PayPal balance or credit cards</span>
                                </label>
                            @endif

                            @if($squareEnabled)
                                <label class="payment-method-btn relative flex cursor-pointer flex-col items-center justify-center rounded-xl p-5 text-center bg-white shadow-sm @if(!$stripeEnabled && !$paypalEnabled) selected-gateway @endif">
                                    <input type="radio" name="payment_provider_select" value="square" class="sr-only" @if(!$stripeEnabled && !$paypalEnabled) checked @endif>
                                    <svg class="h-6 w-6 text-zinc-800 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <rect x="7" y="7" width="10" height="10" />
                                    </svg>
                                    <span class="block text-sm font-bold text-zinc-900">Square Card</span>
                                    <span class="mt-1 block text-xxs text-zinc-400">Fast checkout integration via Square</span>
                                </label>
                            @endif
                        </div>
                    </section>

                    <!-- Billing & Payment Details -->
                    <section class="premium-card p-6 sm:p-8 border-l-4 border-l-emerald-600">
                        <div class="mb-6">
                            <h2 class="text-lg font-black text-zinc-900 tracking-tight">Billing & Payment Details</h2>
                            <p class="mt-1 text-xs text-zinc-500">Your billing credentials will not be stored on our servers. All transactions are fully encrypted.</p>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Billing Name</span>
                                <input type="text" name="billing_name" value="{{ old('billing_name', auth()->user()->name) }}" required class="checkout-input">
                            </label>

                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Billing Email</span>
                                <input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()->email) }}" required class="checkout-input">
                            </label>
                        </div>

                        <!-- Stripe Card Container -->
                        @if($stripeEnabled)
                            <div id="stripe-payment-container" class="payment-details-container mt-6">
                                <label class="block">
                                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Card Details</span>
                                    <div id="card-element" class="checkout-input shadow-inner min-h-[46px] flex items-center"></div>
                                </label>
                                <p id="card-errors" class="mt-3 hidden rounded-lg border border-red-200 bg-red-55 px-4 py-2.5 text-xs font-bold text-red-700"></p>
                                <p class="mt-3 text-xxs leading-relaxed text-zinc-400">For testing purposes, you can use the card number <span class="font-bold text-zinc-500">4242 4242 4242 4242</span> with any future expiration date and any CVC code.</p>
                            </div>
                        @endif

                        <!-- PayPal Button Container -->
                        @if($paypalEnabled)
                            <div id="paypal-payment-container" class="payment-details-container mt-6 hidden">
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-2">PayPal Transaction Summary</span>
                                <div id="paypal-button-container" class="mt-1 min-h-[50px] overflow-hidden rounded-lg"></div>
                                <p id="paypal-errors" class="mt-3 hidden rounded-lg border border-red-200 bg-red-55 px-4 py-2.5 text-xs font-bold text-red-700"></p>
                            </div>
                        @endif

                        <!-- Square Card Container -->
                        @if($squareEnabled)
                            <div id="square-payment-container" class="payment-details-container mt-6 hidden">
                                <label class="block">
                                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 block mb-1.5">Card Details</span>
                                    <div id="square-card-container" class="checkout-input shadow-inner min-h-[46px]"></div>
                                </label>
                                <p id="square-errors" class="mt-3 hidden rounded-lg border border-red-200 bg-red-55 px-4 py-2.5 text-xs font-bold text-red-700"></p>
                            </div>
                        @endif
                    </section>
                </form>
            </div>

            <!-- Right Column: Summary & Place Order -->
            <div class="space-y-6">
                
                <!-- Order Summary Card -->
                <aside class="premium-card p-6 sm:p-8 h-fit lg:sticky lg:top-24">
                    <h2 class="text-lg font-black text-zinc-900 tracking-tight pb-4 border-b border-zinc-200/80">Order Summary</h2>

                    <dl class="mt-6 space-y-4 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-zinc-500 font-medium">Route Price</dt>
                            <dd class="font-extrabold text-zinc-950">${{ number_format((float) $drivingRoute->price, 2) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-zinc-500 font-medium">Currency</dt>
                            <dd class="font-extrabold text-zinc-950">{{ $stripeCurrency }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-zinc-500 font-medium">Map Starts Included</dt>
                            <dd class="font-extrabold text-zinc-950">{{ $startsIncluded }} Drive Starts</dd>
                        </div>
                        @if($purchase)
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-zinc-500 font-medium">Current Starts Remaining</dt>
                                <dd class="font-extrabold text-zinc-950">{{ $remainingStarts }} starts</dd>
                            </div>
                        @endif
                        <div class="border-t border-zinc-200 pt-4 mt-2">
                            <div class="flex items-end justify-between gap-4">
                                <dt class="text-sm font-bold text-zinc-900 mb-1">Total Due</dt>
                                <dd class="text-3xl font-black text-zinc-950 leading-none">${{ number_format((float) $drivingRoute->price, 2) }}</dd>
                            </div>
                        </div>
                    </dl>

                    <div class="mt-6 rounded-xl bg-zinc-50 border border-zinc-200/60 p-4 text-xs">
                        <div class="font-bold text-zinc-900 flex items-center gap-1.5">
                            <svg class="h-4.5 w-4.5 text-blue-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            Instant Map Unlock
                        </div>
                        <p class="mt-1.5 leading-relaxed text-zinc-500">Upon confirmation, the route map displays immediately in your dashboard. Repurchasing adds extra map starts directly to your profile.</p>
                    </div>

                    <!-- Terms Acceptance Checkbox (High Standards Styling) -->
                    <div class="mt-6">
                        <label class="relative flex items-start gap-3 rounded-xl border border-zinc-200 bg-zinc-50/60 p-4 transition-all hover:bg-zinc-50 cursor-pointer">
                            <input type="checkbox" name="terms" value="1" form="checkout-form" required class="mt-1 h-4 w-4 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500" @checked(old('terms'))>
                            <span class="text-xs leading-5 text-zinc-600 font-semibold">
                                I have read and agree to the <button type="button" onclick="openTermsModal()" class="underline font-black text-blue-600 hover:text-blue-700 transition focus:outline-none">Terms and Conditions</button>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button id="checkout-submit" type="submit" form="checkout-form" class="mt-5 w-full rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3.5 px-6 font-bold text-sm tracking-wide shadow-md hover:shadow-lg transition-all duration-200 transform active:scale-[0.99] disabled:cursor-not-allowed disabled:from-zinc-400 disabled:to-zinc-400 disabled:shadow-none">
                        Pay ${{ number_format((float) $drivingRoute->price, 2) }} & Unlock Route
                    </button>

                    <!-- Alert message container -->
                    <p id="checkout-status" class="mt-3.5 hidden rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-xs font-bold text-zinc-700"></p>
                </aside>
            </div>
        </div>
    </section>
    </div>

    <!-- Inline Terms and Conditions Modal Overlay -->
    <div id="terms-modal" class="terms-modal-overlay">
        <div class="terms-modal-container">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 bg-zinc-50/80 rounded-t-2xl">
                <div>
                    <h3 class="text-base font-black text-zinc-950">Terms & Conditions of Sale</h3>
                    <p class="text-xxs text-zinc-400 font-semibold uppercase mt-0.5 tracking-wider">Effective Date: July 19, 2026</p>
                </div>
                <button type="button" onclick="closeTermsModal()" class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-150 hover:text-zinc-700 focus:outline-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-5 text-xs leading-relaxed text-zinc-600">
                
                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">1. Digital Product</h4>
                    <p>Our products consist of digital navigation routes and maps designed to help customers become familiar with areas commonly used during Ontario G2 and G road tests. <strong>This is a digital product only. No physical product will be shipped.</strong></p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">2. One-Time Access</h4>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Your purchase provides one-time access to the digital navigation route.</li>
                        <li>Access is intended for the purchaser only.</li>
                        <li>Once the route has been accessed, it is considered delivered and used.</li>
                        <li>You may take screenshots for your personal reference during your one-time access.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">3. Username and Password</h4>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Access is provided using a unique username and password.</li>
                        <li>You are responsible for keeping your login credentials secure.</li>
                        <li>Sharing, reselling, or distributing credentials or purchased content is strictly prohibited.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">4. Educational Purpose Only</h4>
                    <p>Our routes are created through our own research, observations, customer feedback, and public data. They are study aids. <strong>The routes are not official DriveTest routes and are not endorsed, approved, or affiliated with DriveTest, the MTO, Serco, or any government agency.</strong></p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">5. Route Accuracy</h4>
                    <p>While we make reasonable efforts to provide accurate navigation routes, we cannot guarantee the exact route will be used on test day. DriveTest examiners may change streets, modify routes, avoid traffic/construction, or respond to road closures/weather.</p>
                    <p class="mt-1 font-bold text-zinc-800">Estimates: G2 Route test within ~2-3 km; G Route test within ~4-5 km of the Centre.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">6. No Guarantee of Examination Route</h4>
                    <p>We do not guarantee the examiner will follow the route, that streets or maneuvers will match, or that the route results in a passing grade. Success depends entirely on your driving ability.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">7. No Responsibility for Route Changes</h4>
                    <p>Ontario Services and 5036721 Ontario Inc. accept no responsibility if a DriveTest examiner selects a different route, changes streets, adds/removes turns, or follows any other approved testing course.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5 font-bold text-red-600">8. No Refund Policy</h4>
                    <p class="font-bold text-zinc-900">Due to the nature of digital products, all sales are final. No refunds, exchanges, or cancellations will be provided after purchase.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">9. Chargebacks and Payment Disputes</h4>
                    <p>You agree not to initiate a chargeback or payment dispute on the basis of: lack of knowledge, misunderstanding of description, examiner choosing a different route, failing the test, or route variations.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">10. Intellectual Property</h4>
                    <p>All maps, routes, and digital content are the intellectual property of 5036721 Ontario Inc. Copying, distributing, selling, publishing, sharing, or commercial use is strictly prohibited.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">11. Limitation of Liability</h4>
                    <p>To the maximum extent permitted by law, Ontario Services and 5036721 Ontario Inc. shall not be liable for: test failure, route changes, navigation differences, traffic delays, or any indirect/consequential damages.</p>
                </div>

                <div>
                    <h4 class="font-extrabold text-zinc-950 text-sm mb-1.5">12. Acceptance of Terms</h4>
                    <p>By completing purchase or accessing the route, you confirm you have read, understood, and accept all terms, including the No Refund Policy, examiner route changes, and educational nature of the product.</p>
                </div>

                <div class="border-t border-zinc-150 pt-3">
                    <p class="font-extrabold text-zinc-900">Ontario Services – 5036721 Ontario Inc.</p>
                    <p>30 Eglinton Avenue West, Unit 400, Mississauga, ON L5R 3E7, Canada</p>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="flex items-center justify-between border-t border-zinc-200 px-6 py-4 bg-zinc-50/80 rounded-b-2xl">
                <a href="{{ route('terms') }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                    Open Dedicated Page
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                </a>
                <button type="button" onclick="closeTermsModal()" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition">
                    I Understand
                </button>
            </div>
        </div>
    </div>

    <!-- Payment SDKs -->
    @if($stripeEnabled)
        <script src="https://js.stripe.com/v3/"></script>
    @endif

    @if($paypalEnabled)
        <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency={{ $paypalCurrency }}"></script>
    @endif

    @if($squareEnabled)
        @if($squareEnv === 'sandbox')
            <script src="https://sandbox.web.squareupsandbox.com/v2/paymentform"></script>
        @else
            <script src="https://web.squareupsandbox.com/v2/paymentform"></script>
        @endif
    @endif

    <script>
        const checkoutForm = document.getElementById('checkout-form');
        const checkoutButton = document.getElementById('checkout-submit');
        const checkoutStatus = document.getElementById('checkout-status');
        const paymentIntentInput = document.getElementById('payment-intent-id');
        const providerInput = document.getElementById('payment-provider');
        const providerSelects = document.querySelectorAll('input[name="payment_provider_select"]');

        function openTermsModal() {
            const modal = document.getElementById('terms-modal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeTermsModal() {
            const modal = document.getElementById('terms-modal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close on clicking outside container
        document.getElementById('terms-modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('terms-modal')) {
                closeTermsModal();
            }
        });

        function setCheckoutMessage(message, error = false) {
            let target = checkoutStatus;
            
            // Redirect error to active gateway element if exists
            const currentProvider = providerInput.value;
            if (error) {
                const gatewayErrors = document.getElementById(`${currentProvider}-errors`);
                if (gatewayErrors) {
                    target = gatewayErrors;
                } else {
                    const stripeErrors = document.getElementById('card-errors');
                    if (stripeErrors) target = stripeErrors;
                }
            }
            
            target.textContent = message;
            target.classList.remove('hidden');
            
            // Clean up other errors if success message
            if (!error) {
                document.querySelectorAll('[id$="-errors"]').forEach(el => el.classList.add('hidden'));
            }
        }

        function setCheckoutLoading(loading) {
            checkoutButton.disabled = loading;
            checkoutButton.textContent = loading ? 'Processing payment...' : 'Pay ${{ number_format((float) $drivingRoute->price, 2) }} & Unlock Route';
        }

        // Dynamic gateway selector styling and switching
        function switchPaymentProvider(provider) {
            providerInput.value = provider;
            
            // Hide all payment containers
            document.querySelectorAll('.payment-details-container').forEach(el => el.classList.add('hidden'));
            
            // Show selected container
            const container = document.getElementById(`${provider}-payment-container`);
            if (container) {
                container.classList.remove('hidden');
            }
            
            // Control primary submit button display
            if (provider === 'paypal') {
                checkoutButton.classList.add('hidden');
            } else {
                checkoutButton.classList.remove('hidden');
            }
        }

        providerSelects.forEach(radio => {
            radio.addEventListener('change', (e) => {
                // Remove selected styles from all labels
                providerSelects.forEach(r => {
                    const label = r.closest('.payment-method-btn');
                    if (label) {
                        label.classList.remove('selected-gateway');
                    }
                });
                
                if (e.target.checked) {
                    const label = e.target.closest('.payment-method-btn');
                    if (label) {
                        label.classList.add('selected-gateway');
                    }
                    switchPaymentProvider(e.target.value);
                }
            });
            
            // Trigger load setup
            if (radio.checked) {
                radio.dispatchEvent(new Event('change'));
            }
        });

        // Initialize Stripe if enabled
        let stripe, card;
        @if($stripeEnabled)
            const cardErrors = document.getElementById('card-errors');
            stripe = Stripe(@json($stripeKey));
            const elements = stripe.elements();
            card = elements.create('card', {
                style: {
                    base: {
                        color: '#0f172a',
                        fontFamily: 'ui-sans-serif, system-ui, sans-serif',
                        fontSize: '15px',
                        '::placeholder': { color: '#94a3b8' },
                    },
                    invalid: { color: '#b91c1c' },
                },
            });
            card.mount('#card-element');
        @endif

        // Initialize PayPal if enabled
        @if($paypalEnabled)
            const paypalErrors = document.getElementById('paypal-errors');
            paypal.Buttons({
                onClick: function(data, actions) {
                    if (!checkoutForm.reportValidity()) {
                        return actions.reject();
                    }
                },
                createOrder: async function(data, actions) {
                    try {
                        const formData = new FormData(checkoutForm);
                        const response = await fetch(@json(route('driving-routes.paypal.create-order', $drivingRoute)), {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                            },
                            credentials: 'same-origin',
                            body: formData
                        });

                        const payload = await response.json();
                        if (!response.ok) {
                            throw new Error(payload.message || 'PayPal order creation failed.');
                        }

                        return payload.id;
                    } catch (error) {
                        paypalErrors.textContent = error.message;
                        paypalErrors.classList.remove('hidden');
                        throw error;
                    }
                },
                onApprove: async function(data, actions) {
                    paypalErrors.classList.add('hidden');
                    paymentIntentInput.value = data.orderID;
                    setCheckoutMessage('Payment authorized. Completing checkout...');
                    checkoutForm.submit();
                },
                onError: function(err) {
                    paypalErrors.textContent = 'PayPal transaction error occurred.';
                    paypalErrors.classList.remove('hidden');
                    console.error(err);
                }
            }).render('#paypal-button-container');
        @endif

        // Initialize Square if enabled
        let squarePayments, squareCard;
        @if($squareEnabled)
            const squareErrors = document.getElementById('square-errors');
            async function initializeSquare() {
                try {
                    squarePayments = Square.payments(@json($squareAppId), @json($squareLocationId));
                    squareCard = await squarePayments.card({
                        style: {
                            input: {
                                color: '#0f172a',
                                fontSize: '15px',
                                fontFamily: 'ui-sans-serif, system-ui, sans-serif',
                            },
                            'input::placeholder': {
                                color: '#94a3b8'
                            }
                        }
                    });
                    await squareCard.attach('#square-card-container');
                } catch (error) {
                    console.error('Square initialization failed', error);
                    squareErrors.textContent = 'Square Card elements failed to load.';
                    squareErrors.classList.remove('hidden');
                }
            }
            initializeSquare();
        @endif

        // Form Submit Handler
        checkoutForm.addEventListener('submit', async (event) => {
            const currentProvider = providerInput.value;

            if (paymentIntentInput.value) {
                return; // Payment already authorized, allow form submission
            }

            // Local mode bypass
            if (currentProvider === 'local') {
                return;
            }

            event.preventDefault();

            if (!checkoutForm.reportValidity()) {
                return;
            }

            setCheckoutLoading(true);

            if (currentProvider === 'stripe') {
                setCheckoutMessage('Creating secure payment...');
                try {
                    const formData = new FormData(checkoutForm);
                    const intentResponse = await fetch(@json(route('driving-routes.payment-intent', $drivingRoute)), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                        },
                        credentials: 'same-origin',
                        body: formData,
                    });

                    const intentPayload = await intentResponse.json();

                    if (!intentResponse.ok) {
                        const firstError = intentPayload.errors ? Object.values(intentPayload.errors).flat()[0] : intentPayload.message;
                        throw new Error(firstError || 'Payment could not be started.');
                    }

                    setCheckoutMessage('Confirming card payment...');

                    const result = await stripe.confirmCardPayment(intentPayload.client_secret, {
                        payment_method: {
                            card,
                            billing_details: {
                                name: checkoutForm.billing_name.value,
                                email: checkoutForm.billing_email.value,
                                phone: checkoutForm.student_phone.value,
                            },
                        },
                    });

                    if (result.error) {
                        throw new Error(result.error.message || 'Card payment failed.');
                    }

                    paymentIntentInput.value = blockPaymentIntentId(result.paymentIntent.id);
                    setCheckoutMessage('Payment confirmed. Unlocking your route...');
                    checkoutForm.submit();
                } catch (error) {
                    setCheckoutMessage(error.message, true);
                    setCheckoutLoading(false);
                }
            } else if (currentProvider === 'square') {
                setCheckoutMessage('Tokenizing card details...');
                try {
                    const result = await squareCard.tokenize();
                    if (result.status === 'OK') {
                        paymentIntentInput.value = result.token;
                        setCheckoutMessage('Card authorized. Completing checkout...');
                        checkoutForm.submit();
                    } else {
                        let tokenizationError = 'Card tokenization failed.';
                        if (result.errors && result.errors.length > 0) {
                            tokenizationError = result.errors[0].message;
                        }
                        throw new Error(tokenizationError);
                    }
                } catch (error) {
                    setCheckoutMessage(error.message, true);
                    setCheckoutLoading(false);
                }
            }
        });

        // Helper function to bypass block if mock
        function blockPaymentIntentId(id) {
            return id || 'pi_mock_' + Math.random().toString(36).substring(2);
        }
    </script>
@endsection
