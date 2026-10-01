{{--
    Contact page.

    The enquiry form persists an Enquiry (see ContactController). Spam protection
    and the customer auto-acknowledgement email land with Phase 5.

    Quick-contact channels (WhatsApp, toll-free) omit themselves when
    unconfigured, same as x-site.quick-actions — see config/radix.php.
--}}
@php
    $whatsapp = App\Support\Content\SiteContent::whatsapp();
    $tollFree = App\Support\Content\SiteContent::tollFree();
    $offices = App\Support\Content\ContactPageContent::offices();
@endphp
<x-layouts.public
    title="Contact Us"
    description="Get in touch with Radix Power Solutions for product enquiries, dealership opportunities or export partnerships. We reply within one business day."
>
    {{-- HERO --}}
    <x-ui.section tone="surface" :reveal="false">
        <x-ui.eyebrow>Contact</x-ui.eyebrow>

        <x-ui.heading as="h1" size="hero" class="mt-4 mw-xl text-radix-dark">
            Let&rsquo;s talk <span class="text-radix-red">power</span>.
        </x-ui.heading>

        <p class="mb-0 mt-5 mw-lg fs-16 fs-sm-16-5 lh-relaxed text-lead">
            Product enquiry, dealership or export partnership — tell us what you need and
            we&rsquo;ll reply within one business day.
        </p>

        @if ($whatsapp || $tollFree)
            <div class="d-flex flex-wrap gap-3-5 mt-7">
                @if ($whatsapp)
                    <x-ui.button
                        variant="primary"
                        size="lg"
                        href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}?text={{ rawurlencode('Hello Radix, I would like to enquire about your batteries.') }}"
                    >
                        Chat on WhatsApp
                    </x-ui.button>
                @endif

                @if ($tollFree)
                    <x-ui.button variant="secondary" size="lg" href="tel:{{ preg_replace('/\s+/', '', $tollFree) }}">
                        Call {{ $tollFree }}
                    </x-ui.button>
                @endif
            </div>
        @else
            {{-- No channel is confirmed yet (brief §11.5) — the form below is the
                 primary path rather than a placeholder number or dead link. --}}
            <p class="mb-0 mt-6 fs-13-5 text-muted">
                The quickest way to reach us right now is the form below.
            </p>
        @endif
    </x-ui.section>

    {{-- ENQUIRY FORM --}}
    <x-ui.section tone="white" id="enquiry">
        <div class="row gy-10 gx-lg-14">
            <div class="col-lg-5">
                <x-ui.eyebrow>Enquiry form</x-ui.eyebrow>
                <x-ui.heading size="lg" class="mt-3 text-radix-dark">Send us a message.</x-ui.heading>

                <p class="mb-0 mt-4 mw-sm fs-15 lh-relaxed text-muted">
                    Reach the right team faster — pick a reason below and we&rsquo;ll route it
                    straight to them.
                </p>
            </div>

            <div class="col-lg-7">
                @if (session('enquired'))
                    <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-10">
                        <p class="mb-0 font-display fs-17 fw-extrabold tracking-display text-radix-dark">
                            Enquiry received.
                        </p>
                        <p class="mb-0 mt-2 mw-sm fs-15 lh-relaxed text-muted">
                            Thanks for reaching out &mdash; we&rsquo;ll get back to you within one
                            business day.
                        </p>
                    </div>
                @else
                    <form action="{{ route('contact.enquire') }}" method="POST" class="row g-5">
                        @csrf

                        <x-ui.text-field class="col-12" label="Full name" name="name" required :value="old('name')" />
                        <x-ui.text-field class="col-sm-6" label="Email" name="email" type="email" required :value="old('email')" />
                        <x-ui.text-field class="col-sm-6" label="Phone" name="phone" type="tel" :value="old('phone')" />

                        <x-ui.select-field
                            class="col-sm-6"
                            label="Reason for enquiry"
                            name="type"
                            placeholder="Select a reason"
                            :options="App\Support\Content\ContactPageContent::enquiryTypes()"
                            :selected="old('type', $selectedProduct ? App\Models\Enquiry::TYPE_PRODUCT : null)"
                        />

                        <x-ui.select-field
                            class="col-sm-6"
                            label="Product (optional)"
                            name="product"
                            placeholder="Any product"
                            :options="$products->all()"
                            :selected="old('product', $selectedProduct)"
                        />

                        <x-ui.text-field
                            label="Message"
                            name="message"
                            textarea
                            required
                            placeholder="Tell us a bit about what you need…"
                            :value="old('message')"
                            class="col-12"
                        />

                        <div class="col-12">
                            <x-ui.button type="submit" variant="primary" size="lg">
                                Send enquiry
                            </x-ui.button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </x-ui.section>

    {{-- OFFICES — hidden until ContactPageContent::offices() has data --}}
    @if (count($offices))
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Our offices</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Where to find us.</x-ui.heading>

            <div class="row g-6 mt-2">
                @foreach ($offices as $office)
                    <div class="col-sm-6 col-lg-4">
                        <div class="h-100 rounded-card border border-hairline bg-white p-5">
                            <p class="mb-0 font-display fs-16 fw-bold text-ink">{{ $office['name'] }}</p>
                            <p class="mb-0 mt-2 fs-13-5 lh-relaxed text-muted">{{ $office['address'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- DEALER LOCATOR CROSS-LINK --}}
    <x-ui.section tone="dark" class="text-center">
        <x-ui.eyebrow tone="dark">650+ network</x-ui.eyebrow>
        <x-ui.heading size="md" class="mt-3">Looking for a dealer instead?</x-ui.heading>

        <p class="mx-auto mb-0 mt-2-5 mw-md fs-15 text-on-dark-muted">
            Search our dealer network by city or PIN code to find stocked Radix products
            near you.
        </p>

        <div class="mt-6">
            <x-ui.button variant="inverse" size="lg" href="{{ route('dealers.index') }}">
                Find a dealer
            </x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.public>
