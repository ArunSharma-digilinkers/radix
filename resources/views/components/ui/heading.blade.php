@props([
    /** hero | xl | lg | md */
    'size' => 'lg',
    'as' => 'h2',
])

{{--
    Archivo, tight tracking. Mobile sizes are roughly 60–70% of desktop so the
    display type stays impactful without overflowing a 360px viewport. The scale
    lives in scss/app.scss (.rx-heading--*).
--}}
<{{ $as }} {{ $attributes->class(['rx-heading mb-0', 'rx-heading--'.(in_array($size, ['hero', 'xl', 'lg', 'md'], true) ? $size : 'lg')]) }}>
    {{ $slot }}
</{{ $as }}>
