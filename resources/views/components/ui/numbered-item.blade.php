@props([
    'number',
    'title',
    'description' => null,
    /** light | dark — matches the section it sits in. */
    'tone' => 'light',
    /** hairline (dark sections) | rule (the heavier 2px rule in "Why Radix") */
    'divider' => 'hairline',
])

<div {{ $attributes->class([
    'flex gap-4 py-5 sm:gap-5 sm:py-6',
    'border-t border-white/15' => $tone === 'dark',
    'border-t border-hairline' => $tone === 'light' && $divider === 'hairline',
    'border-t-2 border-ink' => $tone === 'light' && $divider === 'rule',
]) }}>
    <span @class([
        'shrink-0 font-display font-black leading-none tracking-display',
        'w-11 text-[1.625rem]' => $tone === 'dark',
        'w-12 text-[2rem] sm:text-[2.375rem]' => $tone === 'light',
        'text-radix-red-on-dark' => $tone === 'dark',
        'text-radix-red' => $tone === 'light',
    ])>{{ $number }}</span>

    <div class="min-w-0 flex-1">
        <p @class([
            'font-display font-bold',
            'text-[1.0625rem] text-on-dark sm:text-[1.1875rem]' => $tone === 'dark',
            'text-[1.0625rem] text-ink sm:text-[1.1875rem]' => $tone === 'light',
        ])>{{ $title }}</p>

        @if ($description)
            <p @class([
                'mt-1.5 text-[0.84375rem] leading-relaxed',
                'text-on-dark-muted' => $tone === 'dark',
                'text-muted' => $tone === 'light',
            ])>{{ $description }}</p>
        @endif
    </div>
</div>
