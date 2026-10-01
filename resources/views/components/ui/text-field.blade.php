@props([
    'label',
    'name',
    'type' => 'text',
    'placeholder' => null,
    'textarea' => false,
    'required' => false,
    'value' => null,
    /** Override the error-bag key; defaults to the wire:model property. */
    'error' => null,
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
    Underline-style text input/textarea, matching x-ui.select-field's visual
    language so a mixed form (Contact page) reads as one system. A Bootstrap
    .form-control restyled by .rx-input (scss/_components.scss).

    Only `class` reaches the outer wrapper (for grid placement like
    `class="col-sm-12"`) — everything else the caller passes
    (wire:model, autofocus, wire:model.live, ...) goes on the actual
    <input>/<textarea>, not the wrapper, or it would silently do nothing.
--}}
<div {{ $attributes->only('class')->class('min-w-0') }}>
    <label for="{{ $id }}" class="rx-label form-label">
        {{ $label }}@if ($required) <span aria-hidden="true" class="text-radix-red-deep">*</span>@endif
    </label>

    @if ($textarea)
        <textarea
            {{ $attributes->except('class') }}
            id="{{ $id }}"
            name="{{ $name }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            rows="4"
            @if ($hasError) aria-invalid="true" @endif
            @class(['form-control rx-input resize-none', 'is-invalid' => $hasError])
        >{{ $value }}</textarea>
    @else
        <input
            {{ $attributes->except('class') }}
            id="{{ $id }}"
            type="{{ $type }}"
            name="{{ $name }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            @if ($value !== null) value="{{ $value }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            @class(['form-control rx-input', 'is-invalid' => $hasError])
        >
    @endif

    @error($errorKey)
        <p class="mb-0 mt-1-5 fs-12-5 text-radix-red-deep">{{ $message }}</p>
    @enderror
</div>
