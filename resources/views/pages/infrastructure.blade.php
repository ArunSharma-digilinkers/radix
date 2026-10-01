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

        <x-ui.heading as="h1" size="hero" class="mt-4 mw-2xl text-radix-dark">
            Where the <span class="text-radix-red">power</span> is made.
        </x-ui.heading>

        <p class="mb-0 mt-5 mw-xl fs-16 fs-sm-16-5 lh-relaxed text-lead">
            A look inside our production floor and QC lab — the process behind every battery
            that carries the Radix name.
        </p>
    </x-ui.section>

    {{-- PROCESS FLOW --}}
    <x-ui.section tone="white">
        <x-ui.eyebrow>How we build</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 text-radix-dark">Raw material to dispatch.</x-ui.heading>

        <ol class="row g-6 mt-5">
            @foreach ($process as $index => $step)
                <li class="col-sm-6 col-lg-3">
                    <div class="h-100 rounded-card border border-hairline bg-white p-5">
                        <span class="font-display fs-28 fw-black lh-none text-radix-red">
                            {{ sprintf('%02d', $index + 1) }}
                        </span>
                        <p class="mb-0 mt-3 font-display fs-16 fw-bold text-ink">{{ $step['title'] }}</p>
                        <p class="mb-0 mt-1-5 fs-13-5 lh-relaxed text-muted">{{ $step['description'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </x-ui.section>

    {{-- CAPACITY — hidden until a figure is confirmed --}}
    @if ($capacity)
        <x-ui.section tone="dark" padding="band" class="text-center">
            <x-ui.eyebrow tone="dark">Production capacity</x-ui.eyebrow>
            <p class="mb-0 mt-3 font-display fs-30 fs-sm-36 fw-black tracking-display text-on-dark">
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
                <p class="mb-0 mw-lg fs-15 lh-relaxed text-muted">
                    Current factory and QC photos are on their way. In the meantime,
                    <a href="{{ route('contact') }}" class="fw-semibold text-radix-red-deep">get in touch</a>
                    if you&rsquo;d like more detail on our facilities.
                </p>
            </div>
        @else
            <div class="row g-4 mt-2">
                @foreach ($gallery as $item)
                    <div class="col-sm-6 col-lg-4">
                        <div class="ratio ratio-16x9 overflow-hidden rounded-card bg-surface-sunken">
                            @if (str_starts_with($item->mime_type ?? '', 'video/'))
                                <video
                                    src="{{ $item->url() }}"
                                    class="object-fit-cover"
                                    autoplay muted loop playsinline preload="metadata" aria-hidden="true" tabindex="-1"
                                ></video>
                            @else
                                <img src="{{ $item->url() }}" alt="{{ $item->altText() }}" loading="lazy" class="object-fit-cover">
                            @endif
                        </div>
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
            <p class="mb-0 mt-4 mw-lg fs-15 lh-relaxed text-muted">
                Certificate scans are on their way. Radix products are ISO and BIS certified —
                see <a href="{{ route('about') }}" class="fw-semibold text-radix-red-deep">About</a> for more.
            </p>
        @else
            <div class="row g-5 mt-3">
                @foreach ($certifications as $certification)
                    <div class="col-sm-6 col-lg-4">
                        <div class="h-100 rounded-card border border-hairline bg-white p-5">
                            @if ($image = $certification->certificateImage)
                                <img
                                    src="{{ $image->url() }}"
                                    alt="{{ $image->altText() }}"
                                    loading="lazy"
                                    class="h-32 w-100 rounded-lg border border-hairline object-fit-contain bg-surface-sunken"
                                >
                            @endif

                            <p class="mb-0 mt-3 font-display fs-16 fw-bold text-ink">
                                {{ $certification->getTranslation('name', 'en') }}
                            </p>
                            @if ($certification->issuer)
                                <p class="mb-0 mt-1 fs-13 text-meta">{{ $certification->issuer }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.section>

    {{-- CTA BAND --}}
    <x-ui.section tone="accent" padding="band" class="text-center">
        <x-ui.heading size="md" class="text-white">Want to see more?</x-ui.heading>

        <p class="mx-auto mb-0 mt-2-5 mw-xl fs-15 text-white">
            Get in touch for a facility walkthrough or export-quality documentation.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-6">
            <x-ui.button variant="inverse" size="lg" href="{{ route('contact').'#enquiry' }}">Contact us</x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.public>
