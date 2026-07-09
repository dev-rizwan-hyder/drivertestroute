@extends('layouts.app')

@section('title', 'Contact Driver Test Routes')

@push('styles')
    <style>
        .contact-page {
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

        .contact-glass {
            border: 1px solid rgba(203, 213, 225, .9);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            backdrop-filter: blur(16px);
        }

        .contact-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #1e40af 0%, #2563eb 44%, #0891b2 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .contact-field {
            position: relative;
        }

        .contact-input {
            width: 100%;
            border: 1px solid #cfd8e3;
            border-radius: .5rem;
            background: #ffffff;
            padding: 1.15rem .9rem .62rem;
            color: #212529;
            transition: border-color 200ms ease-out, box-shadow 200ms ease-out;
        }

        .contact-input:focus {
            border-color: rgba(37, 99, 235, .52);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: 0;
        }

        .contact-label {
            position: absolute;
            left: .9rem;
            top: .85rem;
            color: #6b7280;
            font-size: .875rem;
            font-weight: 800;
            pointer-events: none;
            transition: transform 180ms ease-out, color 180ms ease-out, font-size 180ms ease-out;
        }

        .contact-input:focus + .contact-label,
        .contact-input:not(:placeholder-shown) + .contact-label,
        select.contact-input + .contact-label {
            color: #1d4ed8;
            font-size: .72rem;
            transform: translateY(-.48rem);
        }

        .contact-button {
            position: relative;
            display: inline-flex;
            min-height: 3rem;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            background: linear-gradient(135deg, #1e40af, #2563eb 52%, #0891b2);
            padding: .85rem 1.2rem;
            color: #fff;
            font-weight: 900;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            transition: transform 200ms cubic-bezier(.16, 1, .3, 1), box-shadow 200ms ease-out;
        }

        .contact-button:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .28);
        }

        .contact-button.is-loading {
            color: transparent;
            pointer-events: none;
        }

        .contact-button.is-loading::after {
            content: "";
            position: absolute;
            height: 1.1rem;
            width: 1.1rem;
            border: 2px solid rgba(255, 255, 255, .28);
            border-top-color: #fff;
            border-radius: 999px;
            animation: contact-spin 700ms linear infinite;
        }

        .contact-checkmark {
            stroke-dasharray: 42;
            stroke-dashoffset: 42;
            animation: contact-check 620ms cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .contact-map-visual {
            background-color: #f1f3f5;
            background-image:
                linear-gradient(135deg, rgba(248, 249, 250, .64), rgba(255, 255, 255, .34), rgba(241, 243, 245, .7)),
                var(--public-image-route);
            background-position: center, center;
            background-repeat: no-repeat;
            background-size: auto, cover;
        }

        @keyframes contact-spin {
            to { transform: rotate(360deg); }
        }

        @keyframes contact-check {
            to { stroke-dashoffset: 0; }
        }
    </style>
@endpush

@section('content')
    <div class="contact-page">
        <section class="mx-auto grid max-w-7xl gap-8 px-4 py-20 sm:px-6 lg:grid-cols-[1fr_.85fr] lg:px-8">
            <div>
                <p class="text-sm font-black uppercase text-cyan-200">Contact Us</p>
                <h1 class="mt-4 text-5xl font-black leading-tight text-white sm:text-6xl">
                    Questions about routes,
                    <span class="contact-gradient-text block">access, or coverage?</span>
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-400">
                    Send a message with the city or route name when relevant. We will review route availability, purchase access, and account questions with the right context.
                </p>

                @if(session('success'))
                    <div class="contact-glass mt-8 flex items-start gap-4 p-5">
                        <svg class="mt-1 h-8 w-8 shrink-0 text-cyan-200" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                            <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="3" opacity=".35" />
                            <path class="contact-checkmark" d="M15 24.5 21.5 31 34 17" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div>
                            <h2 class="font-black text-white">Message received</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-400">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="contact-glass mt-8 p-5" data-contact-form>
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="contact-field block">
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder=" " class="contact-input">
                            <span class="contact-label">Name</span>
                            @error('name') <span class="mt-1 block text-xs font-semibold text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <label class="contact-field block">
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder=" " class="contact-input">
                            <span class="contact-label">Email</span>
                            @error('email') <span class="mt-1 block text-xs font-semibold text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <label class="contact-field block sm:col-span-2">
                            <select name="topic" required class="contact-input">
                                <option value="">Choose a topic</option>
                                @foreach(['Route purchase', 'Map access', 'Coverage request', 'Account help', 'Other'] as $topic)
                                    <option value="{{ $topic }}" @selected(old('topic') === $topic)>{{ $topic }}</option>
                                @endforeach
                            </select>
                            <span class="contact-label">Topic</span>
                            @error('topic') <span class="mt-1 block text-xs font-semibold text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <label class="contact-field block sm:col-span-2">
                            <textarea name="message" rows="6" required placeholder=" " class="contact-input">{{ old('message') }}</textarea>
                            <span class="contact-label">Message</span>
                            @error('message') <span class="mt-1 block text-xs font-semibold text-red-700">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <button type="submit" class="contact-button mt-6 w-full sm:w-auto">Send Message</button>
                </form>
            </div>

            <aside class="space-y-5">
                <div class="contact-glass p-5">
                    <h2 class="text-xl font-black text-white">Support Details</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="font-black text-cyan-200">Office hours</dt>
                            <dd class="mt-1 text-slate-400">Monday to Friday, 9:00 AM - 5:00 PM</dd>
                        </div>
                        <div>
                            <dt class="font-black text-cyan-200">Support email</dt>
                            <dd class="mt-1 text-slate-400">support@drivertestroutes.test</dd>
                        </div>
                        <div>
                            <dt class="font-black text-cyan-200">Phone</dt>
                            <dd class="mt-1 text-slate-400">+1 (555) 010-4477</dd>
                        </div>
                    </dl>
                </div>

                <div class="contact-glass overflow-hidden">
                    <div class="contact-map-visual relative h-80">
                        <svg class="h-full w-full" viewBox="0 0 460 320" fill="none" aria-hidden="true">
                            <path d="M0 72H460M0 144H460M0 216H460M0 288H460M76 0V320M153 0V320M230 0V320M307 0V320M384 0V320" stroke="rgba(148,163,184,.16)" />
                            <path d="M52 250 C110 152 168 198 224 110 C282 18 348 96 410 54" stroke="url(#contactMapRoute)" stroke-width="8" stroke-linecap="round" />
                            <circle cx="52" cy="250" r="11" fill="#38bdf8" />
                            <circle cx="410" cy="54" r="11" fill="#2563eb" />
                            <defs>
                                <linearGradient id="contactMapRoute" x1="52" x2="410" y1="250" y2="54">
                                    <stop stop-color="#1e3a8a" />
                                    <stop offset=".55" stop-color="#2563eb" />
                                    <stop offset="1" stop-color="#06b6d4" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div class="border-t border-white/10 p-5">
                        <h2 class="font-black text-white">Coverage requests</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-400">Tell us which city, province, or test area you want added next.</p>
                    </div>
                </div>
            </aside>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelector('[data-contact-form]')?.addEventListener('submit', (event) => {
            const button = event.currentTarget.querySelector('.contact-button');
            button?.classList.add('is-loading');
            button?.setAttribute('aria-busy', 'true');
        });
    </script>
@endpush
