{{--
    Product detail (brief §4): hero, description, key specs, variants,
    datasheet downloads, use cases, FAQs, and a prefilled Enquire Now.

    The Solar Power Generating System (kind=solar_system) gets the bundled
    treatment PROJECT_PLAN.md calls for — each component explained on its
    own, plus the system as a whole — via product_components, which a plain
    battery line simply has none of.

    "Prefilled" Enquire Now passes the product as a query param; it has
    nowhere to land yet because Contact's enquiry form is still markup-only
    (Phase 5), same as noted on the Contact and Career pages.
--}}
@php
    $specGroups = $product->specs->groupBy(fn ($spec) => $spec->group ?: 'Specifications');
    $documentsByType = $product->documents->groupBy('type');
    $datasheets = $product->documents->where('type', \App\Models\ProductDocument::TYPE_DATASHEET);
@endphp
<x-layouts.public
    :title="$product->getTranslation('name', 'en')"
    :description="trim((string) $product->getTranslation('pitch', 'en')) ?: 'Radix '.$product->getTranslation('name', 'en').' — built in India, backed by a 650+ dealer network.'"
>
    {{-- HERO --}}
    <x-ui.section tone="surface" :reveal="false">
        <div class="grid items-center gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:gap-12">
            <div>
                <x-ui.eyebrow>{{ $product->category?->getTranslation('name', 'en') ?? 'Product' }}</x-ui.eyebrow>

                <x-ui.heading as="h1" size="hero" class="mt-4 text-radix-dark">
                    {{ $product->getTranslation('name', 'en') }}
                </x-ui.heading>

                @if ($pitch = trim((string) $product->getTranslation('pitch', 'en')))
                    <p class="mt-5 max-w-lg text-base leading-relaxed text-lead sm:text-[1.03125rem]">
                        {{ $pitch }}
                    </p>
                @endif

                <div class="mt-7 flex flex-wrap gap-3.5">
                    <x-ui.button variant="primary" size="lg" href="{{ route('contact', ['product' => $product->slug]).'#enquiry' }}">
                        Enquire Now
                    </x-ui.button>

                    @if ($datasheets->isNotEmpty())
                        <x-ui.button
                            variant="secondary"
                            size="lg"
                            href="{{ route('products.documents.download', [$product, $datasheets->first()]) }}"
                        >
                            Download datasheet
                        </x-ui.button>
                    @endif
                </div>
            </div>

            @if ($product->image)
                <x-ui.media-frame
                    image="{{ $product->image->url() }}"
                    alt="{{ $product->image->altText() }}"
                    height="h-64 sm:h-80"
                    :scrim="false"
                />
            @endif
        </div>

        @if ($product->gallery->isNotEmpty())
            <div class="mt-6 grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                @foreach ($product->gallery as $photo)
                    <div class="aspect-square overflow-hidden rounded-lg border border-hairline bg-white">
                        <img src="{{ $photo->url() }}" alt="{{ $photo->altText() }}" loading="lazy" class="h-full w-full object-contain p-2">
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.section>

    {{-- DESCRIPTION & USE CASES --}}
    @if ($description = trim((string) $product->getTranslation('description', 'en')))
        <x-ui.section tone="white">
            <x-ui.eyebrow>Overview</x-ui.eyebrow>
            <div class="mt-4 max-w-2xl space-y-4 text-[0.9375rem] leading-relaxed text-ink-soft">
                @foreach (explode("\n", $description) as $paragraph)
                    @continue(trim($paragraph) === '')
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    @if ($useCases = trim((string) $product->getTranslation('use_cases', 'en')))
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Where it's used</x-ui.eyebrow>
            <p class="mt-4 max-w-2xl text-[0.9375rem] leading-relaxed text-ink-soft">{{ $useCases }}</p>
        </x-ui.section>
    @endif

    {{-- SOLAR SYSTEM COMPONENTS — bundled treatment, kind=solar_system only --}}
    @if ($product->isSolarSystem() && $product->components->isNotEmpty())
        <x-ui.section tone="dark">
            <x-ui.eyebrow tone="dark">The complete system</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3">Four parts, one warranty.</x-ui.heading>

            <div class="mt-7 grid gap-x-14 sm:grid-cols-2">
                {{-- Not `$component`: Blade reserves that name inside a
                     component's slot, and the nested <x-…> tag below would
                     reassign it mid-loop (CLAUDE.md §6). --}}
                @foreach ($product->components as $index => $part)
                    <x-ui.numbered-item
                        tone="dark"
                        :number="sprintf('%02d', $index + 1)"
                        :title="$part->getTranslation('name', 'en')"
                        :description="trim((string) $part->getTranslation('description', 'en')) ?: null"
                    />
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- KEY SPECS --}}
    @if ($product->specs->isNotEmpty())
        <x-ui.section tone="white">
            <x-ui.eyebrow>Specifications</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Key specs.</x-ui.heading>

            <div class="mt-7 grid gap-8 sm:grid-cols-2">
                @foreach ($specGroups as $group => $specs)
                    <div>
                        @if ($group !== 'Specifications' || $specGroups->count() > 1)
                            <p class="font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">{{ $group }}</p>
                        @endif

                        <dl class="mt-2">
                            @foreach ($specs as $spec)
                                <div class="flex items-baseline justify-between gap-4 border-t border-hairline py-2.5 first:border-t-0">
                                    <dt class="text-[0.84375rem] text-muted">{{ $spec->getTranslation('label', 'en') }}</dt>
                                    <dd class="text-right text-[0.9375rem] font-semibold text-ink">{{ $spec->getTranslation('value', 'en') }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- VARIANTS --}}
    @if ($product->variants->isNotEmpty())
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Models</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Available variants.</x-ui.heading>

            <div class="mt-6 overflow-x-auto rounded-card border border-hairline bg-white">
                <table class="w-full min-w-[640px] text-left text-[0.84375rem]">
                    <thead class="border-b border-hairline bg-surface text-[0.71875rem] uppercase tracking-eyebrow text-meta">
                        <tr>
                            <th class="px-4 py-2.5">Model</th>
                            <th class="px-4 py-2.5">Capacity</th>
                            <th class="px-4 py-2.5">Voltage</th>
                            <th class="px-4 py-2.5">Warranty</th>
                            <th class="px-4 py-2.5">Dimensions</th>
                            <th class="px-4 py-2.5">Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->variants as $variant)
                            <tr class="border-b border-hairline last:border-b-0">
                                <td class="px-4 py-2.5 font-medium text-ink">
                                    {{ trim((string) $variant->getTranslation('name', 'en')) ?: $variant->model_code }}
                                </td>
                                <td class="px-4 py-2.5 text-ink-soft">{{ $variant->capacity_ah ? $variant->capacity_ah.' Ah' : '—' }}</td>
                                <td class="px-4 py-2.5 text-ink-soft">{{ $variant->voltage ? $variant->voltage.' V' : '—' }}</td>
                                <td class="px-4 py-2.5 text-ink-soft">{{ $variant->warranty_months ? $variant->warranty_months.' mo' : '—' }}</td>
                                <td class="px-4 py-2.5 text-ink-soft">{{ $variant->dimensions_mm ?: '—' }}</td>
                                <td class="px-4 py-2.5 text-ink-soft">{{ $variant->weight_kg ? $variant->weight_kg.' kg' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.section>
    @endif

    {{-- DOCUMENTS --}}
    @if ($product->documents->isNotEmpty())
        <x-ui.section tone="white">
            <x-ui.eyebrow>Downloads</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Datasheets &amp; documents.</x-ui.heading>

            <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                @foreach ($documentsByType as $type => $documents)
                    @foreach ($documents as $document)
                        <li>
                            <a
                                href="{{ route('products.documents.download', [$product, $document]) }}"
                                class="flex items-center justify-between gap-3 rounded-card border border-hairline bg-white px-4 py-3.5 text-[0.9375rem] font-medium text-ink hover:border-line-control"
                            >
                                <span>{{ $document->getTranslation('title', 'en') }}</span>
                                <span class="shrink-0 font-mono text-[0.625rem] uppercase tracking-eyebrow text-radix-red-deep">
                                    {{ $type }} &darr;
                                </span>
                            </a>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </x-ui.section>
    @endif

    {{-- FAQS --}}
    @if ($product->faqs->isNotEmpty())
        <x-ui.section tone="surface">
            <x-ui.eyebrow>FAQs</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Common questions.</x-ui.heading>

            <div class="mt-6 max-w-2xl divide-y divide-hairline">
                @foreach ($product->faqs as $faq)
                    <div class="py-4">
                        <p class="font-display text-base font-bold text-ink">{{ $faq->getTranslation('question', 'en') }}</p>
                        <p class="mt-1.5 text-[0.875rem] leading-relaxed text-muted">{{ $faq->getTranslation('answer', 'en') }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- CTA BAND --}}
    <x-ui.section tone="accent" padding="band" class="text-center">
        <x-ui.heading size="md" class="text-white">Ready to order {{ $product->getTranslation('name', 'en') }}?</x-ui.heading>

        <p class="mx-auto mt-2.5 max-w-xl text-[0.9375rem] text-white/90">
            Talk to our team or find a stocked dealer near you.
        </p>

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <x-ui.button variant="inverse" size="lg" href="{{ route('contact', ['product' => $product->slug]).'#enquiry' }}">
                Enquire Now
            </x-ui.button>
            <x-ui.button variant="on-dark" size="lg" href="{{ route('dealers.index') }}">
                Find a dealer
            </x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.public>
