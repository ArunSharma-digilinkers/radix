@props([
    /** white | surface | dark | accent */
    'tone' => 'white',
    /** default | tight | flush-top | band */
    'padding' => 'default',
    /** Set false to opt out of the scroll reveal (e.g. the hero, which is above the fold). */
    'reveal' => true,
    /** Set false when the section manages its own inner container. */
    'contained' => true,
    'as' => 'section',
])

@php
    $tones = [
        'white' => 'bg-white text-ink',
        'surface' => 'bg-surface text-ink',
        'dark' => 'bg-radix-dark text-on-dark',
        'accent' => 'bg-radix-red text-white',
    ];

    // Concept spec is 74px/56px on desktop; scaled down for mobile, where the
    // brief says 60–70% of traffic lives.
    //
    // Consecutive same-tone sections would otherwise stack two full paddings and
    // read as a gap, so `flush-top` exists for sections that continue the one
    // above rather than starting a new band.
    $paddings = [
        'default' => 'px-6 py-14 sm:px-10 sm:py-16 lg:px-14 lg:py-[4.625rem]',
        'tight' => 'px-6 py-10 sm:px-10 sm:py-12 lg:px-14',
        'flush-top' => 'px-6 pb-14 pt-10 sm:px-10 sm:pb-16 sm:pt-12 lg:px-14 lg:pb-[4.625rem]',
        'band' => 'px-6 py-12 sm:px-10 sm:py-13 lg:px-14',
    ];
@endphp

<{{ $as }}
    @if ($reveal) data-rev @endif
    {{ $attributes->class([$tones[$tone] ?? $tones['white'], $paddings[$padding] ?? $paddings['default']]) }}
>
    @if ($contained)
        <div class="mx-auto max-w-radix">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</{{ $as }}>
