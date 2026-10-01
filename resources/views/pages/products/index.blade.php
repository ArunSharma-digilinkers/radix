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

        <x-ui.heading as="h1" size="hero" class="mt-4 max-w-2xl text-radix-dark">
            @if ($category)
                {{ $category->getTranslation('name', 'en') }}
            @else
                Eight lines of <span class="text-radix-red">power</span>.
            @endif
        </x-ui.heading>

        <p class="mt-5 max-w-xl text-base leading-relaxed text-lead sm:text-[1.03125rem]">
            @if ($category && ($blurb = trim((string) $category->getTranslation('description', 'en'))))
                {{ $blurb }}
            @else
                Inverter, automotive, solar, e-rickshaw, bike and lithium — built in India,
                backed by a 650+ dealer network.
            @endif
        </p>

        @if ($categories->isNotEmpty())
            <nav aria-label="Product categories" class="mt-8">
                <ul class="flex flex-wrap gap-2">
                    <li>
                        <a
                            href="{{ route('products.index') }}"
                            @class([
                                'inline-flex rounded-full border px-3.5 py-2 text-[0.8125rem] font-medium transition-colors',
                                'border-radix-dark bg-radix-dark text-on-dark' => ! $category,
                                'border-line bg-white text-ink hover:border-line-control' => (bool) $category,
                            ])
                            @if (! $category) aria-current="page" @endif
                        >All products</a>
                    </li>

                    @foreach ($categories as $item)
                        @php $isActive = $category && $category->is($item); @endphp

                        <li>
                            <a
                                href="{{ route('products.category', $item) }}"
                                @class([
                                    'inline-flex rounded-full border px-3.5 py-2 text-[0.8125rem] font-medium transition-colors',
                                    'border-radix-dark bg-radix-dark text-on-dark' => $isActive,
                                    'border-line bg-white text-ink hover:border-line-control' => ! $isActive,
                                ])
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
                <p class="font-display text-[1.25rem] font-extrabold tracking-display text-radix-dark">
                    Nothing published here yet.
                </p>
                <p class="mx-auto mt-2 max-w-sm text-[0.9375rem] text-muted">
                    @if ($category)
                        Nothing in this category is live yet.
                        <a href="{{ route('products.index') }}" class="font-semibold text-radix-red-deep">See all products</a>.
                    @else
                        The full range is on its way — in the meantime,
                        <a href="{{ route('contact') }}" class="font-semibold text-radix-red-deep">get in touch</a>
                        and we'll point you to the right battery.
                    @endif
                </p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-site.product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </x-ui.section>
</x-layouts.public>
