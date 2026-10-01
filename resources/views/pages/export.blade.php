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

        <x-ui.heading as="h1" size="hero" class="mt-4 max-w-2xl text-radix-dark">
            Trusted <span class="text-radix-red">across borders</span>.
        </x-ui.heading>

        <p class="mt-5 max-w-xl text-base leading-relaxed text-lead sm:text-[1.03125rem]">
            25 years of battery manufacturing, now reaching distributors and fleets beyond
            India. Explore where we ship and what it takes to bring Radix batteries to your
            market.
        </p>

        <div class="mt-7 flex flex-wrap gap-3.5">
            <x-ui.button variant="primary" size="lg" href="#enquire">Request an export quote</x-ui.button>
        </div>
    </x-ui.section>

    {{-- MAP + MARKETS --}}
    <x-ui.section tone="dark" id="markets" x-data="exportMarketMap">
        <x-ui.eyebrow tone="dark">Where we ship</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3">Current export markets.</x-ui.heading>

        <div class="mt-8 grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:gap-12 lg:items-center">
            <div
                class="overflow-hidden rounded-frame border border-white/10 bg-radix-dark-2 p-4 text-radix-red-on-dark [--map-land:#22364f] [--map-line:#2e4664]"
                @mouseover="highlight($event.target.dataset.iso)"
                @mouseout="clear()"
                @focusin="highlight($event.target.dataset.iso)"
                @focusout="clear()"
            >
                <x-map.world />
            </div>

            <div>
                @if ($markets->isEmpty())
                    <p class="text-[0.9375rem] leading-relaxed text-on-dark-muted">
                        Detailed notes on each market are on their way. The highlighted
                        countries above are where we currently ship — get in touch below and
                        we&rsquo;ll walk you through specifics for your region.
                    </p>
                @else
                    <ul class="flex flex-col gap-3">
                        @foreach ($markets as $market)
                            <li
                                @if ($market->iso_numeric)
                                    data-iso="{{ $market->iso_numeric }}"
                                    @mouseenter="highlight('{{ $market->iso_numeric }}')"
                                    @mouseleave="clear()"
                                @endif
                                class="rounded-card border border-white/10 bg-white/5 p-4"
                            >
                                <p class="font-display text-base font-bold text-on-dark">
                                    {{ $market->getTranslation('country_name', 'en') }}
                                </p>
                                @if ($blurb = trim((string) $market->getTranslation('blurb', 'en')))
                                    <p class="mt-1.5 text-[0.84375rem] leading-relaxed text-on-dark-muted">
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

        <div class="mt-6 grid gap-x-14 sm:grid-cols-2">
            @foreach ($process as $index => $step)
                <x-ui.numbered-item
                    :number="sprintf('%02d', $index + 1)"
                    :title="$step['title']"
                    :description="$step['description']"
                />
            @endforeach
        </div>
    </x-ui.section>

    {{-- PARTNER ENQUIRY FORM --}}
    <x-ui.section tone="surface" id="enquire">
        <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-14">
            <div>
                <x-ui.eyebrow>Partner with us</x-ui.eyebrow>
                <x-ui.heading size="lg" class="mt-3 text-radix-dark">Let&rsquo;s talk export.</x-ui.heading>

                <p class="mt-4 max-w-sm text-[0.9375rem] leading-relaxed text-muted">
                    Distributor, fleet or government tender — tell us your market and volumes
                    and we&rsquo;ll get back to you with a quote.
                </p>
            </div>

            @if (session('enquired'))
                <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-10">
                    <p class="font-display text-[1.0625rem] font-extrabold tracking-display text-radix-dark">
                        Enquiry received.
                    </p>
                    <p class="mt-2 max-w-sm text-[0.9375rem] leading-relaxed text-muted">
                        Thanks for reaching out — our export team will get back to you within
                        one business day.
                    </p>
                </div>
            @else
                <form action="{{ route('export.enquire') }}" method="POST" class="grid gap-5 sm:grid-cols-2">
                    @csrf

                    <x-ui.text-field label="Full name" name="name" required :value="old('name')" />
                    <x-ui.text-field label="Company" name="company" :value="old('company')" />
                    <x-ui.text-field label="Email" name="email" type="email" required :value="old('email')" />
                    <x-ui.text-field label="Phone" name="phone" type="tel" :value="old('phone')" />

                    <x-ui.text-field
                        label="Message"
                        name="message"
                        textarea
                        required
                        placeholder="Country/region, products of interest, estimated volumes…"
                        :value="old('message')"
                        class="sm:col-span-2"
                    />

                    <x-ui.button type="submit" variant="primary" size="lg" class="sm:col-span-2 sm:w-fit">
                        Send enquiry
                    </x-ui.button>
                </form>
            @endif
        </div>
    </x-ui.section>
</x-layouts.public>
