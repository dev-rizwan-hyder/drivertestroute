@extends('layouts.app')

@section('title', 'Terms and Conditions of Sale')

@push('styles')
    <style>
        .public-dark-page {
            background-color: #f8f9fa;
            color: #212529;
        }

        .terms-hero {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            background-color: #f1f3f5;
            border-bottom: 1px solid #e4e4e7;
        }

        .public-glass-card {
            border: 1px solid rgba(203, 213, 225, .9);
            border-radius: .75rem;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            backdrop-filter: blur(16px);
            transition: all 240ms cubic-bezier(.16, 1, .3, 1);
        }

        .public-glass-card:hover {
            border-color: rgba(37, 99, 235, .28);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .08);
        }

        .public-gradient-text {
            color: transparent;
            background: linear-gradient(100deg, #1e40af 0%, #2563eb 44%, #0891b2 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .sidebar-link {
            transition: all 180ms ease-out;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #2563eb;
            background-color: rgba(37, 99, 235, 0.05);
            transform: translateX(4px);
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

        .section-badge {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            color: #1e40af;
        }

        @media (prefers-reduced-motion: reduce) {
            .public-glass-card,
            [data-page-reveal] {
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
    <div class="public-dark-page">
        <!-- Hero Header -->
        <section class="terms-hero">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="max-w-4xl" data-page-reveal>
                    <p class="text-xs font-black uppercase tracking-wider text-blue-600">Legal Documents</p>
                    <h1 class="mt-4 text-4xl font-black leading-tight text-zinc-950 sm:text-5xl">
                        Terms & Conditions <span class="public-gradient-text">of Sale</span>
                    </h1>
                    <p class="mt-4 text-sm text-zinc-500 font-semibold">
                        Effective Date: July 19, 2026
                    </p>
                    <p class="mt-6 max-w-3xl text-base leading-7 text-zinc-600">
                        These Terms and Conditions govern the purchase and use of digital drive test route navigation products provided by <a href="https://www.drivetestroute.com" class="text-blue-600 hover:underline">www.drivetestroute.com</a> (Ontario Services – 5036721 Ontario Inc.). By purchasing, accessing, or using our digital products, you acknowledge that you have read, understood, and agree to be bound by these Terms.
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content Area -->
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[280px_1fr]">
                
                <!-- Sticky Navigation Sidebar -->
                <aside class="hidden lg:block">
                    <div class="sticky top-28 space-y-1 rounded-xl border border-zinc-200/80 bg-white/70 p-4 shadow-sm backdrop-blur-md">
                        <p class="px-3 mb-3 text-xs font-bold uppercase tracking-wider text-zinc-400">Navigation</p>
                        @foreach([
                            ['1', 'Digital Product', '#sec-1'],
                            ['2', 'One-Time Access', '#sec-2'],
                            ['3', 'Username & Password', '#sec-3'],
                            ['4', 'Educational Purpose', '#sec-4'],
                            ['5', 'Route Accuracy', '#sec-5'],
                            ['6', 'No Route Guarantee', '#sec-6'],
                            ['7', 'Route Changes', '#sec-7'],
                            ['8', 'No Refund Policy', '#sec-8'],
                            ['9', 'Chargebacks & Disputes', '#sec-9'],
                            ['10', 'Intellectual Property', '#sec-10'],
                            ['11', 'Limitation of Liability', '#sec-11'],
                            ['12', 'Acceptance of Terms', '#sec-12'],
                            ['', 'Contact Details', '#contact-details'],
                        ] as $item)
                            <a href="{{ $item[2] }}" class="sidebar-link flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-bold text-zinc-600 hover:text-blue-600">
                                @if($item[0])
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-[10px] font-black text-zinc-500">{{ $item[0] }}</span>
                                @endif
                                <span class="truncate">{{ $item[1] }}</span>
                            </a>
                        @endforeach
                    </div>
                </aside>

                <!-- Detailed Content -->
                <div class="space-y-6">
                    
                    <!-- Section 1 -->
                    <article id="sec-1" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">1</span>
                            <h2 class="text-xl font-bold text-zinc-950">Digital Product</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-3">
                            <p>Our products consist of digital navigation routes and maps designed to help customers become familiar with areas commonly used during Ontario G2 and G road tests.</p>
                            <p class="font-bold text-zinc-800">This is a digital product only. No physical product will be shipped.</p>
                        </div>
                    </article>

                    <!-- Section 2 -->
                    <article id="sec-2" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">2</span>
                            <h2 class="text-xl font-bold text-zinc-950">One-Time Access</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600">
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Your purchase provides one-time access to the digital navigation route.</li>
                                <li>Access is intended for the purchaser only.</li>
                                <li>Once the route has been accessed, it is considered delivered and used.</li>
                                <li>You may take screenshots for your personal reference during your one-time access.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 3 -->
                    <article id="sec-3" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">3</span>
                            <h2 class="text-xl font-bold text-zinc-950">Username and Password</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600">
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Access is provided using a unique username and password.</li>
                                <li>You are responsible for keeping your login credentials secure.</li>
                                <li>Sharing, reselling, or distributing your login credentials or purchased content is strictly prohibited.</li>
                                <li>We reserve the right to suspend or terminate access if unauthorized sharing or misuse is detected.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 4 -->
                    <article id="sec-4" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">4</span>
                            <h2 class="text-xl font-bold text-zinc-950">Educational Purpose Only</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-3">
                            <p>Our routes are created through our own research, observations, customer feedback, and publicly available information. They are intended solely as a study and practice aid.</p>
                            <p class="rounded-lg bg-zinc-50 border border-zinc-200 p-4 font-medium text-zinc-700">
                                The routes are not official DriveTest routes and are not endorsed, approved, or affiliated with DriveTest, the Ministry of Transportation Ontario (MTO), Serco, or any government agency.
                            </p>
                        </div>
                    </article>

                    <!-- Section 5 -->
                    <article id="sec-5" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">5</span>
                            <h2 class="text-xl font-bold text-zinc-950">Route Accuracy</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-3">
                            <p>While we make every reasonable effort to provide accurate and up-to-date navigation routes, we cannot guarantee that the exact route shown will be used during your road test.</p>
                            <p>DriveTest examiners may:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>change streets,</li>
                                <li>modify the route,</li>
                                <li>use alternate roads,</li>
                                <li>avoid construction or traffic,</li>
                                <li>respond to road closures or weather conditions,</li>
                                <li>follow any approved testing route within the local testing area.</li>
                            </ul>
                            <p class="pt-2">Typical testing areas may extend approximately:</p>
                            <ul class="list-disc pl-5 space-y-1 font-bold text-zinc-800">
                                <li>G2 Road Test: within approximately 2–3 km of the DriveTest Centre.</li>
                                <li>G Road Test: within approximately 4–5 km of the DriveTest Centre.</li>
                            </ul>
                            <p class="text-xs text-zinc-400 italic">These distances are estimates only and may vary.</p>
                        </div>
                    </article>

                    <!-- Section 6 -->
                    <article id="sec-6" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">6</span>
                            <h2 class="text-xl font-bold text-zinc-950">No Guarantee of Examination Route</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-3">
                            <p>We do not guarantee that:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>your examiner will follow the displayed route;</li>
                                <li>every street or maneuver will match the route provided;</li>
                                <li>the route will result in a passing grade.</li>
                            </ul>
                            <p class="font-bold text-zinc-800">Your success depends entirely on your driving ability and the examiner's assessment.</p>
                        </div>
                    </article>

                    <!-- Section 7 -->
                    <article id="sec-7" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">7</span>
                            <h2 class="text-xl font-bold text-zinc-950">No Responsibility for Route Changes</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-2">
                            <p>Ontario Services and 5036721 Ontario Inc. accept no responsibility if a DriveTest examiner:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>changes any portion of the route;</li>
                                <li>selects a different approved route;</li>
                                <li>uses an alternate street;</li>
                                <li>adds or removes turns;</li>
                                <li>follows any other approved examination route.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 8 -->
                    <article id="sec-8" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">8</span>
                            <h2 class="text-xl font-bold text-zinc-950 text-red-700">No Refund Policy</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-3">
                            <p>Due to the nature of digital products:</p>
                            <ul class="list-disc pl-5 space-y-1 font-bold text-zinc-800">
                                <li>All sales are final.</li>
                                <li>No refunds will be provided after purchase.</li>
                                <li>No exchanges are available.</li>
                                <li>No cancellations are accepted after access has been issued.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 9 -->
                    <article id="sec-9" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">9</span>
                            <h2 class="text-xl font-bold text-zinc-950">Chargebacks and Payment Disputes</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-3">
                            <p>By purchasing this product, you agree that:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>the product is accurately described;</li>
                                <li>you understand that it is a digital product;</li>
                                <li>you understand it is delivered electronically;</li>
                                <li>you accept these Terms and Conditions before completing your purchase.</li>
                            </ul>
                            <p>You agree not to initiate a chargeback or payment dispute on the basis of:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>lack of knowledge of the product;</li>
                                <li>misunderstanding of the product description;</li>
                                <li>examiner choosing a different route;</li>
                                <li>not passing the driving test;</li>
                                <li>dissatisfaction resulting from route variations.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 10 -->
                    <article id="sec-10" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">10</span>
                            <h2 class="text-xl font-bold text-zinc-950">Intellectual Property</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-3">
                            <p>All maps, navigation routes, graphics, text, and digital content are the intellectual property of 5036721 Ontario Inc.</p>
                            <p>You may not:</p>
                            <div class="grid gap-2 sm:grid-cols-2 text-zinc-800 font-semibold p-3 bg-zinc-50 border border-zinc-200 rounded-lg">
                                <div>• reproduce</div>
                                <div>• copy</div>
                                <div>• distribute</div>
                                <div>• sell</div>
                                <div>• publish</div>
                                <div>• upload</div>
                                <div>• share</div>
                                <div>• commercially use</div>
                            </div>
                            <p>any portion of our digital products without our written permission. Personal screenshots for your own study are permitted.</p>
                        </div>
                    </article>

                    <!-- Section 11 -->
                    <article id="sec-11" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">11</span>
                            <h2 class="text-xl font-bold text-zinc-950">Limitation of Liability</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-2">
                            <p>To the maximum extent permitted by law, Ontario Services and 5036721 Ontario Inc. shall not be liable for:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>failure to pass a road test;</li>
                                <li>examiner route changes;</li>
                                <li>navigation differences;</li>
                                <li>errors caused by road construction, detours, traffic, or temporary restrictions;</li>
                                <li>indirect, incidental, or consequential damages arising from the use of our digital products.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 12 -->
                    <article id="sec-12" class="public-glass-card p-6 sm:p-8" data-page-reveal>
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg section-badge text-sm font-black">12</span>
                            <h2 class="text-xl font-bold text-zinc-950">Acceptance of Terms</h2>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-zinc-600 space-y-3">
                            <p>Before purchasing, you must carefully read these Terms and Conditions.</p>
                            <p>By clicking "I Accept", completing your purchase, or accessing the digital route, you confirm that:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>you have read these Terms and Conditions;</li>
                                <li>you understand the product you are purchasing;</li>
                                <li>you agree that all sales are final;</li>
                                <li>you understand the routes are based on our research and knowledge;</li>
                                <li>you understand examiners may use different approved routes;</li>
                                <li>you accept the No Refund Policy;</li>
                                <li>you agree not to dispute the purchase because the product was accurately described.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Contact Details -->
                    <article id="contact-details" class="public-glass-card p-6 sm:p-8 border-l-4 border-l-blue-600" data-page-reveal>
                        <h2 class="text-lg font-bold text-zinc-950">Contact</h2>
                        <div class="mt-4 text-sm leading-6 text-zinc-600">
                            <p class="font-bold text-zinc-800">Ontario Services – 5036721 Ontario Inc.</p>
                            <p>30 Eglinton Avenue West, Unit 400</p>
                            <p>Mississauga, ON L5R 3E7</p>
                            <p>Canada</p>
                        </div>
                    </article>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const reveals = document.querySelectorAll('[data-page-reveal]');
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, { threshold: .1 });

            reveals.forEach((element) => observer.observe(element));

            // Smooth scroll tracking active sidebar state
            const sections = document.querySelectorAll('article[id]');
            const sidebarLinks = document.querySelectorAll('.sidebar-link');

            function onScroll() {
                let currentSec = '';
                sections.forEach(sec => {
                    const top = sec.offsetTop - 120;
                    if (window.scrollY >= top) {
                        currentSec = '#' + sec.getAttribute('id');
                    }
                });

                sidebarLinks.forEach(link => {
                    link.classList.toggle('active', link.getAttribute('href') === currentSec);
                });
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
    </script>
@endpush
