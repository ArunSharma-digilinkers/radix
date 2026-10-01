{{--
    Dealer locator (brief §4) — search the 650+ network by city, state or PIN,
    optionally narrowed by type. See App\Http\Controllers\DealerController.

    Results are ordered alphabetically, not by distance: turning a typed
    location into coordinates for Dealer::scopeNearest() is Phase 5. The map
    here is the same illustrative India component the homepage teaser uses —
    it shows coverage, not the actual pins for this search, since accurate
    per-result pins need the Maps provider from PROJECT_PLAN.md's open
    question 5.
--}}
@php
    $typeLabels = [
        App\Models\Dealer::TYPE_RETAIL => 'Retail store',
        App\Models\Dealer::TYPE_DISTRIBUTOR => 'Distributor',
        App\Models\Dealer::TYPE_SERVICE_CENTRE => 'Service centre',
    ];
@endphp
<x-layouts.public
    title="Dealer Locator"
    description="Find a Radix Power Solutions dealer, distributor or service centre near you. Search our 650+ strong network by city, state or PIN code."
>
    {{-- HERO + SEARCH --}}
    <x-ui.section tone="surface" :reveal="false">
        <div class="row align-items-center gy-10 gx-lg-12">
            <div class="col-lg-5">
                <x-ui.eyebrow>650+ network</x-ui.eyebrow>

                <x-ui.heading as="h1" size="hero" class="mt-4 text-radix-dark">
                    Find your nearest <span class="text-radix-red">dealer</span>.
                </x-ui.heading>

                <p class="mb-0 mt-5 mw-md fs-16 fs-sm-16-5 lh-relaxed text-lead">
                    Search by city, state or PIN code to find a stocked Radix dealer,
                    distributor or service centre near you.
                </p>

                <form action="{{ route('dealers.index') }}" method="GET" class="mt-7 mw-md">
                    <label for="dealer-search" class="visually-hidden">City, state or PIN code</label>
                    <div class="rx-inline-search d-flex align-items-end gap-3">
                        <input
                            id="dealer-search"
                            type="text"
                            name="location"
                            value="{{ $location }}"
                            placeholder="Enter city, state or PIN code…"
                            class="min-w-0 flex-1"
                        >
                        <button type="submit" class="rx-inline-search__submit fs-15 fw-bold text-radix-red-deep">
                            Search <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>

                    <ul class="d-flex flex-wrap gap-2 mt-5">
                        <li>
                            <a
                                href="{{ route('dealers.index', ['location' => $location]) }}"
                                @class(['rx-chip rx-chip--link', 'rx-chip--active' => ! $type])
                                @if (! $type) aria-current="page" @endif
                            >All types</a>
                        </li>

                        @foreach ($typeLabels as $value => $label)
                            <li>
                                <a
                                    href="{{ route('dealers.index', ['location' => $location, 'type' => $value]) }}"
                                    @class(['rx-chip rx-chip--link', 'rx-chip--active' => $type === $value])
                                    @if ($type === $value) aria-current="page" @endif
                                >{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </form>
            </div>

            <div class="col-lg-7">
                <div class="rx-map-panel overflow-hidden rounded-frame border border-hairline bg-white p-4 text-radix-red">
                    <x-map.india />
                </div>
            </div>
        </div>
    </x-ui.section>

    {{-- RESULTS --}}
    <x-ui.section>
        @if ($dealers->isEmpty())
            <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-14 text-center">
                <p class="mb-0 font-display fs-20 fw-extrabold tracking-display text-radix-dark">
                    No dealers found.
                </p>
                <p class="mx-auto mb-0 mt-2 mw-sm fs-15 text-muted">
                    @if ($location || $type)
                        Try a different city, state or PIN code.
                        <a href="{{ route('dealers.index') }}" class="fw-semibold text-radix-red-deep">Clear search</a>.
                    @else
                        Dealer records are on their way.
                    @endif
                </p>
            </div>
        @else
            <p class="mb-0 fs-13-5 text-muted">
                Showing {{ $dealers->firstItem() }}&ndash;{{ $dealers->lastItem() }} of {{ $dealers->total() }} dealers.
            </p>

            <div class="row g-5 mt-1">
                @foreach ($dealers as $dealer)
                    <div class="col-sm-6 col-lg-4">
                        <div class="h-100 rounded-card border border-hairline bg-white p-5">
                            <span class="font-mono fs-10 text-uppercase tracking-eyebrow text-radix-red-deep">
                                {{ $typeLabels[$dealer->type] ?? $dealer->type }}
                            </span>

                            <p class="mb-0 mt-2 font-display fs-17 fw-extrabold tracking-display text-radix-dark">
                                {{ $dealer->name }}
                            </p>

                            <p class="mb-0 mt-2 fs-14 lh-relaxed text-muted">
                                @if ($dealer->address_line)
                                    {{ $dealer->address_line }}<br>
                                @endif
                                {{ $dealer->city }}, {{ $dealer->state }}
                                @if ($dealer->pincode)
                                    &mdash; {{ $dealer->pincode }}
                                @endif
                            </p>

                            @if ($dealer->phone || $dealer->whatsapp)
                                <div class="d-flex flex-wrap gap-2-5 mt-4">
                                    @if ($dealer->phone)
                                        <x-ui.button variant="secondary" size="md" href="tel:{{ preg_replace('/\s+/', '', $dealer->phone) }}">
                                            Call
                                        </x-ui.button>
                                    @endif

                                    @if ($dealer->whatsapp)
                                        <x-ui.button
                                            variant="primary"
                                            size="md"
                                            href="https://wa.me/{{ preg_replace('/\D+/', '', $dealer->whatsapp) }}?text={{ rawurlencode('Hello, I found your store on the Radix dealer locator.') }}"
                                        >
                                            WhatsApp
                                        </x-ui.button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($dealers->hasPages())
                <div class="mt-10">
                    {{ $dealers->links() }}
                </div>
            @endif
        @endif
    </x-ui.section>

    {{-- BECOME A DEALER CROSS-LINK --}}
    <x-ui.section tone="dark" class="text-center">
        <x-ui.eyebrow tone="dark">Partnership</x-ui.eyebrow>
        <x-ui.heading size="md" class="mt-3">Don&rsquo;t see a dealer near you?</x-ui.heading>

        <p class="mx-auto mb-0 mt-2-5 mw-md fs-15 text-on-dark-muted">
            We&rsquo;re expanding the network every year. Reach out and we&rsquo;ll connect you
            with the closest stocked outlet, or talk to you about becoming one.
        </p>

        <div class="mt-6">
            <x-ui.button variant="inverse" size="lg" href="{{ route('contact').'#enquiry' }}">
                Contact us
            </x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.public>
