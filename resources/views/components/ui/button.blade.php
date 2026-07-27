@props([
    /** primary | secondary | inverse | on-dark */
    'variant' => 'primary',
    /** md | lg */
    'size' => 'md',
    'href' => null,
])

@php
    // Phase 1 has no destinations yet. Rendering a real <button> rather than an
    // <a href="#"> keeps the shell keyboard-operable and avoids dead links that
    // read as broken to screen readers. Phase 4 passes real hrefs.
    $tag = $href ? 'a' : 'button';

    $base = 'inline-flex items-center justify-center gap-2 rounded-btn font-semibold '
        .'transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2';

    $variants = [
        'primary' => 'bg-radix-red text-white font-bold hover:bg-radix-red-deep focus-visible:outline-radix-red-deep',
        'secondary' => 'border border-line-control text-radix-dark hover:border-radix-dark hover:bg-surface',
        'inverse' => 'bg-white text-radix-red-deep font-bold hover:bg-surface focus-visible:outline-white',
        'on-dark' => 'bg-white/15 text-white hover:bg-white/25 focus-visible:outline-white',
    ];

    $sizes = [
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3.5 text-sm sm:text-[0.9375rem]',
    ];
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="button" @endif
    {{ $attributes->class([$base, $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md']]) }}
>
    {{ $slot }}
</{{ $tag }}>
