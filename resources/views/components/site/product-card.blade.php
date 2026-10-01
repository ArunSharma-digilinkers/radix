@props(['product'])

{{--
    Product hub card. Same "whole card isn't the link, the title is" treatment
    as x-site.post-card, for the same reason — an oversized link target makes
    the pitch text unselectable and reads as one giant link to a screen reader.
    Bootstrap's .stretched-link does the same job as the old ::after trick.
--}}
@php
    $image = $product->image;
@endphp

<article {{ $attributes->class('rx-card position-relative d-flex flex-column h-100 overflow-hidden rounded-card border border-hairline bg-white') }}>
    <div class="rx-card__media ratio ratio-4x3 bg-surface-sunken">
        @if ($image)
            <img
                src="{{ $image->url() }}"
                alt="{{ $image->altText() }}"
                loading="lazy"
                class="object-fit-contain p-6"
            >
        @endif
    </div>

    <div class="d-flex flex-column flex-1 p-5">
        @if ($product->category)
            <x-ui.eyebrow>{{ $product->category->getTranslation('name', 'en') }}</x-ui.eyebrow>
        @endif

        <h3 class="mb-0 mt-2 font-display fs-17 fw-extrabold lh-snug tracking-display text-radix-dark">
            <a href="{{ route('products.show', $product) }}" class="stretched-link rx-title-link">
                {{ $product->getTranslation('name', 'en') }}
            </a>
        </h3>

        @if ($pitch = trim((string) $product->getTranslation('pitch', 'en')))
            <p class="mb-0 mt-2 fs-14 lh-relaxed text-muted">{{ $pitch }}</p>
        @endif
    </div>
</article>
