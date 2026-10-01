@props([
    'quote',
    'name',
    'role' => null,
    /** Author photo URL, featured variant only. Falls back to a decorative placeholder. */
    'image' => null,
    /** featured renders the large card; compact renders the two beneath it. */
    'variant' => 'featured',
])

{{--
    The brief asks for testimonials with real names (§4) and flags that the current
    site has none (§6) — callers pass real App\Models\Testimonial rows
    (CLAUDE.md §8), never invented ones.
--}}
<figure {{ $attributes->class([
    'mb-0 bg-white',
    'rx-pull-quote rounded-frame p-7 p-sm-10' => $variant === 'featured',
    'rounded-card p-6 p-sm-7' => $variant === 'compact',
]) }}>
    @if ($variant === 'featured')
        <p aria-hidden="true" class="mb-0 font-display fs-36 fw-black lh-none text-radix-red">&ldquo;</p>
    @endif

    <blockquote @class([
        'mb-0',
        'mt-4 font-display fs-20 fs-sm-24 fs-lg-26 fw-bold lh-snug tracking-display text-radix-dark' => $variant === 'featured',
        'fs-15 lh-relaxed text-ink-soft' => $variant === 'compact',
    ])>
        {{ $quote }}
    </blockquote>

    <figcaption @class(['d-flex align-items-center gap-3-5 mt-5' => $variant === 'featured', 'mt-4' => $variant === 'compact'])>
        @if ($variant === 'featured')
            @if ($image)
                <img src="{{ $image }}" alt="" class="rx-avatar rounded-circle flex-shrink-0 object-fit-cover">
            @else
                {{-- No photo on file for this testimonial yet — a neutral
                     placeholder, not a stock headshot (CLAUDE.md §6). --}}
                <span aria-hidden="true" class="rx-avatar rx-avatar--placeholder rounded-circle flex-shrink-0"></span>
            @endif
        @endif
        <span>
            <span class="d-block font-display fs-14 fw-bold text-ink">{{ $name }}</span>
            @if ($role)
                <span class="d-block mt-0-5 fs-12-5 text-meta">{{ $role }}</span>
            @endif
        </span>
    </figcaption>
</figure>
