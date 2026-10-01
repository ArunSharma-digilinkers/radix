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
        <div class="grid items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-12">
            <div>
                <x-ui.eyebrow>650+ network</x-ui.eyebrow>

                <x-ui.heading as="h1" size="hero" class="mt-4 text-radix-dark">
                    Find your nearest <span class="text-radix-red">dealer</span>.
                </x-ui.heading>

                <p class="mt-5 max-w-md text-base leading-relaxed text-lead sm:text-[1.03125rem]">
                    Search by city, state or PIN code to find a stocked Radix dealer,
                    distributor or service centre near you.
                </p>

                <form action="{{ route('dealers.index') }}" method="GET" class="mt-7 max-w-md">
                    <label for="dealer-search" class="sr-only">City, state or PIN code</label>
                    <div class="flex items-end gap-3 border-b-2 border-line-control focus-within:border-radix-red">
                        <input
                            id="dealer-search"
                            type="text"
                            name="location"
                            value="{{ $location }}"
                            placeholder="Enter city, state or PIN code…"
                            class="min-w-0 flex-1 border-0 bg-transparent pb-2.5 text-[0.9375rem] text-ink placeholder:text-placeholder focus:outline-none focus:ring-0"
                        >
                        <button type="submit" class="pb-2.5 text-[0.9375rem] font-bold text-radix-red-deep">
                            Search <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>

                    <ul class="mt-5 flex flex-wrap gap-2">
                        <li>
                            <a
                                href="{{ route('dealers.index', ['location' => $location]) }}"
                                @class([
                                    'inline-flex rounded-full border px-3.5 py-2 text-[0.8125rem] font-medium transition-colors',
                                    'border-radix-dark bg-radix-dark text-on-dark' => ! $type,
                                    'border-line bg-white text-ink hover:border-line-control' => (bool) $type,
                                ])
                                @if (! $type) aria-current="page" @endif
                            >All types</a>
                        </li>

                        @foreach ($typeLabels as $value => $label)
                            <li>
                                <a
                                    href="{{ route('dealers.index', ['location' => $location, 'type' => $value]) }}"
                                    @class([
                                        'inline-flex rounded-full border px-3.5 py-2 text-[0.8125rem] font-medium transition-colors',
                                        'border-radix-dark bg-radix-dark text-on-dark' => $type === $value,
                                        'border-line bg-white text-ink hover:border-line-control' => $type !== $value,
                                    ])
                                    @if ($type === $value) aria-current="page" @endif
                                >{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </form>
            </div>

            <div class="overflow-hidden rounded-frame border border-hairline bg-white p-4 text-radix-red">
                <x-map.india />
            </div>
        </div>
    </x-ui.section>

    {{-- RESULTS --}}
    <x-ui.section>
        @if ($dealers->isEmpty())
            <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-14 text-center">
                <p class="font-display text-[1.25rem] font-extrabold tracking-display text-radix-dark">
                    No dealers found.
                </p>
                <p class="mx-auto mt-2 max-w-sm text-[0.9375rem] text-muted">
                    @if ($location || $type)
                        Try a different city, state or PIN code.
                        <a href="{{ route('dealers.index') }}" class="font-semibold text-radix-red-deep">Clear search</a>.
                    @else
                        Dealer records are on their way.
                    @endif
                </p>
            </div>
        @else
            <p class="text-[0.84375rem] text-muted">
                Showing {{ $dealers->firstItem() }}&ndash;{{ $dealers->lastItem() }} of {{ $dealers->total() }} dealers.
            </p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($dealers as $dealer)
                    <div class="rounded-card border border-hairline bg-white p-5">
                        <span class="font-mono text-[0.625rem] uppercase tracking-eyebrow text-radix-red-deep">
                            {{ $typeLabels[$dealer->type] ?? $dealer->type }}
                        </span>

                        <p class="mt-2 font-display text-[1.0625rem] font-extrabold tracking-display text-radix-dark">
                            {{ $dealer->name }}
                        </p>

                        <p class="mt-2 text-[0.875rem] leading-relaxed text-muted">
                            @if ($dealer->address_line)
                                {{ $dealer->address_line }}<br>
                            @endif
                            {{ $dealer->city }}, {{ $dealer->state }}
                            @if ($dealer->pincode)
                                &mdash; {{ $dealer->pincode }}
                            @endif
                        </p>

                        @if ($dealer->phone || $dealer->whatsapp)
                            <div class="mt-4 flex flex-wrap gap-2.5">
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

        <p class="mx-auto mt-2.5 max-w-md text-[0.9375rem] text-on-dark-muted">
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
