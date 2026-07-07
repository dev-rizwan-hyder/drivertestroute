@extends('layouts.app')

@section('title', 'Checkout - '.$drivingRoute->title)

@section('content')
    @php
        $startsIncluded = max(1, (int) ($drivingRoute->access_limit ?? 1));
        $remainingStarts = $purchase?->remainingStarts() ?? 0;
        $totalAfterPurchase = $remainingStarts + $startsIncluded;
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('driving-routes.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Back to routes</a>
                <h1 class="mt-3 text-3xl font-bold text-zinc-950">Secure Checkout</h1>
                <p class="mt-2 max-w-2xl text-zinc-600">Confirm student details, review the route access, and complete payment to unlock the map.</p>
            </div>
            <span class="inline-flex w-fit rounded-md bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-800">
                {{ $stripeEnabled ? 'Stripe card payment' : 'Local checkout mode' }}
            </span>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_420px]">
            <div class="space-y-6">
                <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
                    <div class="border-b border-zinc-200 bg-zinc-950 p-6 text-white">
                        <p class="text-sm font-bold uppercase tracking-normal text-emerald-300">Selected route</p>
                        <h2 class="mt-2 text-3xl font-bold">{{ $drivingRoute->title }}</h2>
                        <p class="mt-2 text-zinc-300">{{ $drivingRoute->city }}, {{ $drivingRoute->province }}</p>
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

                    <section class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
                        <div class="mb-5">
                            <h2 class="text-xl font-bold text-zinc-950">Billing & Card</h2>
                            <p class="mt-1 text-sm text-zinc-600">Card details are handled securely by Stripe and never stored on this server.</p>
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

                        @if($stripeEnabled)
                            <div class="mt-5">
                                <label class="block">
                                    <span class="text-sm font-bold text-zinc-800">Card Details</span>
                                    <div id="card-element" class="mt-1 rounded-md border border-zinc-300 bg-white px-3 py-3 shadow-sm"></div>
                                </label>
                                <p id="card-errors" class="mt-3 hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700"></p>
                                <p class="mt-3 text-xs leading-5 text-zinc-500">Use Stripe test cards in test mode, for example 4242 4242 4242 4242 with any future expiry and CVC.</p>
                            </div>
                        @else
                            <div class="mt-5 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                                Stripe is not configured. This checkout will record a local paid purchase after submission.
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

    @if($stripeEnabled)
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const checkoutForm = document.getElementById('checkout-form');
            const checkoutButton = document.getElementById('checkout-submit');
            const checkoutStatus = document.getElementById('checkout-status');
            const cardErrors = document.getElementById('card-errors');
            const paymentIntentInput = document.getElementById('payment-intent-id');
            const stripe = Stripe(@json($stripeKey));
            const elements = stripe.elements();
            const card = elements.create('card', {
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

            function setCheckoutMessage(message, error = false) {
                const target = error ? cardErrors : checkoutStatus;
                target.textContent = message;
                target.classList.remove('hidden');
                if (!error) {
                    cardErrors.classList.add('hidden');
                }
            }

            function setCheckoutLoading(loading) {
                checkoutButton.disabled = loading;
                checkoutButton.textContent = loading ? 'Processing payment...' : 'Pay ${{ number_format((float) $drivingRoute->price, 2) }} and Unlock';
            }

            checkoutForm.addEventListener('submit', async (event) => {
                if (paymentIntentInput.value) {
                    return;
                }

                event.preventDefault();

                if (!checkoutForm.reportValidity()) {
                    return;
                }

                setCheckoutLoading(true);
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
            });
        </script>
    @endif
@endsection
