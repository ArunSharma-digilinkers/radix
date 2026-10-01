@props([
    /** success | error */
    'variant' => 'success',
])

@php
    $styles = [
        'success' => ['bg-success-soft text-success', 'check'],
        'error' => ['bg-danger-soft text-radix-red-deep', 'alert'],
    ];

    [$tone, $icon] = $styles[$variant] ?? $styles['success'];
@endphp

<div
    role="{{ $variant === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->class(['d-flex align-items-start gap-2-5 rounded-btn px-4 py-3 fs-13-5 fw-medium', $tone]) }}
>
    <x-admin.icon name="{{ $icon }}" class="mt-px h-4-5 w-4-5 flex-shrink-0" />
    <span>{{ $slot }}</span>
</div>
