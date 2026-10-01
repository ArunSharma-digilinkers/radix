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
    reader; Bootstrap's .stretched-link keeps the big click area without that.
--}}
@php
    $image = $post->image;
    $category = $post->category;
@endphp

<article {{ $attributes->class('rx-card position-relative d-flex flex-column h-100 overflow-hidden rounded-card border border-hairline bg-white') }}>
    <div @class(['rx-card__media ratio bg-surface-sunken', 'ratio-16x9' => ! $lead, 'rx-ratio-16x10' => $lead])>
        @if ($image)
            <img
                src="{{ $image->url() }}"
                alt="{{ $image->altText() }}"
                loading="lazy"
                class="object-fit-cover"
            >
        @endif
    </div>

    <div @class(['d-flex flex-column flex-1 p-5', 'p-sm-7' => $lead])>
        @if ($category)
            <x-ui.eyebrow>{{ $category->getTranslation('name', 'en') }}</x-ui.eyebrow>
        @endif

        <h3 @class([
            'mb-0 font-display fw-extrabold tracking-display text-radix-dark',
            'mt-2 fs-17 lh-snug' => ! $lead,
            'mt-2-5 fs-22 fs-sm-26 lh-tight' => $lead,
        ])>
            <a href="{{ route('blog.show', $post) }}" class="stretched-link rx-title-link">
                {{ $post->getTranslation('title', 'en') }}
            </a>
        </h3>

        @if ($excerpt = $post->getTranslation('excerpt', 'en'))
            <p @class(['mb-0 mt-2 fs-14 lh-relaxed text-muted', 'fs-sm-15' => $lead])>
                {{ $excerpt }}
            </p>
        @endif

        <p class="rx-meta-line d-flex flex-wrap align-items-center column-gap-2 row-gap-1 mb-0 mt-4 pt-1 font-mono fs-10 text-uppercase tracking-eyebrow text-meta">
            <span>{{ $post->authorName() }}</span>
            <span aria-hidden="true">·</span>
            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d M Y') }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ $post->readingTime() }} min read</span>
        </p>
    </div>
</article>
