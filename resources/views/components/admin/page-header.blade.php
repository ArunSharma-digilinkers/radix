@props([
    'title',
    'description' => null,
])

{{--
    The one page-title pattern for the panel: title left, actions right,
    stacked on mobile. Pass buttons through the `actions` slot so every screen
    puts its primary action in the same place.
--}}
<div {{ $attributes->class('mb-6 flex flex-col gap-4 border-b border-hairline pb-5 sm:flex-row sm:items-end sm:justify-between') }}>
    <div class="min-w-0">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $title }}</x-ui.heading>

        @if ($description)
            <p class="mt-1.5 text-[0.875rem] text-muted">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2.5">{{ $actions }}</div>
    @endisset
</div>
