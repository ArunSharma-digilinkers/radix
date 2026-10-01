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
    'd-flex gap-4 gap-sm-5 py-5 py-sm-6 border-top',
    'rx-rule-on-dark' => $tone === 'dark',
    'border-hairline' => $tone === 'light' && $divider === 'hairline',
    'border-2 border-ink' => $tone === 'light' && $divider === 'rule',
]) }}>
    <span @class([
        'flex-shrink-0 font-display fw-black lh-none tracking-display',
        'rx-numbered__num rx-numbered__num--dark fs-26 text-radix-red-on-dark' => $tone === 'dark',
        'rx-numbered__num rx-numbered__num--light fs-32 fs-sm-38 text-radix-red' => $tone === 'light',
    ])>{{ $number }}</span>

    <div class="min-w-0 flex-1">
        <p @class([
            'mb-0 font-display fw-bold fs-17 fs-sm-19',
            'text-on-dark' => $tone === 'dark',
            'text-ink' => $tone === 'light',
        ])>{{ $title }}</p>

        @if ($description)
            <p @class([
                'mb-0 mt-1-5 fs-13-5 lh-relaxed',
                'text-on-dark-muted' => $tone === 'dark',
                'text-muted' => $tone === 'light',
            ])>{{ $description }}</p>
        @endif
    </div>
</div>
