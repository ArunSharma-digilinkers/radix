@props([
    'label',
    'name',
    'options' => [],
    'placeholder' => null,
])

@php
    $id = $name.'-'.Str::random(6);
@endphp

{{--
    Underline-style select from the concept.

    The concept mocked these as styled <div>s. They are real <select> elements here:
    a native control is keyboard operable, announces its label, and works before any
    JavaScript loads. The finder is wired to real products in Phase 5.
--}}
<div {{ $attributes->class('min-w-0') }}>
    <label for="{{ $id }}" class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">
        {{ $label }}
    </label>

    <div class="relative mt-2">
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            class="peer w-full appearance-none border-0 border-b-2 border-line-control bg-transparent pb-2 pr-6 text-[0.9375rem] text-ink
                   focus:border-radix-red focus:outline-none focus:ring-0
                   [&:has(option[value='']:checked)]:text-placeholder"
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach ($options as $value => $text)
                <option value="{{ is_int($value) ? $text : $value }}">{{ $text }}</option>
            @endforeach
        </select>

        <span aria-hidden="true" class="pointer-events-none absolute bottom-2.5 right-0 text-radix-red-deep">&#9662;</span>
    </div>
</div>
