@props([
    /** hero | xl | lg | md */
    'size' => 'lg',
    'as' => 'h2',
])

@php
    // Archivo, tight tracking. Mobile sizes are roughly 60–70% of desktop so the
    // display type stays impactful without overflowing a 360px viewport.
    $sizes = [
        'hero' => 'text-[2.125rem] leading-[1.05] sm:text-5xl sm:leading-[1.02] lg:text-[3.5rem] lg:leading-none font-extrabold',
        'xl' => 'text-[1.75rem] leading-[1.1] sm:text-[2.25rem] lg:text-[2.75rem] lg:leading-[1.02] font-black',
        'lg' => 'text-[1.625rem] leading-[1.15] sm:text-[2rem] lg:text-[2.5rem] lg:leading-[1.05] font-black',
        'md' => 'text-[1.375rem] leading-[1.2] sm:text-[1.625rem] lg:text-[2rem] font-extrabold',
    ];
@endphp

<{{ $as }} {{ $attributes->class(['font-display tracking-display', $sizes[$size] ?? $sizes['lg']]) }}>
    {{ $slot }}
</{{ $as }}>
