@props(['product'])

{{--
    Product hub card. Same "whole card isn't the link, the title is" treatment
    as x-site.post-card, for the same reason — an oversized link target makes
    the pitch text unselectable and reads as one giant link to a screen reader.
--}}
@php
    $image = $product->image;
@endphp

<article {{ $attributes->class('group relative flex flex-col overflow-hidden rounded-card border border-hairline bg-white') }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-surface-sunken">
        @if ($image)
            <img
                src="{{ $image->url() }}"
                alt="{{ $image->altText() }}"
                loading="lazy"
                class="h-full w-full object-contain p-6 transition-transform duration-500 group-hover:scale-[1.03]"
            >
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        @if ($product->category)
            <x-ui.eyebrow>{{ $product->category->getTranslation('name', 'en') }}</x-ui.eyebrow>
        @endif

        <h3 class="mt-2 font-display text-[1.0625rem] font-extrabold leading-snug tracking-display text-radix-dark">
            <a
                href="{{ route('products.show', $product) }}"
                class="after:absolute after:inset-0 after:content-[''] hover:text-radix-red-deep"
            >
                {{ $product->getTranslation('name', 'en') }}
            </a>
        </h3>

        @if ($pitch = trim((string) $product->getTranslation('pitch', 'en')))
            <p class="mt-2 text-[0.875rem] leading-relaxed text-muted">{{ $pitch }}</p>
        @endif
    </div>
</article>
