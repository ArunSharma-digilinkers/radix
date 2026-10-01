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
        <div class="grid items-center gap-9 lg:grid-cols-[1.05fr_0.95fr] lg:gap-11">
            <div>
                <x-ui.eyebrow>25 years of power &middot; made in India</x-ui.eyebrow>

                <x-ui.heading as="h1" size="hero" class="mt-4 text-radix-dark">
                    The battery brand India <span class="text-radix-red">runs on</span>.
                </x-ui.heading>

                <p class="mt-5 max-w-md text-base leading-relaxed text-lead sm:text-[1.03125rem]">
                    Inverter, automotive, solar and lithium — plus complete solar systems
                    and a 650-dealer network to back them.
                </p>

                <div class="mt-7 flex flex-wrap gap-3.5">
                    <x-ui.button variant="primary" size="lg" href="#finder">Find Your Battery</x-ui.button>
                    <x-ui.button variant="secondary" size="lg" href="#dealers">Become a dealer</x-ui.button>
                </div>
            </div>

            <x-ui.media-frame
                video="{{ asset('video/factory-hero.mp4') }}"
                badge="Live from the floor"
            />
        </div>
    </x-ui.section>

    {{-- TRUST STATS — the brief asks for these to be headline figures, not buried --}}
    <x-ui.section tone="white" padding="tight" class="border-b border-hairline">
        <dl class="grid grid-cols-2 gap-6 sm:gap-0 lg:grid-cols-4">
            @foreach (App\Support\Content\HomePageContent::stats() as $stat)
                <x-ui.stat :value="$stat['value']" :label="$stat['label']" class="first:border-t-0 sm:first:border-l-0 sm:first:pl-0" />
            @endforeach
        </dl>
    </x-ui.section>

    {{-- FIND YOUR BATTERY — quick-select from brief §4 --}}
    <x-ui.section tone="white" padding="flush-top" id="finder">
        <div class="rounded-frame border border-hairline bg-surface-raised p-6 sm:p-7">
            <h2 class="font-display text-xl font-extrabold tracking-display text-radix-dark">Find Your Battery</h2>

            <form class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-[repeat(3,1fr)_auto] lg:items-end">
                @foreach (App\Support\Content\HomePageContent::finder() as $name => $field)
                    <x-ui.select-field
                        :name="$name"
                        :label="$field['label']"
                        :placeholder="$field['placeholder']"
                        :options="$field['options']"
                    />
                @endforeach

                <x-ui.button type="submit" variant="primary" size="lg" class="sm:col-span-2 lg:col-span-1">
                    Show results
                </x-ui.button>
            </form>
        </div>
    </x-ui.section>

    {{-- PRODUCTS — editorial numbered index. Real Product rows (CLAUDE.md §8):
         empty until the admin adds them, same as the blog strip below. --}}
    @if ($products->isNotEmpty())
        <x-ui.section tone="white" padding="flush-top" id="products">
            <x-ui.eyebrow>Explore the range</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Eight lines of power.</x-ui.heading>

            <div class="mt-6 grid gap-x-10 sm:grid-cols-2">
                @foreach ($products as $product)
                    <x-ui.index-row
                        :number="sprintf('%02d', $loop->iteration)"
                        :name="$product->getTranslation('name', 'en')"
                        :pitch="trim((string) $product->getTranslation('pitch', 'en')) ?: null"
                        :image="$product->image?->url()"
                        :href="route('products.show', $product)"
                    />
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
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-13">
            <div>
                <x-ui.eyebrow tone="dark">The complete solution</x-ui.eyebrow>

                <x-ui.heading size="xl" class="mt-3.5">Solar, sold as one system.</x-ui.heading>

                <p class="mt-4 text-[0.9375rem] leading-relaxed text-on-dark-muted sm:text-base">
                    Panel, battery, inverter and charge controller — matched and warrantied
                    together, not four parts you have to reconcile yourself.
                </p>

                <x-ui.media-frame
                    class="mt-6"
                    image="{{ asset('images/placeholder/solar-array.jpg') }}"
                    alt="A Radix solar power generating system installed on a rooftop"
                    height="h-48 sm:h-60"
                    :scrim="false"
                />
            </div>

            <div>
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

        <div class="mt-7 grid gap-x-14 sm:grid-cols-2">
            @foreach (App\Support\Content\HomePageContent::whyRadix() as $reason)
                <x-ui.numbered-item
                    divider="rule"
                    :number="$reason['number']"
                    :title="$reason['title']"
                    :description="$reason['description']"
                />
            @endforeach
        </div>
    </x-ui.section>

    {{-- INFRASTRUCTURE --}}
    <x-ui.section tone="surface" id="infrastructure">
        <div class="grid items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-12">
            <div>
                <x-ui.eyebrow>Inside the factory</x-ui.eyebrow>

                <x-ui.heading size="md" class="mt-3 text-radix-dark">See where the power is made.</x-ui.heading>

                <p class="mt-4 text-[0.9375rem] leading-relaxed text-muted">
                    A walk through the production floor, QC lab and testing bays — the
                    credibility a spec sheet alone can't give.
                </p>

                <ul class="mt-5 flex flex-wrap gap-2.5">
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

            <x-ui.media-frame
                video="{{ asset('video/factory-floor.mp4') }}"
                badge="Live from the floor"
            />
        </div>
    </x-ui.section>

    {{-- DEALER LOCATOR --}}
    <x-ui.section tone="white" id="dealers">
        <div class="grid items-center gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:gap-12">
            <div class="overflow-hidden rounded-frame border border-hairline bg-surface p-4 text-radix-red">
                <x-map.india />
            </div>

            <div>
                <x-ui.eyebrow>650+ network</x-ui.eyebrow>

                <x-ui.heading size="md" class="mt-3 text-radix-dark">Find your nearest dealer.</x-ui.heading>

                <p class="mt-3.5 text-[0.90625rem] leading-relaxed text-muted">
                    Search by city or state and connect with a stocked Radix dealer near you.
                </p>

                {{-- Text search runs against real dealer records at /dealers.
                     Ordering by distance instead of city name needs geocoding,
                     which is Phase 5. --}}
                <form action="{{ route('dealers.index') }}" method="GET" class="mt-5">
                    <label for="dealer-search" class="sr-only">City, state or PIN code</label>
                    <div class="flex items-end gap-3 border-b-2 border-line-control focus-within:border-radix-red">
                        <input
                            id="dealer-search"
                            type="text"
                            name="location"
                            placeholder="Enter city or PIN code…"
                            class="min-w-0 flex-1 border-0 bg-transparent pb-2.5 text-[0.9375rem] text-ink placeholder:text-placeholder focus:outline-none focus:ring-0"
                        >
                        <button type="submit" class="pb-2.5 text-[0.9375rem] font-bold text-radix-red-deep">
                            Search <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.section>

    {{-- EXPORT --}}
    <x-ui.section tone="dark" id="export">
        <div class="grid items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-12">
            <div>
                <x-ui.eyebrow tone="dark">Export &middot; B2B</x-ui.eyebrow>

                <x-ui.heading size="md" class="mt-3">Trusted across borders.</x-ui.heading>

                <ul class="mt-5">
                    @foreach (App\Support\Content\HomePageContent::exportMarkets() as $market)
                        <li class="border-t border-white/15 py-2.5 font-display text-[1.0625rem] font-bold text-on-dark sm:text-lg">
                            {{ $market }}
                        </li>
                    @endforeach
                </ul>

                <x-ui.button variant="primary" size="lg" class="mt-6" href="{{ route('export.index').'#enquire' }}">
                    Request an export quote <span aria-hidden="true">&rarr;</span>
                </x-ui.button>
            </div>

            <div class="overflow-hidden rounded-frame border border-white/10 bg-radix-dark-2 p-4 text-radix-red-on-dark [--map-land:#22364f] [--map-line:#2e4664]">
                <x-map.world />
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
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    @foreach ($testimonials->slice(1) as $testimonial)
                        <x-ui.pull-quote
                            variant="compact"
                            :quote="$testimonial->getTranslation('quote', 'en')"
                            :name="$testimonial->author_name"
                            :role="trim(collect([$testimonial->getTranslation('author_role', 'en'), $testimonial->location])->filter()->implode(', '))"
                        />
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
            <div class="flex flex-wrap items-end justify-between gap-4">
                <x-ui.heading size="lg" class="text-radix-dark">From the Radix blog</x-ui.heading>
                <a href="{{ route('blog.index') }}" class="text-sm font-bold text-radix-red-deep">View all posts <span aria-hidden="true">&rarr;</span></a>
            </div>

            <div class="mt-7 grid items-start gap-9 lg:grid-cols-[1.3fr_1fr]">
                <article>
                    <div class="h-56 overflow-hidden rounded-card bg-surface-sunken sm:h-72 lg:h-[18.75rem]">
                        @if ($lead->image)
                            <img
                                src="{{ $lead->image->url() }}"
                                alt="{{ $lead->image->altText() }}"
                                loading="lazy"
                                decoding="async"
                                class="h-full w-full object-cover"
                            >
                        @endif
                    </div>

                    @if ($lead->category)
                        <x-ui.eyebrow class="mt-4.5">{{ $lead->category->getTranslation('name', 'en') }}</x-ui.eyebrow>
                    @endif

                    <h3 class="mt-2.5 font-display text-xl font-extrabold leading-tight tracking-display text-ink sm:text-2xl">
                        <a href="{{ route('blog.show', $lead) }}" class="hover:text-radix-red-deep">{{ $lead->getTranslation('title', 'en') }}</a>
                    </h3>

                    @if ($excerpt = $lead->getTranslation('excerpt', 'en'))
                        <p class="mt-2.5 text-[0.90625rem] leading-relaxed text-muted">{{ $excerpt }}</p>
                    @endif

                    <p class="mt-2 text-[0.78125rem] text-meta">
                        {{ $lead->authorName() }} ·
                        <time datetime="{{ $lead->published_at->toDateString() }}">{{ $lead->published_at->format('M Y') }}</time>
                    </p>
                </article>

                <div>
                    @foreach ($posts->skip(1) as $post)
                        <article class="flex gap-4 border-t border-hairline py-5">
                            <div class="h-[4.5rem] w-24 shrink-0 overflow-hidden rounded-lg bg-surface-sunken">
                                @if ($post->image)
                                    <img
                                        src="{{ $post->image->url() }}"
                                        alt="{{ $post->image->altText() }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-full w-full object-cover"
                                    >
                                @endif
                            </div>
                            <div class="min-w-0">
                                @if ($post->category)
                                    <x-ui.eyebrow size="xs">{{ $post->category->getTranslation('name', 'en') }}</x-ui.eyebrow>
                                @endif
                                <h3 class="mt-1.5 font-display text-[0.9375rem] font-bold leading-snug text-ink">
                                    <a href="{{ route('blog.show', $post) }}" class="hover:text-radix-red-deep">{{ $post->getTranslation('title', 'en') }}</a>
                                </h3>
                                <p class="mt-1.5 text-[0.71875rem] text-meta">
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

        <p class="mx-auto mt-2.5 max-w-xl text-[0.9375rem] text-white/90">
            Distributor enquiry, export quote or a battery for home — we reply within one
            business day.
        </p>

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <x-ui.button variant="inverse" size="lg">Enquire Now</x-ui.button>
            <x-ui.button variant="on-dark" size="lg">Chat on WhatsApp</x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.public>
