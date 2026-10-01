{{--
    Homepage — "Editorial Red" direction.

    Section order follows the approved concept. Content comes from
    App\Support\Content\HomePageContent; Phase 4 swaps that class for real
    queries without touching this file.
--}}
<x-layouts.public
    title="Batteries built to last | Inverter, Automotive, Solar & Lithium"
    description="Radix Power Solutions manufactures inverter, automotive, solar, e-rickshaw and lithium batteries, backed by 25 years of manufacturing and a 650+ dealer network across India."
>
    {{-- HERO — a looping factory clip, not a carousel (brief §5.4) --}}
    <x-ui.section tone="surface" :reveal="false">
        <div class="row align-items-center gy-9 gx-lg-11">
            <div class="col-lg-6">
                <x-ui.eyebrow>25 years of power &middot; made in India</x-ui.eyebrow>

                <x-ui.heading as="h1" size="hero" class="mt-4 text-radix-dark">
                    The battery brand India <span class="text-radix-red">runs on</span>.
                </x-ui.heading>

                <p class="mb-0 mt-5 mw-md fs-16 fs-sm-16-5 lh-relaxed text-lead">
                    Inverter, automotive, solar and lithium — plus complete solar systems
                    and a 650-dealer network to back them.
                </p>

                <div class="d-flex flex-wrap gap-3-5 mt-7">
                    <x-ui.button variant="primary" size="lg" href="#finder">Find Your Battery</x-ui.button>
                    <x-ui.button variant="secondary" size="lg" href="{{ route('dealers.index') }}">Become a dealer</x-ui.button>
                </div>
            </div>

            <div class="col-lg-6">
                <x-ui.media-frame
                    video="{{ asset('video/factory-hero.mp4') }}"
                    badge="Live from the floor"
                />
            </div>
        </div>
    </x-ui.section>

    {{-- TRUST STATS — the brief asks for these to be headline figures, not buried --}}
    <x-ui.section tone="white" padding="tight" class="border-bottom border-hairline">
        <dl class="rx-stats mb-0">
            @foreach (App\Support\Content\HomePageContent::stats() as $stat)
                <x-ui.stat :value="$stat['value']" :label="$stat['label']" />
            @endforeach
        </dl>
    </x-ui.section>

    {{-- FIND YOUR BATTERY — quick-select from brief §4 --}}
    <x-ui.section tone="white" padding="flush-top" id="finder">
        <div class="rounded-frame border border-hairline bg-surface-raised p-6 p-sm-7">
            <h2 class="mb-0 font-display fs-20 fw-extrabold tracking-display text-radix-dark">Find Your Battery</h2>

            <form class="row g-5 align-items-end mt-0">
                @foreach (App\Support\Content\HomePageContent::finder() as $name => $field)
                    <x-ui.select-field
                        class="col-sm-6 col-lg"
                        :name="$name"
                        :label="$field['label']"
                        :placeholder="$field['placeholder']"
                        :options="$field['options']"
                    />
                @endforeach

                <div class="col-12 col-lg-auto">
                    <x-ui.button type="submit" variant="primary" size="lg" class="w-100">
                        Show results
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.section>

    {{-- PRODUCTS — editorial numbered index. Real Product rows (CLAUDE.md §8):
         empty until the admin adds them, same as the blog strip below. --}}
    @if ($products->isNotEmpty())
        <x-ui.section tone="white" padding="flush-top" id="products">
            <x-ui.eyebrow>Explore the range</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Eight lines of power.</x-ui.heading>

            <div class="row gx-10 mt-6">
                @foreach ($products as $product)
                    <div class="col-sm-6">
                        <x-ui.index-row
                            :number="sprintf('%02d', $loop->iteration)"
                            :name="$product->getTranslation('name', 'en')"
                            :pitch="trim((string) $product->getTranslation('pitch', 'en')) ?: null"
                            :image="$product->image?->url()"
                            :href="route('products.show', $product)"
                        />
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                <x-ui.button variant="secondary" size="md" href="{{ route('products.index') }}">
                    See all products
                </x-ui.button>
            </div>
        </x-ui.section>
    @endif

    {{-- SOLAR — the bundled system the current site never shows (brief §6) --}}
    <x-ui.section tone="dark" id="solar">
        <div class="row align-items-center gy-10 gx-lg-13">
            <div class="col-lg-6">
                <x-ui.eyebrow tone="dark">The complete solution</x-ui.eyebrow>

                <x-ui.heading size="xl" class="mt-3-5">Solar, sold as one system.</x-ui.heading>

                <p class="mb-0 mt-4 fs-15 fs-sm-16 lh-relaxed text-on-dark-muted">
                    Panel, battery, inverter and charge controller — matched and warrantied
                    together, not four parts you have to reconcile yourself.
                </p>

                <x-ui.media-frame
                    class="mt-6"
                    image="{{ asset('images/placeholder/solar-array.jpg') }}"
                    alt="A Radix solar power generating system installed on a rooftop"
                    height="rx-media--short"
                    :scrim="false"
                />
            </div>

            <div class="col-lg-6">
                {{-- Not `$component`: Blade reserves that name inside a component's
                     slot, and a nested <x-…> tag reassigns it mid-loop. --}}
                @foreach (App\Support\Content\HomePageContent::solarComponents() as $part)
                    <x-ui.numbered-item
                        tone="dark"
                        :number="$part['number']"
                        :title="$part['title']"
                        :description="$part['description']"
                    />
                @endforeach
            </div>
        </div>
    </x-ui.section>

    {{-- WHY RADIX --}}
    <x-ui.section tone="white" id="why">
        <x-ui.eyebrow>Why Radix</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 text-radix-dark">Built like a national brand.</x-ui.heading>

        <div class="row gx-14 mt-7">
            @foreach (App\Support\Content\HomePageContent::whyRadix() as $reason)
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

    {{-- INFRASTRUCTURE --}}
    <x-ui.section tone="surface" id="infrastructure">
        <div class="row align-items-center gy-10 gx-lg-12">
            <div class="col-lg-5">
                <x-ui.eyebrow>Inside the factory</x-ui.eyebrow>

                <x-ui.heading size="md" class="mt-3 text-radix-dark">See where the power is made.</x-ui.heading>

                <p class="mb-0 mt-4 fs-15 lh-relaxed text-muted">
                    A walk through the production floor, QC lab and testing bays — the
                    credibility a spec sheet alone can't give.
                </p>

                <ul class="d-flex flex-wrap gap-2-5 mt-5">
                    @foreach (App\Support\Content\HomePageContent::processFlow() as $step)
                        <x-ui.chip>{{ $step }}</x-ui.chip>
                    @endforeach
                </ul>

                <div class="mt-6">
                    <x-ui.button variant="secondary" size="md" href="{{ route('infrastructure.index') }}">
                        See the full facility
                    </x-ui.button>
                </div>
            </div>

            <div class="col-lg-7">
                <x-ui.media-frame
                    video="{{ asset('video/factory-floor.mp4') }}"
                    :lazy="true"
                    badge="Live from the floor"
                />
            </div>
        </div>
    </x-ui.section>

    {{-- DEALER LOCATOR --}}
    <x-ui.section tone="white" id="dealers">
        <div class="row align-items-center gy-10 gx-lg-12">
            <div class="col-lg-7">
                <div class="rx-map-panel overflow-hidden rounded-frame border border-hairline bg-surface p-4 text-radix-red">
                    <x-map.india />
                </div>
            </div>

            <div class="col-lg-5">
                <x-ui.eyebrow>650+ network</x-ui.eyebrow>

                <x-ui.heading size="md" class="mt-3 text-radix-dark">Find your nearest dealer.</x-ui.heading>

                <p class="mb-0 mt-3-5 fs-14-5 lh-relaxed text-muted">
                    Search by city or state and connect with a stocked Radix dealer near you.
                </p>

                {{-- Text search runs against real dealer records at /dealers.
                     Ordering by distance instead of city name needs geocoding,
                     which is Phase 5. --}}
                <form action="{{ route('dealers.index') }}" method="GET" class="mt-5">
                    <label for="dealer-search" class="visually-hidden">City, state or PIN code</label>
                    <div class="rx-inline-search d-flex align-items-end gap-3">
                        <input
                            id="dealer-search"
                            type="text"
                            name="location"
                            placeholder="Enter city or PIN code…"
                            class="min-w-0 flex-1"
                        >
                        <button type="submit" class="rx-inline-search__submit fs-15 fw-bold text-radix-red-deep">
                            Search <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.section>

    {{-- EXPORT --}}
    <x-ui.section tone="dark" id="export">
        <div class="row align-items-center gy-10 gx-lg-12">
            <div class="col-lg-5">
                <x-ui.eyebrow tone="dark">Export &middot; B2B</x-ui.eyebrow>

                <x-ui.heading size="md" class="mt-3">Trusted across borders.</x-ui.heading>

                <ul class="mt-5">
                    @foreach (App\Support\Content\HomePageContent::exportMarkets() as $market)
                        <li class="border-top rx-rule-on-dark py-2-5 font-display fs-17 fs-sm-18 fw-bold text-on-dark">
                            {{ $market }}
                        </li>
                    @endforeach
                </ul>

                <x-ui.button variant="primary" size="lg" class="mt-6" href="{{ route('export.index').'#enquire' }}">
                    Request an export quote <span aria-hidden="true">&rarr;</span>
                </x-ui.button>
            </div>

            <div class="col-lg-7">
                <div class="rx-map-panel rx-map-panel--dark overflow-hidden rounded-frame border rx-rule-on-dark bg-radix-dark-2 p-4 text-radix-red-on-dark">
                    <x-map.world />
                </div>
            </div>
        </div>
    </x-ui.section>

    {{-- TESTIMONIALS — real, named quotes (CLAUDE.md §8); hidden until the
         admin adds at least one, same treatment as every other unverified
         section on this site. --}}
    @if ($testimonials->isNotEmpty())
        <x-ui.section tone="surface">
            <x-ui.pull-quote
                variant="featured"
                :quote="$testimonials[0]->getTranslation('quote', 'en')"
                :name="$testimonials[0]->author_name"
                :role="trim(collect([$testimonials[0]->getTranslation('author_role', 'en'), $testimonials[0]->location])->filter()->implode(', '))"
                :image="$testimonials[0]->image?->url()"
            />

            @if ($testimonials->count() > 1)
                <div class="row g-5 mt-0">
                    @foreach ($testimonials->slice(1) as $testimonial)
                        <div class="col-sm-6">
                            <x-ui.pull-quote
                                variant="compact"
                                class="h-100"
                                :quote="$testimonial->getTranslation('quote', 'en')"
                                :name="$testimonial->author_name"
                                :role="trim(collect([$testimonial->getTranslation('author_role', 'en'), $testimonial->location])->filter()->implode(', '))"
                            />
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.section>
    @endif

    {{-- BLOG --}}
    {{--
        Real posts, not scaffold copy: the homepage strip renders whatever the
        admin has published (CLAUDE.md §8). With nothing published the section
        is dropped entirely — an empty "From the Radix blog" heading is worse
        than no heading at all.
    --}}
    @if ($posts->isNotEmpty())
        @php $lead = $posts->first(); @endphp

        <x-ui.section tone="white" id="blog">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-4">
                <x-ui.heading size="lg" class="text-radix-dark">From the Radix blog</x-ui.heading>
                <a href="{{ route('blog.index') }}" class="fs-14 fw-bold text-radix-red-deep">View all posts <span aria-hidden="true">&rarr;</span></a>
            </div>

            <div class="row align-items-start gy-9 gx-lg-9 mt-7">
                <div class="col-lg-7">
                    <article>
                        <div class="rx-blog-lead overflow-hidden rounded-card bg-surface-sunken">
                            @if ($lead->image)
                                <img
                                    src="{{ $lead->image->url() }}"
                                    alt="{{ $lead->image->altText() }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="w-100 h-100 object-fit-cover"
                                >
                            @endif
                        </div>

                        @if ($lead->category)
                            <x-ui.eyebrow class="mt-4-5">{{ $lead->category->getTranslation('name', 'en') }}</x-ui.eyebrow>
                        @endif

                        <h3 class="mb-0 mt-2-5 font-display fs-20 fs-sm-24 fw-extrabold lh-tight tracking-display text-ink">
                            <a href="{{ route('blog.show', $lead) }}" class="rx-title-link">{{ $lead->getTranslation('title', 'en') }}</a>
                        </h3>

                        @if ($excerpt = $lead->getTranslation('excerpt', 'en'))
                            <p class="mb-0 mt-2-5 fs-14-5 lh-relaxed text-muted">{{ $excerpt }}</p>
                        @endif

                        <p class="mb-0 mt-2 fs-12-5 text-meta">
                            {{ $lead->authorName() }} ·
                            <time datetime="{{ $lead->published_at->toDateString() }}">{{ $lead->published_at->format('M Y') }}</time>
                        </p>
                    </article>
                </div>

                <div class="col-lg-5">
                    @foreach ($posts->skip(1) as $post)
                        <article class="d-flex gap-4 border-top border-hairline py-5">
                            <div class="rx-blog-thumb flex-shrink-0 overflow-hidden rounded-lg bg-surface-sunken">
                                @if ($post->image)
                                    <img
                                        src="{{ $post->image->url() }}"
                                        alt="{{ $post->image->altText() }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="w-100 h-100 object-fit-cover"
                                    >
                                @endif
                            </div>
                            <div class="min-w-0">
                                @if ($post->category)
                                    <x-ui.eyebrow size="xs">{{ $post->category->getTranslation('name', 'en') }}</x-ui.eyebrow>
                                @endif
                                <h3 class="mb-0 mt-1-5 font-display fs-15 fw-bold lh-snug text-ink">
                                    <a href="{{ route('blog.show', $post) }}" class="rx-title-link">{{ $post->getTranslation('title', 'en') }}</a>
                                </h3>
                                <p class="mb-0 mt-1-5 fs-11-5 text-meta">
                                    {{ $post->authorName() }} ·
                                    <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M Y') }}</time>
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </x-ui.section>
    @endif

    {{-- CTA BAND --}}
    <x-ui.section tone="accent" padding="band" class="text-center">
        <x-ui.heading size="md" class="text-white">Ready to power up with Radix?</x-ui.heading>

        <p class="mx-auto mb-0 mt-2-5 mw-xl fs-15 text-white">
            Distributor enquiry, export quote or a battery for home — we reply within one
            business day.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-6">
            <x-ui.button variant="inverse" size="lg" href="{{ route('contact').'#enquiry' }}">Enquire Now</x-ui.button>
            @if ($whatsapp = \App\Support\Content\SiteContent::whatsapp())
                <x-ui.button
                    variant="on-dark"
                    size="lg"
                    href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}?text={{ rawurlencode('Hello Radix, I would like to enquire about your batteries.') }}"
                    target="_blank"
                    rel="noopener"
                >Chat on WhatsApp</x-ui.button>
            @endif
        </div>
    </x-ui.section>
</x-layouts.public>
