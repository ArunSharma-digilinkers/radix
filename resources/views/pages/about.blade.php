{{--
    About page.

    Content comes from App\Support\Content\AboutPageContent, plus the shared
    stats/why-Radix/product data already sourced in HomePageContent — see the
    note on AboutPageContent for why those aren't duplicated here.

    Milestones and Leadership are real sections in the markup but render
    nothing until their content methods return data (CLAUDE.md §8): headcount,
    company history detail and leadership names/bios/photos are unverified
    and must not be invented. "What we make" is real Product data too, for
    the same reason — see ProductController.
--}}
@php
    $products = App\Models\Product::forDisplay()->with('image')->get();
@endphp
<x-layouts.public
    title="About Us"
    description="25+ years manufacturing inverter, automotive, solar, e-rickshaw and lithium batteries — a 650+ dealer network and export partners across 5+ countries."
>
    {{-- HERO --}}
    <x-ui.section tone="surface" :reveal="false">
        <x-ui.eyebrow>About Radix</x-ui.eyebrow>

        <x-ui.heading as="h1" size="hero" class="mt-4 mw-2xl text-radix-dark">
            25+ years of power, <span class="text-radix-red">built in India</span>.
        </x-ui.heading>

        <p class="mb-0 mt-5 mw-xl fs-16 fs-sm-16-5 lh-relaxed text-lead">
            A battery manufacturer with a nationwide dealer network and an export business —
            built on one idea: fit it and forget it.
        </p>

        <div class="d-flex flex-wrap gap-3-5 mt-7">
            <x-ui.button variant="primary" size="lg" href="{{ route('contact') }}">Get in touch</x-ui.button>
            <x-ui.button variant="secondary" size="lg" href="{{ route('products.index') }}">Explore products</x-ui.button>
        </div>
    </x-ui.section>

    {{-- TRUST STATS — single-sourced from HomePageContent (CLAUDE.md §7) --}}
    <x-ui.section tone="white" padding="tight" class="border-bottom border-hairline">
        <dl class="rx-stats mb-0">
            @foreach (App\Support\Content\HomePageContent::stats() as $stat)
                <x-ui.stat :value="$stat['value']" :label="$stat['label']" />
            @endforeach
        </dl>
    </x-ui.section>

    {{-- OUR STORY --}}
    <x-ui.section tone="white" padding="flush-top">
        <x-ui.eyebrow>Our story</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 mw-xl text-radix-dark">A quarter-century in the making.</x-ui.heading>

        <div class="vstack gap-4 mt-6 mw-2xl fs-15 lh-relaxed text-lead">
            @foreach (App\Support\Content\AboutPageContent::story() as $paragraph)
                <p class="mb-0">{{ $paragraph }}</p>
            @endforeach
        </div>
    </x-ui.section>

    {{-- WHAT SETS US APART — reuses the homepage's "why Radix" pitch --}}
    <x-ui.section tone="surface">
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

    {{-- WHAT WE MAKE — real Product rows; hidden until the admin adds them --}}
    @if ($products->isNotEmpty())
        <x-ui.section tone="white" id="lines">
            <x-ui.eyebrow>What we make</x-ui.eyebrow>
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
        </x-ui.section>
    @endif

    {{-- CERTIFICATIONS --}}
    <x-ui.section tone="dark">
        <x-ui.eyebrow tone="dark">Quality</x-ui.eyebrow>
        <x-ui.heading size="md" class="mt-3">Certified, not just claimed.</x-ui.heading>

        <ul class="d-flex flex-wrap gap-2-5 mt-6">
            @foreach (App\Support\Content\AboutPageContent::certifications() as $certification)
                <li class="rx-chip rx-chip--on-dark">
                    {{ $certification }}
                </li>
            @endforeach
        </ul>
    </x-ui.section>

    {{-- MILESTONES — hidden until AboutPageContent::milestones() has data --}}
    @php $milestones = App\Support\Content\AboutPageContent::milestones(); @endphp
    @if (count($milestones))
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Our journey</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Milestones.</x-ui.heading>

            <ol class="mt-6">
                @foreach ($milestones as $milestone)
                    <li class="d-flex gap-5 border-top border-hairline py-4">
                        <span class="w-16 flex-shrink-0 font-display fs-18 fw-extrabold text-radix-red">{{ $milestone['year'] }}</span>
                        <span class="fs-15 text-ink-soft">{{ $milestone['event'] }}</span>
                    </li>
                @endforeach
            </ol>
        </x-ui.section>
    @endif

    {{-- LEADERSHIP — hidden until AboutPageContent::leadership() has data --}}
    @php $leadership = App\Support\Content\AboutPageContent::leadership(); @endphp
    @if (count($leadership))
        <x-ui.section tone="white">
            <x-ui.eyebrow>Leadership</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">Who runs Radix.</x-ui.heading>

            <div class="row g-7 mt-4">
                @foreach ($leadership as $person)
                    <div class="col-sm-6 col-lg-4">
                        <p class="mb-0 font-display fs-16 fw-bold text-ink">{{ $person['name'] }}</p>
                        <p class="mb-0 mt-1 fs-13 text-meta">{{ $person['role'] }}</p>
                    </div>
                @endforeach
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
        </div>
    </x-ui.section>
</x-layouts.public>
