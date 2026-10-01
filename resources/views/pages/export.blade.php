{{--
    Export page (brief §4): interactive world map, per-region blurbs, the
    logistics overview, and a partner enquiry form.

    The map highlights the countries confirmed in CLAUDE.md §7 (baked in by
    scripts/build-maps.mjs — see that file's SERVED/WORLD_PINS constants);
    that part is safe to show regardless of admin data. The per-region
    blurbs are real App\Models\ExportMarket rows and render nothing beyond a
    plain list until the admin adds them — same honest-until-populated
    treatment as Dealers and Careers.

    The partner form posts to App\Http\Controllers\ExportController::enquire()
    and creates a real Enquiry(type=export) — see that controller's note on
    why, unlike Contact, this one isn't markup-only.
--}}
@php
    $process = App\Support\Content\ExportPageContent::process();
@endphp
<x-layouts.public
    title="Export"
    description="Radix Power Solutions exports batteries to Nigeria, UAE, Afghanistan, Nepal and more. See our markets, how we ship, and enquire about a partnership."
>
    {{-- HERO --}}
    <x-ui.section tone="surface" :reveal="false">
        <x-ui.eyebrow>Export &middot; B2B</x-ui.eyebrow>

        <x-ui.heading as="h1" size="hero" class="mt-4 mw-2xl text-radix-dark">
            Trusted <span class="text-radix-red">across borders</span>.
        </x-ui.heading>

        <p class="mb-0 mt-5 mw-xl fs-16 fs-sm-16-5 lh-relaxed text-lead">
            25 years of battery manufacturing, now reaching distributors and fleets beyond
            India. Explore where we ship and what it takes to bring Radix batteries to your
            market.
        </p>

        <div class="d-flex flex-wrap gap-3-5 mt-7">
            <x-ui.button variant="primary" size="lg" href="#enquire">Request an export quote</x-ui.button>
        </div>
    </x-ui.section>

    {{-- MAP + MARKETS --}}
    <x-ui.section tone="dark" id="markets" x-data="exportMarketMap">
        <x-ui.eyebrow tone="dark">Where we ship</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3">Current export markets.</x-ui.heading>

        <div class="row gy-10 gx-lg-12 align-items-lg-center mt-4">
            <div class="col-lg-7">
                <div
                    class="rx-map-panel rx-map-panel--dark overflow-hidden rounded-frame border rx-rule-on-dark bg-radix-dark-2 p-4 text-radix-red-on-dark"
                    @mouseover="highlight($event.target.dataset.iso)"
                    @mouseout="clear()"
                    @focusin="highlight($event.target.dataset.iso)"
                    @focusout="clear()"
                >
                    <x-map.world />
                </div>
            </div>

            <div class="col-lg-5">
                @if ($markets->isEmpty())
                    <p class="mb-0 fs-15 lh-relaxed text-on-dark-muted">
                        Detailed notes on each market are on their way. The highlighted
                        countries above are where we currently ship — get in touch below and
                        we&rsquo;ll walk you through specifics for your region.
                    </p>
                @else
                    <ul class="d-flex flex-column gap-3">
                        @foreach ($markets as $market)
                            <li
                                @if ($market->iso_numeric)
                                    data-iso="{{ $market->iso_numeric }}"
                                    @mouseenter="highlight('{{ $market->iso_numeric }}')"
                                    @mouseleave="clear()"
                                @endif
                                class="rx-market-card rounded-card border rx-rule-on-dark p-4"
                            >
                                <p class="mb-0 font-display fs-16 fw-bold text-on-dark">
                                    {{ $market->getTranslation('country_name', 'en') }}
                                </p>
                                @if ($blurb = trim((string) $market->getTranslation('blurb', 'en')))
                                    <p class="mb-0 mt-1-5 fs-13-5 lh-relaxed text-on-dark-muted">
                                        {{ $blurb }}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </x-ui.section>

    {{-- LOGISTICS OVERVIEW --}}
    <x-ui.section tone="white">
        <x-ui.eyebrow>How it works</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 text-radix-dark">From enquiry to your dock.</x-ui.heading>

        <div class="row gx-14 mt-6">
            @foreach ($process as $index => $step)
                <div class="col-sm-6">
                    <x-ui.numbered-item
                        :number="sprintf('%02d', $index + 1)"
                        :title="$step['title']"
                        :description="$step['description']"
                    />
                </div>
            @endforeach
        </div>
    </x-ui.section>

    {{-- PARTNER ENQUIRY FORM --}}
    <x-ui.section tone="surface" id="enquire">
        <div class="row gy-10 gx-lg-14">
            <div class="col-lg-5">
                <x-ui.eyebrow>Partner with us</x-ui.eyebrow>
                <x-ui.heading size="lg" class="mt-3 text-radix-dark">Let&rsquo;s talk export.</x-ui.heading>

                <p class="mb-0 mt-4 mw-sm fs-15 lh-relaxed text-muted">
                    Distributor, fleet or government tender — tell us your market and volumes
                    and we&rsquo;ll get back to you with a quote.
                </p>
            </div>

            <div class="col-lg-7">
                @if (session('enquired'))
                    <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-10">
                        <p class="mb-0 font-display fs-17 fw-extrabold tracking-display text-radix-dark">
                            Enquiry received.
                        </p>
                        <p class="mb-0 mt-2 mw-sm fs-15 lh-relaxed text-muted">
                            Thanks for reaching out — our export team will get back to you within
                            one business day.
                        </p>
                    </div>
                @else
                    <form action="{{ route('export.enquire') }}" method="POST" class="row g-5">
                        @csrf

                        <x-ui.text-field class="col-sm-6" label="Full name" name="name" required :value="old('name')" />
                        <x-ui.text-field class="col-sm-6" label="Company" name="company" :value="old('company')" />
                        <x-ui.text-field class="col-sm-6" label="Email" name="email" type="email" required :value="old('email')" />
                        <x-ui.text-field class="col-sm-6" label="Phone" name="phone" type="tel" :value="old('phone')" />

                        <x-ui.text-field
                            label="Message"
                            name="message"
                            textarea
                            required
                            placeholder="Country/region, products of interest, estimated volumes…"
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
</x-layouts.public>
