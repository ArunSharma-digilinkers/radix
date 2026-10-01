{{--
    Contact page.

    The enquiry form is markup only — same treatment as the homepage's dealer
    search and battery finder. Submission handling, validation and spam
    protection land with the rest of the lead pipeline in Phase 5; see
    App\Support\Content\ContactPageContent.

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

        <x-ui.heading as="h1" size="hero" class="mt-4 max-w-xl text-radix-dark">
            Let&rsquo;s talk <span class="text-radix-red">power</span>.
        </x-ui.heading>

        <p class="mt-5 max-w-lg text-base leading-relaxed text-lead sm:text-[1.03125rem]">
            Product enquiry, dealership or export partnership — tell us what you need and
            we&rsquo;ll reply within one business day.
        </p>

        @if ($whatsapp || $tollFree)
            <div class="mt-7 flex flex-wrap gap-3.5">
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
            <p class="mt-6 text-[0.84375rem] text-muted">
                The quickest way to reach us right now is the form below.
            </p>
        @endif
    </x-ui.section>

    {{-- ENQUIRY FORM --}}
    <x-ui.section tone="white" id="enquiry">
        <div class="grid gap-10 lg:grid-cols-[1fr_1.2fr] lg:gap-14">
            <div>
                <x-ui.eyebrow>Enquiry form</x-ui.eyebrow>
                <x-ui.heading size="lg" class="mt-3 text-radix-dark">Send us a message.</x-ui.heading>

                <p class="mt-4 max-w-sm text-[0.9375rem] leading-relaxed text-muted">
                    Reach the right team faster — pick a reason below and we&rsquo;ll route it
                    straight to them.
                </p>
            </div>

            {{-- Persisted and routed to the sales inbox in Phase 5 — see
                 App\Models\Enquiry. --}}
            <form class="grid gap-5 sm:grid-cols-2">
                <x-ui.text-field label="Full name" name="name" required class="sm:col-span-2" />
                <x-ui.text-field label="Email" name="email" type="email" required />
                <x-ui.text-field label="Phone" name="phone" type="tel" />

                <x-ui.select-field
                    label="Reason for enquiry"
                    name="type"
                    placeholder="Select a reason"
                    :options="App\Support\Content\ContactPageContent::enquiryTypes()"
                    class="sm:col-span-2"
                />

                <x-ui.text-field
                    label="Message"
                    name="message"
                    textarea
                    required
                    placeholder="Tell us a bit about what you need…"
                    class="sm:col-span-2"
                />

                <x-ui.button type="submit" variant="primary" size="lg" class="sm:col-span-2 sm:w-fit">
                    Send enquiry
                </x-ui.button>
            </form>
        </div>
    </x-ui.section>

    {{-- OFFICES — hidden until ContactPageContent::offices() has data --}}
    @if (count($offices))
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Our offices</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Where to find us.</x-ui.heading>

            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($offices as $office)
                    <div class="rounded-card border border-hairline bg-white p-5">
                        <p class="font-display text-base font-bold text-ink">{{ $office['name'] }}</p>
                        <p class="mt-2 text-[0.84375rem] leading-relaxed text-muted">{{ $office['address'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- DEALER LOCATOR CROSS-LINK --}}
    <x-ui.section tone="dark" class="text-center">
        <x-ui.eyebrow tone="dark">650+ network</x-ui.eyebrow>
        <x-ui.heading size="md" class="mt-3">Looking for a dealer instead?</x-ui.heading>

        <p class="mx-auto mt-2.5 max-w-md text-[0.9375rem] text-on-dark-muted">
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
