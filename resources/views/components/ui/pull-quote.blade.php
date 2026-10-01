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
    'rounded-frame bg-white' => $variant === 'featured',
    'p-7 sm:p-10 shadow-[0_20px_46px_rgba(15,27,45,0.08)]' => $variant === 'featured',
    'rounded-card bg-white p-6 sm:p-7' => $variant === 'compact',
]) }}>
    @if ($variant === 'featured')
        <p aria-hidden="true" class="font-display text-4xl font-black leading-none text-radix-red">&ldquo;</p>
    @endif

    <blockquote @class([
        'font-display text-xl font-bold leading-snug tracking-display text-radix-dark sm:text-2xl lg:text-[1.625rem]' => $variant === 'featured',
        'text-[0.9375rem] leading-relaxed text-ink-soft' => $variant === 'compact',
        'mt-4' => $variant === 'featured',
    ])>
        {{ $quote }}
    </blockquote>

    <figcaption @class(['mt-5 flex items-center gap-3.5' => $variant === 'featured', 'mt-4' => $variant === 'compact'])>
        @if ($variant === 'featured')
            @if ($image)
                <img src="{{ $image }}" alt="" class="size-11 shrink-0 rounded-full object-cover">
            @else
                {{-- No photo on file for this testimonial yet — a neutral
                     placeholder, not a stock headshot (CLAUDE.md §6). --}}
                <span aria-hidden="true" class="size-11 shrink-0 rounded-full bg-[repeating-linear-gradient(135deg,#e7ebf1_0_8px,#f1f4f8_8px_16px)]"></span>
            @endif
        @endif
        <span>
            <span class="block font-display text-sm font-bold text-ink">{{ $name }}</span>
            @if ($role)
                <span class="mt-0.5 block text-[0.78125rem] text-meta">{{ $role }}</span>
            @endif
        </span>
    </figcaption>
</figure>
