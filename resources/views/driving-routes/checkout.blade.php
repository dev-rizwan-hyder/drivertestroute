@extends('layouts.app')

@section('title', 'Checkout - '.$drivingRoute->title)

@push('styles')
    <style>
        .checkout-page {
            min-height: calc(100vh - 5rem);
            background-color: #f8f9fa;
            background-image:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .09), transparent 32%),
                radial-gradient(circle at 86% 12%, rgba(6, 182, 212, .07), transparent 30%),
                linear-gradient(180deg, rgba(248, 249, 250, .9), rgba(241, 243, 245, .94) 48%, rgba(248, 249, 250, .96)),
                var(--public-image-pages);
            background-position: center, center, center, center top;
            background-repeat: no-repeat;
            background-size: auto, auto, auto, cover;
        }

        .checkout-route-head {
            background-color: #f1f3f5;
            background-image:
                linear-gradient(135deg, rgba(248, 249, 250, .72), rgba(255, 255, 255, .38), rgba(241, 243, 245, .8)),
                var(--public-image-route);
            background-position: center, center;
            background-repeat: no-repeat;
            background-size: auto, cover;
        }

        .payment-method-btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .payment-method-btn:hover {
            border-color: #059669; /* emerald-600 */
            background-color: rgba(240, 253, 244, 0.5); /* emerald-50/50 */
        }
    </style>
@endpush

@section('content')
    @php
        $startsIncluded = max(1, (int) ($drivingRoute->access_limit ?? 1));
        $remainingStarts = $purchase?->remainingStarts() ?? 0;
        $totalAfterPurchase = $remainingStarts + $startsIncluded;
    @endphp

    <div class="checkout-page">
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('driving-routes.index') }}" class="text-sm font-semibold text-cyan-200 hover:text-white">Back to routes</a>
                <h1 class="mt-3 text-3xl font-bold text-white">Secure Checkout</h1>
                <p class="mt-2 max-w-2xl text-slate-400">Confirm student details, review the route access, and complete payment to unlock the map.</p>
            </div>
            <span class="inline-flex w-fit rounded-md border border-blue-500/20 bg-white/[.08] px-3 py-2 text-sm font-bold text-cyan-100">
                {{ $stripeEnabled ? 'Stripe card payment' : 'Local checkout mode' }}
            </span>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_420px]">
            <div class="space-y-6">
                <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
                    <div class="checkout-route-head border-b border-zinc-200 p-6 text-white">
                        <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Selected route</p>
                        <h2 class="mt-2 text-3xl font-bold">{{ $drivingRoute->title }}</h2>
                    <p class="mt-2 text-zinc-600">{{ $drivingRoute->city }}, {{ $drivingRoute->province }}</p>
                    </div>

                    <div class="grid gap-4 p-6 md:grid-cols-2">
                        <div class="rounded-md bg-zinc-50 p-4">
                            <div class="text-sm font-medium text-zinc-500">Start</div>
                            <div class="mt-1 font-bold text-zinc-950">{{ $drivingRoute->start_label ?: 'Start point' }}</div>
                        </div>
                        <div class="rounded-md bg-zinc-50 p-4">
                            <div class="text-sm font-medium text-zinc-500">Midpoint</div>
                            <div class="mt-1 font-bold text-zinc-950">{{ $drivingRoute->destination_label ?: 'Midpoint' }}</div>
                        </div>
                        <div class="rounded-md bg-zinc-50 p-4">
                            <div class="text-sm font-medium text-zinc-500">Included map starts</div>
                            <div class="mt-1 font-bold text-zinc-950">{{ $startsIncluded }}</div>
                        </div>
                        <div class="rounded-md bg-zinc-50 p-4">
                            <div class="text-sm font-medium text-zinc-500">After checkout</div>
                            <div class="mt-1 font-bold text-zinc-950">{{ $totalAfterPurchase }} starts available</div>
                        </div>
                    </div>
                </article>

                <form id="checkout-form" method="POST" action="{{ route('driving-routes.checkout.store', $drivingRoute) }}" class="space-y-6">
                    @csrf
                    <input id="payment-intent-id" type="hidden" name="payment_intent_id" value="{{ old('payment_intent_id') }}">
                    <input id="payment-provider" type="hidden" name="payment_provider" value="{{ $stripeEnabled ? 'stripe' : ($paypalEnabled ? 'paypal' : ($squareEnabled ? 'square' : 'local')) }}">

                    <section class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h2 class="text-xl font-bold text-zinc-950">Student Details</h2>
                            <p class="mt-1 text-sm text-zinc-600">These details help identify who the paid route access is for.</p>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-bold text-zinc-800">Student Full Name</span>
                                <input type="text" name="student_name" value="{{ old('student_name', auth()->user()->name) }}" required class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </label>

                            <label class="block">
                                <span class="text-sm font-bold text-zinc-800">Student Email</span>
                                <input type="email" name="student_email" value="{{ old('student_email', auth()->user()->email) }}" required class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </label>

                            <label class="block">
                                <span class="text-sm font-bold text-zinc-800">Student Phone</span>
                                <input type="tel" name="student_phone" value="{{ old('student_phone') }}" required class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </label>

                            <label class="block">
                                <span class="text-sm font-bold text-zinc-800">City / Test Area</span>
                                <input type="text" name="student_city" value="{{ old('student_city', $drivingRoute->city) }}" class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </label>

                            <label class="block">
                                <span class="text-sm font-bold text-zinc-800">Expected Test Date</span>
                                <input type="date" name="student_test_date" value="{{ old('student_test_date') }}" class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </label>

                            <label class="block md:col-span-2">
                                <span class="text-sm font-bold text-zinc-800">Notes for This Student</span>
                                <textarea name="student_notes" rows="3" class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">{{ old('student_notes') }}</textarea>
                            </label>
                        </div>
                    </section>

                    <!-- Payment Method Selector -->
                    <section class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h2 class="text-xl font-bold text-zinc-950">Payment Method</h2>
                            <p class="mt-1 text-sm text-zinc-600">Select your preferred secure payment method.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
                            @if($stripeEnabled)
                                <label class="payment-method-btn relative flex cursor-pointer flex-col rounded-lg border-2 p-4 text-center hover:bg-zinc-50 focus:outline-none border-emerald-600 bg-emerald-50/30">
                                    <input type="radio" name="payment_provider_select" value="stripe" class="sr-only" checked>
                                    <span class="block text-sm font-bold text-zinc-900">Stripe Card</span>
                                    <span class="mt-1 block text-xs text-zinc-500">Pay securely using Credit/Debit Card</span>
                                </label>
                            @endif

                            @if($paypalEnabled)
                                <label class="payment-method-btn relative flex cursor-pointer flex-col rounded-lg border-2 p-4 text-center hover:bg-zinc-50 focus:outline-none @if(!$stripeEnabled) border-emerald-600 bg-emerald-50/30 @else border-zinc-200 @endif">
                                    <input type="radio" name="payment_provider_select" value="paypal" class="sr-only" @if(!$stripeEnabled) checked @endif>
                                    <span class="block text-sm font-bold text-zinc-900">PayPal</span>
                                    <span class="mt-1 block text-xs text-zinc-500">Pay with PayPal balance or card</span>
                                </label>
                            @endif

                            @if($squareEnabled)
                                <label class="payment-method-btn relative flex cursor-pointer flex-col rounded-lg border-2 p-4 text-center hover:bg-zinc-50 focus:outline-none @if(!$stripeEnabled && !$paypalEnabled) border-emerald-600 bg-emerald-50/30 @else border-zinc-200 @endif">
                                    <input type="radio" name="payment_provider_select" value="square" class="sr-only" @if(!$stripeEnabled && !$paypalEnabled) checked @endif>
                                    <span class="block text-sm font-bold text-zinc-900">Square Card</span>
                                    <span class="mt-1 block text-xs text-zinc-500">Fast card payment via Square</span>
                                </label>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h2 class="text-xl font-bold text-zinc-950">Billing & Payment Details</h2>
                            <p class="mt-1 text-sm text-zinc-600">All payment details are handled securely by the respective providers and never stored on this server.</p>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-bold text-zinc-800">Billing Name</span>
                                <input type="text" name="billing_name" value="{{ old('billing_name', auth()->user()->name) }}" required class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </label>

                            <label class="block">
                                <span class="text-sm font-bold text-zinc-800">Billing Email</span>
                                <input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()->email) }}" required class="mt-1 block w-full rounded-md border border-zinc-300 px-3 py-2 text-zinc-950 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </label>
                        </div>

                        <!-- Stripe Details -->
                        @if($stripeEnabled)
                            <div id="stripe-payment-container" class="payment-details-container mt-5">
                                <label class="block">
                                    <span class="text-sm font-bold text-zinc-800">Card Details</span>
                                    <div id="card-element" class="mt-1 rounded-md border border-zinc-300 bg-white px-3 py-3 shadow-sm"></div>
                                </label>
                                <p id="card-errors" class="mt-3 hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700"></p>
                                <p class="mt-3 text-xs leading-5 text-zinc-500">Use Stripe test cards in test mode, for example 4242 4242 4242 4242 with any future expiry and CVC.</p>
                            </div>
                        @endif

                        <!-- PayPal Details -->
                        @if($paypalEnabled)
                            <div id="paypal-payment-container" class="payment-details-container mt-5 hidden">
                                <span class="text-sm font-bold text-zinc-800 block mb-2">PayPal Checkout</span>
                                <div id="paypal-button-container" class="mt-1 min-h-[50px]"></div>
                                <p id="paypal-errors" class="mt-3 hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700"></p>
                            </div>
                        @endif

                        <!-- Square Details -->
                        @if($squareEnabled)
                            <div id="square-payment-container" class="payment-details-container mt-5 hidden">
                                <label class="block">
                                    <span class="text-sm font-bold text-zinc-800">Card Details</span>
                                    <div id="square-card-container" class="mt-1 rounded-md border border-zinc-300 bg-white px-3 py-3 shadow-sm min-h-[40px]"></div>
                                </label>
                                <p id="square-errors" class="mt-3 hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700"></p>
                            </div>
                        @endif

                        <label class="mt-5 flex items-start gap-3 text-sm text-zinc-700">
                            <input type="checkbox" name="terms" value="1" required class="mt-1 rounded border-zinc-300 text-emerald-700 focus:ring-emerald-600" @checked(old('terms'))>
                            <span>I understand each Start Drive action uses one paid map start and this purchase unlocks access for the student listed above.</span>
                        </label>
                    </section>
                </form>
            </div>

            <aside class="h-fit rounded-lg border border-zinc-200 bg-white p-6 shadow-sm lg:sticky lg:top-24">
                <h2 class="text-lg font-bold text-zinc-950">Order Summary</h2>

                <dl class="mt-5 space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-zinc-600">Route price</dt>
                        <dd class="font-bold text-zinc-950">${{ number_format((float) $drivingRoute->price, 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-zinc-600">Currency</dt>
                        <dd class="font-bold text-zinc-950">{{ $stripeCurrency }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-zinc-600">Map starts included</dt>
                        <dd class="font-bold text-zinc-950">{{ $startsIncluded }}</dd>
                    </div>
                    @if($purchase)
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-zinc-600">Current starts left</dt>
                            <dd class="font-bold text-zinc-950">{{ $remainingStarts }}</dd>
                        </div>
                    @endif
                    <div class="border-t border-zinc-200 pt-4">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-base font-bold text-zinc-950">Total due</dt>
                            <dd class="text-2xl font-black text-zinc-950">${{ number_format((float) $drivingRoute->price, 2) }}</dd>
                        </div>
                    </div>
                </dl>

                <div class="mt-6 rounded-md bg-zinc-50 p-4 text-sm">
                    <div class="font-bold text-zinc-950">Access after payment</div>
                    <p class="mt-2 leading-6 text-zinc-600">The map unlocks immediately after payment is confirmed. Buying again adds more starts to your existing access.</p>
                </div>

                <button id="checkout-submit" type="submit" form="checkout-form" class="mt-6 w-full rounded-md bg-emerald-700 px-4 py-3 font-bold text-white transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:bg-zinc-400">
                    Pay ${{ number_format((float) $drivingRoute->price, 2) }} and Unlock
                </button>

                <p id="checkout-status" class="mt-3 hidden text-sm font-semibold text-zinc-600"></p>
            </aside>
        </div>
    </section>
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
            checkoutButton.textContent = loading ? 'Processing payment...' : 'Pay ${{ number_format((float) $drivingRoute->price, 2) }} and Unlock';
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
                        label.classList.remove('border-emerald-600', 'bg-emerald-50/30');
                        label.classList.add('border-zinc-200');
                    }
                });
                
                if (e.target.checked) {
                    const label = e.target.closest('.payment-method-btn');
                    if (label) {
                        label.classList.remove('border-zinc-200');
                        label.classList.add('border-emerald-600', 'bg-emerald-50/30');
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
                        color: '#18181b',
                        fontFamily: 'ui-sans-serif, system-ui, sans-serif',
                        fontSize: '16px',
                        '::placeholder': { color: '#a1a1aa' },
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
                                color: '#18181b',
                                fontSize: '16px',
                                fontFamily: 'ui-sans-serif, system-ui, sans-serif',
                            },
                            'input::placeholder': {
                                color: '#a1a1aa'
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

                    paymentIntentInput.value = result.paymentIntent.id;
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
    </script>
@endsection
