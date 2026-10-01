{{--
    Infrastructure page (brief §4): process flow, capacity figure, factory/QC
    gallery, and certifications with certificate images.

    Capacity, gallery and certifications are real sections that render an
    honest empty state until App\Http\Controllers\InfrastructureController's
    data sources have something — CLAUDE.md §8 blocks inventing any of these
    (factory photos, capacity numbers, certifications) pending client input.
    The process flow is generic workflow copy, not a specific claim, so it
    can ship now — see App\Support\Content\InfrastructurePageContent.
--}}
@php
    $process = App\Support\Content\InfrastructurePageContent::process();
@endphp
<x-layouts.public
    title="Infrastructure"
    description="Inside Radix Power Solutions' manufacturing — from raw material to dispatch, our quality control process and certifications."
>
    {{-- HERO --}}
    <x-ui.section tone="surface" :reveal="false">
        <x-ui.eyebrow>Infrastructure</x-ui.eyebrow>

        <x-ui.heading as="h1" size="hero" class="mt-4 max-w-2xl text-radix-dark">
            Where the <span class="text-radix-red">power</span> is made.
        </x-ui.heading>

        <p class="mt-5 max-w-xl text-base leading-relaxed text-lead sm:text-[1.03125rem]">
            A look inside our production floor and QC lab — the process behind every battery
            that carries the Radix name.
        </p>
    </x-ui.section>

    {{-- PROCESS FLOW --}}
    <x-ui.section tone="white">
        <x-ui.eyebrow>How we build</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 text-radix-dark">Raw material to dispatch.</x-ui.heading>

        <ol class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($process as $index => $step)
                <li class="rounded-card border border-hairline bg-white p-5">
                    <span class="font-display text-[1.75rem] font-black leading-none text-radix-red">
                        {{ sprintf('%02d', $index + 1) }}
                    </span>
                    <p class="mt-3 font-display text-base font-bold text-ink">{{ $step['title'] }}</p>
                    <p class="mt-1.5 text-[0.84375rem] leading-relaxed text-muted">{{ $step['description'] }}</p>
                </li>
            @endforeach
        </ol>
    </x-ui.section>

    {{-- CAPACITY — hidden until a figure is confirmed --}}
    @if ($capacity)
        <x-ui.section tone="dark" padding="band" class="text-center">
            <x-ui.eyebrow tone="dark">Production capacity</x-ui.eyebrow>
            <p class="mt-3 font-display text-3xl font-black tracking-display text-on-dark sm:text-4xl">
                {{ $capacity }}
            </p>
        </x-ui.section>
    @endif

    {{-- GALLERY --}}
    <x-ui.section tone="surface">
        <x-ui.eyebrow>Inside the factory</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 text-radix-dark">Factory floor &amp; QC lab.</x-ui.heading>

        @if ($gallery->isEmpty())
            <div class="mt-6 rounded-frame border border-hairline bg-white px-6 py-10">
                <p class="max-w-lg text-[0.9375rem] leading-relaxed text-muted">
                    Current factory and QC photos are on their way. In the meantime,
                    <a href="{{ route('contact') }}" class="font-semibold text-radix-red-deep">get in touch</a>
                    if you&rsquo;d like more detail on our facilities.
                </p>
            </div>
        @else
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($gallery as $item)
                    <div class="overflow-hidden rounded-card bg-surface-sunken">
                        @if (str_starts_with($item->mime_type ?? '', 'video/'))
                            <video
                                src="{{ $item->url() }}"
                                class="aspect-video w-full object-cover"
                                autoplay muted loop playsinline preload="metadata" aria-hidden="true" tabindex="-1"
                            ></video>
                        @else
                            <img src="{{ $item->url() }}" alt="{{ $item->altText() }}" loading="lazy" class="aspect-video w-full object-cover">
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.section>

    {{-- CERTIFICATIONS --}}
    <x-ui.section tone="white">
        <x-ui.eyebrow>Quality</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 text-radix-dark">Certified, not just claimed.</x-ui.heading>

        @if ($certifications->isEmpty())
            <p class="mt-4 max-w-lg text-[0.9375rem] leading-relaxed text-muted">
                Certificate scans are on their way. Radix products are ISO and BIS certified —
                see <a href="{{ route('about') }}" class="font-semibold text-radix-red-deep">About</a> for more.
            </p>
        @else
            <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($certifications as $certification)
                    <div class="rounded-card border border-hairline bg-white p-5">
                        @if ($image = $certification->certificateImage)
                            <img
                                src="{{ $image->url() }}"
                                alt="{{ $image->altText() }}"
                                loading="lazy"
                                class="h-32 w-full rounded-lg border border-hairline object-contain bg-surface-sunken"
                            >
                        @endif

                        <p class="mt-3 font-display text-base font-bold text-ink">
                            {{ $certification->getTranslation('name', 'en') }}
                        </p>
                        @if ($certification->issuer)
                            <p class="mt-1 text-[0.8125rem] text-meta">{{ $certification->issuer }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.section>

    {{-- CTA BAND --}}
    <x-ui.section tone="accent" padding="band" class="text-center">
        <x-ui.heading size="md" class="text-white">Want to see more?</x-ui.heading>

        <p class="mx-auto mt-2.5 max-w-xl text-[0.9375rem] text-white/90">
            Get in touch for a facility walkthrough or export-quality documentation.
        </p>

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <x-ui.button variant="inverse" size="lg" href="{{ route('contact').'#enquiry' }}">Contact us</x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.public>
