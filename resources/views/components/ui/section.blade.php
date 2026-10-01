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
    // brief says 60–70% of traffic lives. The paddings themselves are in
    // scss/app.scss (.rx-section--*).
    //
    // Consecutive same-tone sections would otherwise stack two full paddings and
    // read as a gap, so `flush-top` exists for sections that continue the one
    // above rather than starting a new band.
    $paddings = ['default', 'tight', 'flush-top', 'band'];
@endphp

<{{ $as }}
    @if ($reveal) data-rev @endif
    {{ $attributes->class(['rx-section', 'rx-section--'.(in_array($padding, $paddings, true) ? $padding : 'default'), $tones[$tone] ?? $tones['white']]) }}
>
    @if ($contained)
        <div class="rx-container">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</{{ $as }}>
