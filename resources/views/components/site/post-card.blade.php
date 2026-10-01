@props([
    'post',
    /** Set true for the first card in a grid, which gets the larger treatment. */
    'lead' => false,
])

{{--
    Blog card. One component for the index grid, the category pages and the
    related-posts strip, so a post looks the same everywhere it is listed.

    The whole card is not a link — the title is. A card-sized link makes the
    excerpt unselectable and reads as one enormous link target to a screen
    reader; the ::after trick below keeps the big click area without that.
--}}
@php
    $image = $post->image;
    $category = $post->category;
@endphp

<article {{ $attributes->class('group relative flex flex-col overflow-hidden rounded-card border border-hairline bg-white') }}>
    <div @class(['relative overflow-hidden bg-surface-sunken', 'aspect-[16/9]' => ! $lead, 'aspect-[16/10]' => $lead])>
        @if ($image)
            <img
                src="{{ $image->url() }}"
                alt="{{ $image->altText() }}"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            >
        @endif
    </div>

    <div @class(['flex flex-1 flex-col p-5', 'sm:p-7' => $lead])>
        @if ($category)
            <x-ui.eyebrow>{{ $category->getTranslation('name', 'en') }}</x-ui.eyebrow>
        @endif

        <h3 @class([
            'font-display font-extrabold tracking-display text-radix-dark',
            'mt-2 text-[1.0625rem] leading-snug' => ! $lead,
            'mt-2.5 text-[1.375rem] leading-tight sm:text-[1.625rem]' => $lead,
        ])>
            <a
                href="{{ route('blog.show', $post) }}"
                class="after:absolute after:inset-0 after:content-[''] hover:text-radix-red-deep"
            >
                {{ $post->getTranslation('title', 'en') }}
            </a>
        </h3>

        @if ($excerpt = $post->getTranslation('excerpt', 'en'))
            <p @class(['mt-2 text-[0.875rem] leading-relaxed text-muted', 'sm:text-[0.9375rem]' => $lead])>
                {{ $excerpt }}
            </p>
        @endif

        <p class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 pt-1 font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">
            <span>{{ $post->authorName() }}</span>
            <span aria-hidden="true">·</span>
            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d M Y') }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ $post->readingTime() }} min read</span>
        </p>
    </div>
</article>
