@props([
    'label',
    'name',
    /** Livewire property this field writes to, e.g. "body". */
    'model',
    'value' => null,
    'hint' => null,
    'required' => false,
    /** POST endpoint for image uploads; null hides the editor's image button entirely. */
    'uploadUrl' => null,
])

@php
    $id = $name.'-'.Str::random(6);
    $countId = $id.'-count';
@endphp

{{--
    CKEditor 5 field.

    Three things make this work with Livewire and all three are load-bearing:

    1. `wire:ignore` — CKEditor rewrites this subtree completely. Without it,
       Livewire's DOM morphing on the next render (a validation error, a file
       upload finishing) would fight the editor and blank it.
    2. Because of (1) the value can no longer flow through `wire:model`, so
       resources/js/rich-text.js pushes it with `$wire.set(model, html, false)`
       instead — deferred, so typing costs no requests.
    3. The initial HTML is echoed unescaped. It is safe because everything that
       reaches the database went through App\Support\Html\RichText first; if
       that ever stops being true, this is the line that turns it into stored
       XSS.
--}}
<div {{ $attributes->only('class')->class('min-w-0') }}>
    <label for="{{ $id }}" class="rx-label form-label">
        {{ $label }}@if ($required) <span aria-hidden="true" class="text-radix-red-deep">*</span>@endif
    </label>

    <div wire:ignore>
        <div
            id="{{ $id }}"
            data-rich-text
            data-model="{{ $model }}"
            data-count-target="#{{ $countId }}"
            @if ($uploadUrl) data-upload-url="{{ $uploadUrl }}" @endif
        >{!! $value !!}</div>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-1-5">
        <p class="mb-0 fs-12 text-meta">{{ $hint }}</p>
        <div id="{{ $countId }}"></div>
    </div>

    @error($model)
        <p class="mb-0 mt-1 fs-12-5 text-radix-red-deep">{{ $message }}</p>
    @enderror
</div>
