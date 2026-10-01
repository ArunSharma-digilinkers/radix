@props([
    /** light = on white/surface, dark = on --color-radix-dark */
    'tone' => 'light',
    /** default | xs — xs is for dense list items, not section headers. */
    'size' => 'default',
])

{{--
    Small uppercase mono label above a heading.

    At 10–11px this is "normal text" for WCAG, so it needs 4.5:1. The full-strength
    brand red only reaches 4.46:1 on --color-surface, hence the deeper red on light
    backgrounds and the lighter one on dark. See scripts/check-contrast.mjs.
--}}
<p {{ $attributes->class([
    'rx-eyebrow mb-0',
    'rx-eyebrow--xs' => $size === 'xs',
    'rx-eyebrow--dark' => $tone === 'dark',
]) }}>
    {{ $slot }}
</p>
