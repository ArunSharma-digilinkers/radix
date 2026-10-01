@props([
    /** Livewire property names this panel binds to. */
    'titleModel' => 'metaTitle',
    'descriptionModel' => 'metaDescription',
    /** Falls back to these when the meta fields are left blank, matching what the page will actually output. */
    'fallbackTitleModel' => 'title',
    'fallbackDescriptionModel' => 'excerpt',
    'slugModel' => 'slug',
    /** Public URL prefix for the preview, e.g. "/blog/". */
    'basePath' => '/',
])

{{--
    Search-appearance panel.

    Counters and preview are pure Alpine reading $wire, so they update as the
    author types without a request per keystroke — the fields keep plain
    deferred `wire:model`, and only the display is live.

    The limits are advisory, not validation: Google truncates by pixel width,
    not characters, so a hard cap would be wrong. The counter turns red as a
    warning and the form still saves.
--}}
<section
    {{ $attributes->class('border-top border-hairline pt-6') }}
    x-data="{
        limits: { title: 60, description: 160 },
        slugify(value) {
            return (value || '')
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },
        get metaTitle() { return ($wire.{{ $titleModel }} || '').trim(); },
        get metaDescription() { return ($wire.{{ $descriptionModel }} || '').trim(); },
        get previewTitle() { return this.metaTitle || ($wire.{{ $fallbackTitleModel }} || '').trim() || 'Untitled post'; },
        get previewDescription() { return this.metaDescription || ($wire.{{ $fallbackDescriptionModel }} || '').trim(); },
        get previewPath() { return '{{ $basePath }}' + (this.slugify($wire.{{ $slugModel }}) || this.slugify($wire.{{ $fallbackTitleModel }})); },
    }"
>
    <div class="d-flex align-items-baseline justify-content-between gap-3">
        <div>
            <x-ui.eyebrow>Search appearance</x-ui.eyebrow>
            <h2 class="mt-1 font-display fs-17 fw-extrabold tracking-display text-radix-dark">Meta</h2>
        </div>
    </div>

    <p class="mt-1-5 mw-prose fs-13 text-muted">
        Leave a field blank to fall back to the post's own title and excerpt — that is exactly what the
        page will output, and the preview below shows the result either way.
    </p>

    <div class="mt-5 d-grid gap-5 grid-cols-sm-2">
        <div>
            <x-ui.text-field label="Meta title" name="meta_title" wire:model="{{ $titleModel }}" placeholder="Defaults to the post title" />
            <p class="mt-1-5 font-mono fs-10 text-uppercase tracking-eyebrow" :class="metaTitle.length > limits.title ? 'text-radix-red-deep' : 'text-meta'">
                <span x-text="metaTitle.length"></span> / <span x-text="limits.title"></span> characters
            </p>
        </div>

        <div>
            <x-ui.text-field label="Meta description" name="meta_description" wire:model="{{ $descriptionModel }}" textarea placeholder="Defaults to the excerpt" />
            <p class="mt-1-5 font-mono fs-10 text-uppercase tracking-eyebrow" :class="metaDescription.length > limits.description ? 'text-radix-red-deep' : 'text-meta'">
                <span x-text="metaDescription.length"></span> / <span x-text="limits.description"></span> characters
            </p>
        </div>
    </div>

    <div class="mt-5 rounded-card border border-hairline bg-surface-raised p-4">
        <x-ui.eyebrow size="xs">Preview</x-ui.eyebrow>

        <div class="mt-2-5">
            <p class="text-truncate fs-12 text-meta">
                {{ rtrim(config('app.url'), '/') }}<span x-text="previewPath"></span>
            </p>
            <p class="mt-0-5 fs-17 fw-medium lh-snug text-radix-dark" x-text="previewTitle"></p>
            <p class="mt-1 fs-13 lh-relaxed text-muted" x-text="previewDescription || 'No description yet — search engines will pick their own snippet.'"></p>
        </div>
    </div>
</section>
