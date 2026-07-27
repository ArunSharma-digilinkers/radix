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
    {{ $attributes->class([
        'group flex items-center gap-4 border-t border-hairline py-4 sm:gap-5 sm:py-5',
        'transition-transform duration-200 motion-safe:hover:translate-x-2 no-underline hover:no-underline' => (bool) $href,
    ]) }}
>
    <span class="w-6 shrink-0 font-display text-[0.9375rem] font-extrabold text-radix-red-deep">
        {{ $number }}
    </span>

    <span class="flex h-14 w-[4.5rem] shrink-0 items-center justify-center rounded-lg bg-surface-sunken p-1.5">
        @if ($image)
            {{-- Decorative: the product name sits immediately alongside, so alt
                 text here would just repeat it to a screen reader. --}}
            <img src="{{ $image }}" alt="" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain">
        @endif
    </span>

    <span class="min-w-0 flex-1">
        <span class="block font-display text-[0.9375rem] font-bold text-ink sm:text-base">{{ $name }}</span>
        @if ($pitch)
            <span class="mt-0.5 block text-xs text-muted sm:text-[0.78125rem]">{{ $pitch }}</span>
        @endif
    </span>

    <span aria-hidden="true" class="shrink-0 text-lg text-radix-red-deep">&rarr;</span>
</{{ $tag }}>
