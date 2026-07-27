@props([
    /** Path under public/ for a looping video. */
    'video' => null,
    /** Poster for the video, or the image to show when no video is given. */
    'image' => null,
    'alt' => '',
    /** Small mono badge in the top-left corner. */
    'badge' => null,
    'height' => 'h-56 sm:h-72 lg:h-[21.25rem]',
    /** Adds a bottom scrim so an overlaid badge stays legible. */
    'scrim' => true,
])

{{--
    Rounded media frame used for the hero and infrastructure videos and for the
    solar photograph.

    The brief bans auto-rotating carousels as the hero (§5.4); a muted looping
    clip of the actual factory is the replacement. Video is decorative here, so
    it is muted, loops, and carries no audio track to miss.
--}}
<div {{ $attributes->class(['relative overflow-hidden rounded-frame bg-radix-dark shadow-[0_20px_46px_rgba(15,27,45,0.18)]', $height]) }}>
    @if ($video)
        <video
            @if ($image) poster="{{ $image }}" @endif
            autoplay
            muted
            loop
            playsinline
            preload="metadata"
            aria-hidden="true"
            tabindex="-1"
            class="absolute inset-0 h-full w-full object-cover"
        >
            <source src="{{ $video }}" type="video/mp4">
        </video>
    @elseif ($image)
        <img
            src="{{ $image }}"
            alt="{{ $alt }}"
            loading="lazy"
            decoding="async"
            class="absolute inset-0 h-full w-full object-cover"
        >
    @endif

    @if ($scrim)
        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-b from-transparent from-55% to-radix-dark/40"></div>
    @endif

    @if ($badge)
        <p class="absolute left-4 top-4 rounded-md bg-radix-red px-2.5 py-1.5 font-mono text-[0.625rem] uppercase tracking-eyebrow text-white">
            {{ $badge }}
        </p>
    @endif

    {{ $slot }}
</div>
