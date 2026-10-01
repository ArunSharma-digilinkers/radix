{{--
    Living style guide.

    Brief §9 asks for a style guide delivered alongside the design "so it stays
    consistent as pages scale". Rendering the real components — rather than
    documenting them in a separate file that drifts — means this page is wrong the
    moment a component is, which is the point.

    Not routed in production; see routes/web.php.
--}}
<x-layouts.public title="Style guide">
    <x-ui.section tone="white" padding="tight">
        <x-ui.eyebrow>Internal</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 text-radix-dark">Style guide</x-ui.heading>
        <p class="mb-0 mt-4 mw-xl fs-15 lh-relaxed text-muted">
            Every component in the design system, rendered live. Tokens come from
            <code class="font-mono fs-13-5 text-ink">resources/scss/_tokens.scss</code>;
            colour combinations are verified by <code class="font-mono fs-13-5 text-ink">npm run check:contrast</code>.
        </p>
    </x-ui.section>

    {{-- COLOUR --}}
    <x-ui.section tone="surface" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Colour</x-ui.heading>

        <div class="row g-6 mt-2">
            @foreach ([
                'Brand' => ['radix-red', 'radix-red-deep', 'radix-red-on-dark', 'radix-dark', 'radix-dark-2'],
                'Text' => ['ink', 'ink-soft', 'nav', 'lead', 'muted', 'meta', 'placeholder'],
                'Surfaces' => ['surface', 'surface-raised', 'surface-sunken'],
                'Lines' => ['hairline', 'line', 'line-strong', 'line-control'],
            ] as $group => $tokens)
                <div class="col-sm-6 col-lg-3">
                    <h3 class="mb-0 font-mono fs-10 fw-normal text-uppercase tracking-eyebrow text-meta">{{ $group }}</h3>
                    <ul class="vstack gap-2 mt-3">
                        @foreach ($tokens as $token)
                            {{-- The swatch colour is read from the --color-* custom
                                 property that scss/_tokens-root.scss emits for every token. --}}
                            <li class="d-flex align-items-center gap-3">
                                <span
                                    class="rx-swatch flex-shrink-0 rounded border border-hairline"
                                    style="background: var(--color-{{ $token }})"
                                ></span>
                                <code class="font-mono fs-12 text-ink">{{ $token }}</code>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </x-ui.section>

    {{-- TYPE --}}
    <x-ui.section tone="white" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Type</x-ui.heading>

        <div class="vstack gap-6 mt-6">
            @foreach (['hero' => 'Heading / hero', 'xl' => 'Heading / xl', 'lg' => 'Heading / lg', 'md' => 'Heading / md'] as $size => $label)
                <div>
                    <p class="mb-0 font-mono fs-10 text-uppercase tracking-eyebrow text-meta">{{ $label }}</p>
                    <x-ui.heading :size="$size" as="p" class="mt-1-5 text-radix-dark">The battery brand India runs on.</x-ui.heading>
                </div>
            @endforeach

            <div>
                <p class="mb-0 font-mono fs-10 text-uppercase tracking-eyebrow text-meta">Eyebrow / default &amp; xs</p>
                <x-ui.eyebrow class="mt-1-5">Explore the range</x-ui.eyebrow>
                <x-ui.eyebrow size="xs" class="mt-1">Maintenance tips</x-ui.eyebrow>
            </div>

            <div>
                <p class="mb-0 font-mono fs-10 text-uppercase tracking-eyebrow text-meta">Body</p>
                <p class="mb-0 mt-1-5 mw-lg fs-15 lh-relaxed text-lead">
                    Lead paragraph — IBM Plex Sans, used for section intros.
                </p>
                <p class="mb-0 mt-2 mw-lg fs-15 lh-relaxed text-muted">
                    Secondary body copy for supporting detail.
                </p>
                <p class="mb-0 mt-2 fs-12-5 text-meta">Caption / byline text</p>
            </div>
        </div>
    </x-ui.section>

    {{-- BUTTONS --}}
    <x-ui.section tone="white" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Buttons</x-ui.heading>

        <div class="vstack gap-5 mt-6">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <x-ui.button variant="primary" size="lg">Primary large</x-ui.button>
                <x-ui.button variant="primary" size="md">Primary</x-ui.button>
                <x-ui.button variant="secondary" size="lg">Secondary</x-ui.button>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3 rounded-card bg-radix-dark p-5">
                <x-ui.button variant="on-dark" size="lg">On dark</x-ui.button>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3 rounded-card bg-radix-red p-5">
                <x-ui.button variant="inverse" size="lg">Inverse</x-ui.button>
            </div>
        </div>
    </x-ui.section>

    {{-- STATS + CHIPS --}}
    <x-ui.section tone="surface" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Stats &amp; chips</x-ui.heading>

        <dl class="rx-stats mb-0 mt-6">
            @foreach (App\Support\Content\HomePageContent::stats() as $stat)
                <x-ui.stat :value="$stat['value']" :label="$stat['label']" />
            @endforeach
        </dl>

        <ul class="d-flex flex-wrap gap-2-5 mt-6">
            @foreach (App\Support\Content\HomePageContent::processFlow() as $step)
                <x-ui.chip>{{ $step }}</x-ui.chip>
            @endforeach
        </ul>
    </x-ui.section>

    {{-- LISTS --}}
    <x-ui.section tone="white" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Index rows &amp; numbered items</x-ui.heading>

        {{-- Sample rows for layout reference only — real product data comes
             from App\Models\Product via ProductController. --}}
        <div class="row gx-10 mt-6">
            @foreach ([
                ['number' => '01', 'name' => 'Sample Line One', 'pitch' => 'One-line pitch for the index row.'],
                ['number' => '02', 'name' => 'Sample Line Two', 'pitch' => 'One-line pitch for the index row.'],
                ['number' => '03', 'name' => 'Sample Line Three', 'pitch' => 'One-line pitch for the index row.'],
                ['number' => '04', 'name' => 'Sample Line Four', 'pitch' => 'One-line pitch for the index row.'],
            ] as $product)
                <div class="col-sm-6">
                    <x-ui.index-row
                        :number="$product['number']"
                        :name="$product['name']"
                        :pitch="$product['pitch']"
                    />
                </div>
            @endforeach
        </div>

        <div class="row gx-14 mt-8">
            @foreach (array_slice(App\Support\Content\HomePageContent::whyRadix(), 0, 2) as $reason)
                <div class="col-sm-6">
                    <x-ui.numbered-item
                        divider="rule"
                        :number="$reason['number']"
                        :title="$reason['title']"
                        :description="$reason['description']"
                    />
                </div>
            @endforeach
        </div>
    </x-ui.section>

    <x-ui.section tone="dark" padding="tight">
        <x-ui.heading size="md">Numbered items on dark</x-ui.heading>
        <div class="mt-6 mw-lg">
            @foreach (array_slice(App\Support\Content\HomePageContent::solarComponents(), 0, 2) as $part)
                <x-ui.numbered-item
                    tone="dark"
                    :number="$part['number']"
                    :title="$part['title']"
                    :description="$part['description']"
                />
            @endforeach
        </div>
    </x-ui.section>

    {{-- FORM CONTROLS --}}
    <x-ui.section tone="surface" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Form controls</x-ui.heading>

        <div class="mt-6 rounded-frame border border-hairline bg-surface-raised p-6">
            <div class="row g-5">
                @foreach (App\Support\Content\HomePageContent::finder() as $name => $field)
                    <x-ui.select-field
                        class="col-sm-4"
                        :name="'sg-'.$name"
                        :label="$field['label']"
                        :placeholder="$field['placeholder']"
                        :options="$field['options']"
                    />
                @endforeach
            </div>
        </div>
    </x-ui.section>

    {{-- MEDIA + QUOTES --}}
    <x-ui.section tone="white" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Media frame &amp; pull-quote</x-ui.heading>

        <div class="row g-6 mt-2">
            <div class="col-lg-6">
                <x-ui.media-frame
                    image="{{ asset('images/placeholder/solar-array.jpg') }}"
                    alt="Example media frame"
                    badge="Badge"
                    height="rx-media--short"
                />
            </div>

            {{-- Sample copy for layout reference only — this page never ships
                 to production. Real testimonials come from App\Models\Testimonial. --}}
            <div class="col-lg-6">
                <x-ui.pull-quote
                    variant="compact"
                    class="h-100"
                    quote="Sample quote text for reviewing the compact pull-quote layout."
                    name="Component Preview"
                    role="Styleguide"
                />
            </div>
        </div>
    </x-ui.section>

    {{-- MAPS --}}
    <x-ui.section tone="white" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Maps</x-ui.heading>
        <p class="mb-0 mt-3 mw-xl fs-14 lh-relaxed text-muted">
            Pre-rendered at build time by <code class="font-mono fs-13 text-ink">npm run build:maps</code>.
            Inline SVG, no runtime JavaScript, colour inherited from the parent.
        </p>

        <div class="row g-6 mt-2">
            <div class="col-lg-6">
                <div class="rx-map-panel rounded-frame border border-hairline bg-surface p-4 text-radix-red">
                    <x-map.india />
                </div>
            </div>
            <div class="col-lg-6">
                <div class="rx-map-panel rx-map-panel--dark rounded-frame border rx-rule-on-dark bg-radix-dark-2 p-4 text-radix-red-on-dark">
                    <x-map.world />
                </div>
            </div>
        </div>
    </x-ui.section>
</x-layouts.public>
