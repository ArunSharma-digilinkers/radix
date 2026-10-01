{{--
    Products hub — grid of product lines, filterable by category (brief §4).

    Same treatment as the blog index: nothing here is placeholder copy. On a
    fresh install this renders an honest empty state until the admin adds the
    eight lines through the Products admin (already built in Phase 3) —
    CLAUDE.md §8 applies here too, since a live SKU list is unverified until
    Radix confirms it.
--}}
<x-layouts.public
    :title="$category ? $category->getTranslation('name', 'en').' — Products' : 'Products'"
    :description="$category
        ? trim((string) $category->getTranslation('description', 'en')) ?: 'Radix '.$category->getTranslation('name', 'en').' — inverter, automotive, solar, e-rickshaw, bike and lithium batteries.'
        : 'Eight lines of Radix batteries — inverter, automotive, solar, solar power systems, e-rickshaw, bike and lithium. Built in India, backed by a 650+ dealer network.'"
>
    <x-ui.section tone="surface" :reveal="false">
        <x-ui.eyebrow>Products</x-ui.eyebrow>

        <x-ui.heading as="h1" size="hero" class="mt-4 mw-2xl text-radix-dark">
            @if ($category)
                {{ $category->getTranslation('name', 'en') }}
            @else
                Eight lines of <span class="text-radix-red">power</span>.
            @endif
        </x-ui.heading>

        <p class="mb-0 mt-5 mw-xl fs-16 fs-sm-16-5 lh-relaxed text-lead">
            @if ($category && ($blurb = trim((string) $category->getTranslation('description', 'en'))))
                {{ $blurb }}
            @else
                Inverter, automotive, solar, e-rickshaw, bike and lithium — built in India,
                backed by a 650+ dealer network.
            @endif
        </p>

        @if ($categories->isNotEmpty())
            <nav aria-label="Product categories" class="mt-8">
                <ul class="d-flex flex-wrap gap-2">
                    <li>
                        <a
                            href="{{ route('products.index') }}"
                            @class(['rx-chip rx-chip--link', 'rx-chip--active' => ! $category])
                            @if (! $category) aria-current="page" @endif
                        >All products</a>
                    </li>

                    @foreach ($categories as $item)
                        @php $isActive = $category && $category->is($item); @endphp

                        <li>
                            <a
                                href="{{ route('products.category', $item) }}"
                                @class(['rx-chip rx-chip--link', 'rx-chip--active' => $isActive])
                                @if ($isActive) aria-current="page" @endif
                            >{{ $item->getTranslation('name', 'en') }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif
    </x-ui.section>

    <x-ui.section>
        @if ($products->isEmpty())
            <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-14 text-center">
                <p class="mb-0 font-display fs-20 fw-extrabold tracking-display text-radix-dark">
                    Nothing published here yet.
                </p>
                <p class="mx-auto mb-0 mt-2 mw-sm fs-15 text-muted">
                    @if ($category)
                        Nothing in this category is live yet.
                        <a href="{{ route('products.index') }}" class="fw-semibold text-radix-red-deep">See all products</a>.
                    @else
                        The full range is on its way — in the meantime,
                        <a href="{{ route('contact') }}" class="fw-semibold text-radix-red-deep">get in touch</a>
                        and we'll point you to the right battery.
                    @endif
                </p>
            </div>
        @else
            <div class="row g-6">
                @foreach ($products as $product)
                    <div class="col-sm-6 col-lg-4">
                        <x-site.product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.section>
</x-layouts.public>
