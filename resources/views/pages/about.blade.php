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

        <x-ui.heading as="h1" size="hero" class="mt-4 max-w-2xl text-radix-dark">
            25+ years of power, <span class="text-radix-red">built in India</span>.
        </x-ui.heading>

        <p class="mt-5 max-w-xl text-base leading-relaxed text-lead sm:text-[1.03125rem]">
            A battery manufacturer with a nationwide dealer network and an export business —
            built on one idea: fit it and forget it.
        </p>

        <div class="mt-7 flex flex-wrap gap-3.5">
            <x-ui.button variant="primary" size="lg" href="{{ route('contact') }}">Get in touch</x-ui.button>
            <x-ui.button variant="secondary" size="lg" href="{{ route('products.index') }}">Explore products</x-ui.button>
        </div>
    </x-ui.section>

    {{-- TRUST STATS — single-sourced from HomePageContent (CLAUDE.md §7) --}}
    <x-ui.section tone="white" padding="tight" class="border-b border-hairline">
        <dl class="grid grid-cols-2 gap-6 sm:gap-0 lg:grid-cols-4">
            @foreach (App\Support\Content\HomePageContent::stats() as $stat)
                <x-ui.stat :value="$stat['value']" :label="$stat['label']" class="first:border-t-0 sm:first:border-l-0 sm:first:pl-0" />
            @endforeach
        </dl>
    </x-ui.section>

    {{-- OUR STORY --}}
    <x-ui.section tone="white" padding="flush-top">
        <x-ui.eyebrow>Our story</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 max-w-xl text-radix-dark">A quarter-century in the making.</x-ui.heading>

        <div class="mt-6 max-w-2xl space-y-4 text-[0.9375rem] leading-relaxed text-lead">
            @foreach (App\Support\Content\AboutPageContent::story() as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </x-ui.section>

    {{-- WHAT SETS US APART — reuses the homepage's "why Radix" pitch --}}
    <x-ui.section tone="surface">
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

    {{-- WHAT WE MAKE — real Product rows; hidden until the admin adds them --}}
    @if ($products->isNotEmpty())
        <x-ui.section tone="white" id="lines">
            <x-ui.eyebrow>What we make</x-ui.eyebrow>
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
        </x-ui.section>
    @endif

    {{-- CERTIFICATIONS --}}
    <x-ui.section tone="dark">
        <x-ui.eyebrow tone="dark">Quality</x-ui.eyebrow>
        <x-ui.heading size="md" class="mt-3">Certified, not just claimed.</x-ui.heading>

        <ul class="mt-6 flex flex-wrap gap-2.5">
            @foreach (App\Support\Content\AboutPageContent::certifications() as $certification)
                <li class="rounded-full border border-white/20 bg-white/5 px-3.5 py-2 text-[0.8125rem] font-medium text-on-dark">
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
                    <li class="flex gap-5 border-t border-hairline py-4">
                        <span class="w-16 shrink-0 font-display text-lg font-extrabold text-radix-red">{{ $milestone['year'] }}</span>
                        <span class="text-[0.9375rem] text-ink-soft">{{ $milestone['event'] }}</span>
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

            <div class="mt-7 grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($leadership as $person)
                    <div>
                        <p class="font-display text-base font-bold text-ink">{{ $person['name'] }}</p>
                        <p class="mt-1 text-[0.8125rem] text-meta">{{ $person['role'] }}</p>
                    </div>
                @endforeach
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
            <x-ui.button variant="inverse" size="lg" href="{{ route('contact').'#enquiry' }}">Enquire Now</x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.public>
