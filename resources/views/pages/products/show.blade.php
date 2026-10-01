{{--
    Product detail (brief §4): hero, description, key specs, variants,
    datasheet downloads, use cases, FAQs, and a prefilled Enquire Now.

    The Solar Power Generating System (kind=solar_system) gets the bundled
    treatment PROJECT_PLAN.md calls for — each component explained on its
    own, plus the system as a whole — via product_components, which a plain
    battery line simply has none of.

    "Prefilled" Enquire Now passes the product as a query param; the Contact
    form reads it to pre-select the product and reason.
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
        <div class="row align-items-center gy-10 gx-lg-12">
            <div class="col-lg-6">
                <x-ui.eyebrow>{{ $product->category?->getTranslation('name', 'en') ?? 'Product' }}</x-ui.eyebrow>

                <x-ui.heading as="h1" size="hero" class="mt-4 text-radix-dark">
                    {{ $product->getTranslation('name', 'en') }}
                </x-ui.heading>

                @if ($pitch = trim((string) $product->getTranslation('pitch', 'en')))
                    <p class="mb-0 mt-5 mw-lg fs-16 fs-sm-16-5 lh-relaxed text-lead">
                        {{ $pitch }}
                    </p>
                @endif

                <div class="d-flex flex-wrap gap-3-5 mt-7">
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
                <div class="col-lg-6">
                    <x-ui.media-frame
                        image="{{ $product->image->url() }}"
                        alt="{{ $product->image->altText() }}"
                        height="rx-media--tall"
                        :scrim="false"
                    />
                </div>
            @endif
        </div>

        @if ($product->gallery->isNotEmpty())
            <div class="row row-cols-3 row-cols-sm-4 row-cols-lg-6 g-3 mt-3">
                @foreach ($product->gallery as $photo)
                    <div class="col">
                        <div class="ratio ratio-1x1 overflow-hidden rounded-lg border border-hairline bg-white">
                            <img src="{{ $photo->url() }}" alt="{{ $photo->altText() }}" loading="lazy" class="object-fit-contain p-2">
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.section>

    {{-- DESCRIPTION & USE CASES --}}
    @if ($description = trim((string) $product->getTranslation('description', 'en')))
        <x-ui.section tone="white">
            <x-ui.eyebrow>Overview</x-ui.eyebrow>
            <div class="vstack gap-4 mt-4 mw-2xl fs-15 lh-relaxed text-ink-soft">
                @foreach (explode("\n", $description) as $paragraph)
                    @continue(trim($paragraph) === '')
                    <p class="mb-0">{{ $paragraph }}</p>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    @if ($useCases = trim((string) $product->getTranslation('use_cases', 'en')))
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Where it's used</x-ui.eyebrow>
            <p class="mb-0 mt-4 mw-2xl fs-15 lh-relaxed text-ink-soft">{{ $useCases }}</p>
        </x-ui.section>
    @endif

    {{-- SOLAR SYSTEM COMPONENTS — bundled treatment, kind=solar_system only --}}
    @if ($product->isSolarSystem() && $product->components->isNotEmpty())
        <x-ui.section tone="dark">
            <x-ui.eyebrow tone="dark">The complete system</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3">Four parts, one warranty.</x-ui.heading>

            <div class="row gx-14 mt-7">
                {{-- Not `$component`: Blade reserves that name inside a
                     component's slot, and the nested <x-…> tag below would
                     reassign it mid-loop (CLAUDE.md §6). --}}
                @foreach ($product->components as $index => $part)
                    <div class="col-sm-6">
                        <x-ui.numbered-item
                            tone="dark"
                            :number="sprintf('%02d', $index + 1)"
                            :title="$part->getTranslation('name', 'en')"
                            :description="trim((string) $part->getTranslation('description', 'en')) ?: null"
                        />
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- KEY SPECS --}}
    @if ($product->specs->isNotEmpty())
        <x-ui.section tone="white">
            <x-ui.eyebrow>Specifications</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Key specs.</x-ui.heading>

            <div class="row g-8 mt-4">
                @foreach ($specGroups as $group => $specs)
                    <div class="col-sm-6">
                        @if ($group !== 'Specifications' || $specGroups->count() > 1)
                            <p class="mb-0 font-mono fs-10 text-uppercase tracking-eyebrow text-meta">{{ $group }}</p>
                        @endif

                        <dl class="mb-0 mt-2">
                            @foreach ($specs as $spec)
                                <div class="rx-spec-row d-flex align-items-baseline justify-content-between gap-4 py-2-5">
                                    <dt class="fw-normal fs-13-5 text-muted">{{ $spec->getTranslation('label', 'en') }}</dt>
                                    <dd class="mb-0 text-end fs-15 fw-semibold text-ink">{{ $spec->getTranslation('value', 'en') }}</dd>
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

            <div class="table-responsive mt-6 rounded-card border border-hairline bg-white">
                <table class="rx-table table mb-0 fs-13-5">
                    <thead>
                        <tr>
                            <th scope="col">Model</th>
                            <th scope="col">Capacity</th>
                            <th scope="col">Voltage</th>
                            <th scope="col">Warranty</th>
                            <th scope="col">Dimensions</th>
                            <th scope="col">Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->variants as $variant)
                            <tr>
                                <td class="fw-medium text-ink">
                                    {{ trim((string) $variant->getTranslation('name', 'en')) ?: $variant->model_code }}
                                </td>
                                <td class="text-ink-soft">{{ $variant->capacity_ah ? $variant->capacity_ah.' Ah' : '—' }}</td>
                                <td class="text-ink-soft">{{ $variant->voltage ? $variant->voltage.' V' : '—' }}</td>
                                <td class="text-ink-soft">{{ $variant->warranty_months ? $variant->warranty_months.' mo' : '—' }}</td>
                                <td class="text-ink-soft">{{ $variant->dimensions_mm ?: '—' }}</td>
                                <td class="text-ink-soft">{{ $variant->weight_kg ? $variant->weight_kg.' kg' : '—' }}</td>
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

            <ul class="row g-3 mt-3">
                @foreach ($documentsByType as $type => $documents)
                    @foreach ($documents as $document)
                        <li class="col-sm-6">
                            <a
                                href="{{ route('products.documents.download', [$product, $document]) }}"
                                class="rx-doc-link d-flex align-items-center justify-content-between gap-3 rounded-card border border-hairline bg-white px-4 py-3-5 fs-15 fw-medium text-ink"
                            >
                                <span>{{ $document->getTranslation('title', 'en') }}</span>
                                <span class="flex-shrink-0 font-mono fs-10 text-uppercase tracking-eyebrow text-radix-red-deep">
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

            <div class="mt-6 mw-2xl">
                @foreach ($product->faqs as $faq)
                    <div class="border-top border-hairline py-4">
                        <p class="mb-0 font-display fs-16 fw-bold text-ink">{{ $faq->getTranslation('question', 'en') }}</p>
                        <p class="mb-0 mt-1-5 fs-14 lh-relaxed text-muted">{{ $faq->getTranslation('answer', 'en') }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- CTA BAND --}}
    <x-ui.section tone="accent" padding="band" class="text-center">
        <x-ui.heading size="md" class="text-white">Ready to order {{ $product->getTranslation('name', 'en') }}?</x-ui.heading>

        <p class="mx-auto mb-0 mt-2-5 mw-xl fs-15 text-white">
            Talk to our team or find a stocked dealer near you.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-6">
            <x-ui.button variant="inverse" size="lg" href="{{ route('contact', ['product' => $product->slug]).'#enquiry' }}">
                Enquire Now
            </x-ui.button>
            <x-ui.button variant="on-dark" size="lg" href="{{ route('dealers.index') }}">
                Find a dealer
            </x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.public>
