@props([
    'label',
    'name',
    'options' => [],
    'placeholder' => null,
    /** Override the error-bag key; defaults to the wire:model property. */
    'error' => null,
    /** Pre-select an option on a plain (non-Livewire) form, e.g. :selected="old('type')". */
    'selected' => null,
])

@php
    $id = $name.'-'.Str::random(6);

    /*
     | Which key to read the validation error from.
     |
     | Livewire keys its error bag by PROPERTY name ("metaTitle"), while $name
     | here is the HTML name ("meta_title") — using $name would silently find
     | nothing and the field would look valid while the save quietly failed.
     | So the key comes from whatever wire:model variant the caller passed,
     | with an explicit `error` prop as the override for plain (non-Livewire)
     | forms.
     */
    $modelAttribute = collect($attributes->getAttributes())
        ->keys()
        ->first(fn (string $key): bool => str_starts_with($key, 'wire:model'));

    $errorKey = $error ?? ($modelAttribute ? $attributes->get($modelAttribute) : $name);
    $hasError = $errors->has($errorKey);
@endphp

{{--
    Underline-style select from the concept.

    The concept mocked these as styled <div>s. They are real <select> elements here:
    a native control is keyboard operable, announces its label, and works before any
    JavaScript loads. The finder is wired to real products in Phase 5.

    Only `class` reaches the outer wrapper (grid placement) — everything else
    (wire:model, wire:change, ...) goes on the actual <select>, or it would
    silently land on the wrapper and do nothing.
--}}
<div {{ $attributes->only('class')->class('min-w-0') }}>
    <label for="{{ $id }}" class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">
        {{ $label }}
    </label>

    <div class="relative mt-2">
        <select
            {{ $attributes->except('class') }}
            id="{{ $id }}"
            name="{{ $name }}"
            @if ($hasError) aria-invalid="true" @endif
            @class([
                'peer w-full appearance-none border-0 border-b-2 bg-transparent pb-2 pr-6 text-[0.9375rem] text-ink',
                'focus:border-radix-red focus:outline-none focus:ring-0',
                "[&:has(option[value='']:checked)]:text-placeholder",
                'border-line-control' => ! $hasError,
                'border-radix-red' => $hasError,
            ])
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach ($options as $value => $text)
                @php $optionValue = is_int($value) ? $text : $value; @endphp
                <option value="{{ $optionValue }}" @selected($selected !== null && (string) $selected === (string) $optionValue)>{{ $text }}</option>
            @endforeach
        </select>

        <span aria-hidden="true" class="pointer-events-none absolute bottom-2.5 right-0 text-radix-red-deep">&#9662;</span>
    </div>

    @error($errorKey)
        <p class="mt-1.5 text-[0.78125rem] text-radix-red-deep">{{ $message }}</p>
    @enderror
</div>
