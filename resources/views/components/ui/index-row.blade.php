@props([
    'number',
    'name',
    'pitch' => null,
    'image' => null,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class(['rx-index-row d-flex align-items-center gap-4 gap-sm-5 border-top border-hairline py-4 py-sm-5', 'rx-index-row--link' => (bool) $href]) }}
>
    <span class="rx-index-row__num flex-shrink-0 font-display fs-15 fw-extrabold text-radix-red-deep">
        {{ $number }}
    </span>

    <span class="rx-index-row__thumb d-flex flex-shrink-0 align-items-center justify-content-center bg-surface-sunken">
        @if ($image)
            {{-- Decorative: the product name sits immediately alongside, so alt
                 text here would just repeat it to a screen reader. --}}
            <img src="{{ $image }}" alt="" loading="lazy" decoding="async">
        @endif
    </span>

    <span class="min-w-0 flex-1">
        <span class="d-block font-display fs-15 fs-sm-16 fw-bold text-ink">{{ $name }}</span>
        @if ($pitch)
            <span class="d-block mt-0-5 fs-12 fs-sm-12-5 text-muted">{{ $pitch }}</span>
        @endif
    </span>

    <span aria-hidden="true" class="flex-shrink-0 fs-18 text-radix-red-deep">&rarr;</span>
</{{ $tag }}>
