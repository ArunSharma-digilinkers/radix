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
        <p class="mt-4 max-w-xl text-[0.9375rem] leading-relaxed text-muted">
            Every component in the design system, rendered live. Tokens come from
            <code class="font-mono text-[0.84375rem] text-ink">resources/css/app.css</code>;
            colour combinations are verified by <code class="font-mono text-[0.84375rem] text-ink">npm run check:contrast</code>.
        </p>
    </x-ui.section>

    {{-- COLOUR --}}
    <x-ui.section tone="surface" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Colour</x-ui.heading>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                'Brand' => ['radix-red', 'radix-red-deep', 'radix-red-on-dark', 'radix-dark', 'radix-dark-2'],
                'Text' => ['ink', 'ink-soft', 'nav', 'lead', 'muted', 'meta', 'placeholder'],
                'Surfaces' => ['surface', 'surface-raised', 'surface-sunken'],
                'Lines' => ['hairline', 'line', 'line-strong', 'line-control'],
            ] as $group => $tokens)
                <div>
                    <h3 class="font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">{{ $group }}</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach ($tokens as $token)
                            {{-- The swatch colour is set through the CSS variable rather
                                 than a `bg-{{ '{{ $token }}' }}` class: Tailwind scans for literal
                                 class strings, so an interpolated one never compiles. --}}
                            <li class="flex items-center gap-3">
                                <span
                                    class="size-8 shrink-0 rounded border border-hairline"
                                    style="background: var(--color-{{ $token }})"
                                ></span>
                                <code class="font-mono text-[0.75rem] text-ink">{{ $token }}</code>
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

        <div class="mt-6 space-y-6">
            @foreach (['hero' => 'Heading / hero', 'xl' => 'Heading / xl', 'lg' => 'Heading / lg', 'md' => 'Heading / md'] as $size => $label)
                <div>
                    <p class="font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">{{ $label }}</p>
                    <x-ui.heading :size="$size" as="p" class="mt-1.5 text-radix-dark">The battery brand India runs on.</x-ui.heading>
                </div>
            @endforeach

            <div>
                <p class="font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">Eyebrow / default &amp; xs</p>
                <x-ui.eyebrow class="mt-1.5">Explore the range</x-ui.eyebrow>
                <x-ui.eyebrow size="xs" class="mt-1">Maintenance tips</x-ui.eyebrow>
            </div>

            <div>
                <p class="font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">Body</p>
                <p class="mt-1.5 max-w-lg text-[0.9375rem] leading-relaxed text-lead">
                    Lead paragraph — IBM Plex Sans, used for section intros.
                </p>
                <p class="mt-2 max-w-lg text-[0.9375rem] leading-relaxed text-muted">
                    Secondary body copy for supporting detail.
                </p>
                <p class="mt-2 text-[0.78125rem] text-meta">Caption / byline text</p>
            </div>
        </div>
    </x-ui.section>

    {{-- BUTTONS --}}
    <x-ui.section tone="white" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Buttons</x-ui.heading>

        <div class="mt-6 space-y-5">
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button variant="primary" size="lg">Primary large</x-ui.button>
                <x-ui.button variant="primary" size="md">Primary</x-ui.button>
                <x-ui.button variant="secondary" size="lg">Secondary</x-ui.button>
            </div>
            <div class="flex flex-wrap items-center gap-3 rounded-card bg-radix-dark p-5">
                <x-ui.button variant="on-dark" size="lg">On dark</x-ui.button>
            </div>
            <div class="flex flex-wrap items-center gap-3 rounded-card bg-radix-red p-5">
                <x-ui.button variant="inverse" size="lg">Inverse</x-ui.button>
            </div>
        </div>
    </x-ui.section>

    {{-- STATS + CHIPS --}}
    <x-ui.section tone="surface" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Stats &amp; chips</x-ui.heading>

        <dl class="mt-6 grid grid-cols-2 gap-6 sm:gap-0 lg:grid-cols-4">
            @foreach (App\Support\Content\HomePageContent::stats() as $stat)
                <x-ui.stat :value="$stat['value']" :label="$stat['label']" class="first:border-t-0 sm:first:border-l-0 sm:first:pl-0" />
            @endforeach
        </dl>

        <ul class="mt-6 flex flex-wrap gap-2.5">
            @foreach (App\Support\Content\HomePageContent::processFlow() as $step)
                <x-ui.chip>{{ $step }}</x-ui.chip>
            @endforeach
        </ul>
    </x-ui.section>

    {{-- LISTS --}}
    <x-ui.section tone="white" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Index rows &amp; numbered items</x-ui.heading>

        <div class="mt-6 grid gap-x-10 sm:grid-cols-2">
            @foreach (array_slice(App\Support\Content\HomePageContent::products(), 0, 4) as $product)
                <x-ui.index-row
                    :number="$product['number']"
                    :name="$product['name']"
                    :pitch="$product['pitch']"
                    :image="asset('images/placeholder/'.$product['image'])"
                />
            @endforeach
        </div>

        <div class="mt-8 grid gap-x-14 sm:grid-cols-2">
            @foreach (array_slice(App\Support\Content\HomePageContent::whyRadix(), 0, 2) as $reason)
                <x-ui.numbered-item
                    divider="rule"
                    :number="$reason['number']"
                    :title="$reason['title']"
                    :description="$reason['description']"
                />
            @endforeach
        </div>
    </x-ui.section>

    <x-ui.section tone="dark" padding="tight">
        <x-ui.heading size="md">Numbered items on dark</x-ui.heading>
        <div class="mt-6 max-w-lg">
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
            <div class="grid gap-5 sm:grid-cols-3">
                @foreach (App\Support\Content\HomePageContent::finder() as $name => $field)
                    <x-ui.select-field
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

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <x-ui.media-frame
                image="{{ asset('images/placeholder/solar-array.jpg') }}"
                alt="Example media frame"
                badge="Badge"
                height="h-56"
            />

            @php $testimonials = App\Support\Content\HomePageContent::testimonials(); @endphp
            <x-ui.pull-quote
                variant="compact"
                :quote="$testimonials[1]['quote']"
                :name="$testimonials[1]['name']"
                :role="$testimonials[1]['role']"
            />
        </div>
    </x-ui.section>

    {{-- MAPS --}}
    <x-ui.section tone="white" padding="tight">
        <x-ui.heading size="md" class="text-radix-dark">Maps</x-ui.heading>
        <p class="mt-3 max-w-xl text-[0.875rem] leading-relaxed text-muted">
            Pre-rendered at build time by <code class="font-mono text-[0.8125rem] text-ink">npm run build:maps</code>.
            Inline SVG, no runtime JavaScript, colour inherited from the parent.
        </p>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <div class="rounded-frame border border-hairline bg-surface p-4 text-radix-red">
                <x-map.india />
            </div>
            <div class="rounded-frame border border-white/10 bg-radix-dark-2 p-4 text-radix-red-on-dark [--map-land:#22364f] [--map-line:#2e4664]">
                <x-map.world />
            </div>
        </div>
    </x-ui.section>
</x-layouts.public>
