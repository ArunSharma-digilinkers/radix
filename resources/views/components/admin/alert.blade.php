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
    {{ $attributes->class(['flex items-start gap-2.5 rounded-btn px-4 py-3 text-[0.84375rem] font-medium', $tone]) }}
>
    <x-admin.icon name="{{ $icon }}" class="mt-px h-[1.0625rem] w-[1.0625rem]" />
    <span>{{ $slot }}</span>
</div>
