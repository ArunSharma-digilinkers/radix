@props([
    /** primary | secondary | inverse | on-dark */
    'variant' => 'primary',
    /** md | lg */
    'size' => 'md',
    'href' => null,
    /** button | submit | reset — ignored when $href is set. Forms must pass 'submit' explicitly. */
    'type' => 'button',
])

@php
    // Defaulting $type to 'button' (not 'submit') means a plain <x-ui.button>
    // inside a <form> does nothing on click unless the caller explicitly asks
    // for type="submit" — deliberate, so a form's real submit action is always
    // a conscious choice in the calling template, not an accident of this
    // component's default.
    $tag = $href ? 'a' : 'button';

    // Bootstrap's .btn plus the Radix variants defined in scss/app.scss.
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-outline-radix',
        'inverse' => 'btn-inverse',
        'on-dark' => 'btn-on-dark',
    ];

    $sizes = [
        'md' => '',
        'lg' => 'btn-lg',
    ];
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="{{ $type }}" @endif
    {{ $attributes->class(['btn d-inline-flex align-items-center justify-content-center gap-2', $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? '']) }}
>
    {{ $slot }}
</{{ $tag }}>
