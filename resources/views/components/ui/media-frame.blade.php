@props([
    /** Path under public/ for a looping video. */
    'video' => null,
    /** Poster for the video, or the image to show when no video is given. */
    'image' => null,
    'alt' => '',
    /** Small mono badge in the top-left corner. */
    'badge' => null,
    /** Extra classes for the frame's height — see .rx-media in scss/_components.scss. */
    'height' => 'rx-media--default',
    /** Adds a bottom scrim so an overlaid badge stays legible. */
    'scrim' => true,
    /** Below-the-fold video: nothing downloads until it scrolls near the viewport. */
    'lazy' => false,
])

{{--
    Rounded media frame used for the hero and infrastructure videos and for the
    solar photograph.

    The brief bans auto-rotating carousels as the hero (§5.4); a muted looping
    clip of the actual factory is the replacement. Video is decorative here, so
    it is muted, loops, and carries no audio track to miss.
--}}
<div {{ $attributes->class(['rx-media position-relative overflow-hidden rounded-frame bg-radix-dark', $height]) }}>
    @if ($video)
        <video
            @if ($image) poster="{{ $image }}" @endif
            @unless ($lazy) autoplay @endunless
            muted
            loop
            playsinline
            preload="{{ $lazy ? 'none' : 'metadata' }}"
            @if ($lazy) data-lazy-video @endif
            aria-hidden="true"
            tabindex="-1"
            class="rx-media__fill"
        >
            <source {{ $lazy ? 'data-src' : 'src' }}="{{ $video }}" type="video/mp4">
        </video>
    @elseif ($image)
        <img
            src="{{ $image }}"
            alt="{{ $alt }}"
            loading="lazy"
            decoding="async"
            class="rx-media__fill"
        >
    @endif

    @if ($scrim)
        <div aria-hidden="true" class="rx-media__scrim"></div>
    @endif

    @if ($badge)
        <p class="rx-media__badge position-absolute mb-0 rounded-md bg-radix-red text-white">
            {{ $badge }}
        </p>
    @endif

    {{ $slot }}
</div>
