@props([
    'title',
    'description' => null,
])

{{--
    The one page-title pattern for the panel: title left, actions right,
    stacked on mobile. Pass buttons through the `actions` slot so every screen
    puts its primary action in the same place.
--}}
<div {{ $attributes->class('d-flex flex-column flex-sm-row align-items-sm-end justify-content-sm-between gap-4 mb-6 pb-5 border-bottom border-hairline') }}>
    <div class="min-w-0">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $title }}</x-ui.heading>

        @if ($description)
            <p class="mb-0 mt-1-5 fs-14 text-muted">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="d-flex flex-wrap align-items-center gap-2-5">{{ $actions }}</div>
    @endisset
</div>
