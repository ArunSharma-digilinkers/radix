{{--
    Careers page (brief §4): open positions, or a "send your CV" fallback when
    there are none — see App\Http\Controllers\CareerController.

    Benefits, culture photos and employee testimonials are real sections in
    the markup but render nothing until App\Support\Content\CareerPageContent
    has data for them — same treatment as About's milestones/leadership and
    Contact's offices (CLAUDE.md §6/§8): no stock photography, and no invented
    names or claims about a company policy we haven't confirmed.

    The application form posts to App\Http\Controllers\CareerController::apply()
    and every submission lands in the admin inbox
    (Livewire\Admin\Careers\Applications).
--}}
@php
    $benefits = App\Support\Content\CareerPageContent::benefits();
    $culturePhotos = App\Support\Content\CareerPageContent::culturePhotos();
    $testimonials = App\Support\Content\CareerPageContent::employeeTestimonials();

    $positionOptions = $openings->mapWithKeys(
        fn ($opening) => [$opening->slug => $opening->getTranslation('title', 'en')]
    )->all();
@endphp
<x-layouts.public
    title="Careers"
    description="Join Radix Power Solutions. See our open positions across manufacturing, quality, sales and logistics, or send us your CV."
>
    {{-- HERO --}}
    <x-ui.section tone="surface" :reveal="false">
        <x-ui.eyebrow>Careers</x-ui.eyebrow>

        <x-ui.heading as="h1" size="hero" class="mt-4 mw-2xl text-radix-dark">
            Build the power behind <span class="text-radix-red">every home</span>.
        </x-ui.heading>

        <p class="mb-0 mt-5 mw-xl fs-16 fs-sm-16-5 lh-relaxed text-lead">
            25+ years of manufacturing, a nationwide dealer network and an export business —
            run by people who fit it and forget it, every day.
        </p>

        <div class="d-flex flex-wrap gap-3-5 mt-7">
            <x-ui.button variant="primary" size="lg" href="#positions">See open positions</x-ui.button>
            <x-ui.button variant="secondary" size="lg" href="#apply">Send your CV</x-ui.button>
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

    {{-- BENEFITS — hidden until CareerPageContent::benefits() has data --}}
    @if (count($benefits))
        <x-ui.section tone="white" padding="flush-top">
            <x-ui.eyebrow>Why work here</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">What you get.</x-ui.heading>

            <ul class="row gx-10 gy-3 mt-4">
                @foreach ($benefits as $benefit)
                    <li class="col-sm-6 d-flex align-items-start gap-2-5 fs-15 lh-relaxed text-ink-soft">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="mt-0-5 flex-shrink-0 text-radix-red-deep" width="16" height="16">
                            <circle cx="12" cy="12" r="8.5" />
                            <path d="m8.2 12.3 2.6 2.6 5-5.4" />
                        </svg>
                        <span>{{ $benefit }}</span>
                    </li>
                @endforeach
            </ul>
        </x-ui.section>
    @endif

    {{-- CULTURE PHOTOS — hidden until CareerPageContent::culturePhotos() has data --}}
    @if (count($culturePhotos))
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Life at Radix</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">On the floor and on the road.</x-ui.heading>

            <div class="row g-4 mt-3">
                @foreach ($culturePhotos as $photo)
                    <div class="col-sm-4">
                        <div class="ratio ratio-4x3 overflow-hidden rounded-card bg-surface-sunken">
                            <img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}" loading="lazy" class="object-fit-cover">
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- OPEN POSITIONS --}}
    <x-ui.section tone="white" id="positions">
        <x-ui.eyebrow>Open positions</x-ui.eyebrow>
        <x-ui.heading size="lg" class="mt-3 text-radix-dark">
            {{ $openings->isEmpty() ? "Nothing open right now." : 'Current openings.' }}
        </x-ui.heading>

        @if ($openings->isEmpty())
            <div class="mt-6 rounded-frame border border-hairline bg-surface-raised px-6 py-10">
                <p class="mb-0 mw-lg fs-15 lh-relaxed text-muted">
                    We don&rsquo;t have a live vacancy at the moment, but we&rsquo;re always glad
                    to hear from good people. Send us your CV below and we&rsquo;ll reach out
                    when something opens up.
                </p>
                <div class="mt-5">
                    <x-ui.button variant="primary" size="md" href="#apply">Send your CV</x-ui.button>
                </div>
            </div>
        @else
            <div class="vstack gap-4 mt-6">
                @foreach ($openings as $opening)
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-4 rounded-card border border-hairline bg-white p-5">
                        <div>
                            <p class="mb-0 font-display fs-17 fw-extrabold tracking-display text-radix-dark">
                                {{ $opening->getTranslation('title', 'en') }}
                            </p>

                            <p class="d-flex flex-wrap align-items-center column-gap-2 mb-0 mt-1-5 fs-13 text-meta">
                                @if ($opening->department)
                                    <span>{{ $opening->department }}</span>
                                    <span aria-hidden="true">&middot;</span>
                                @endif
                                @if ($opening->location)
                                    <span>{{ $opening->location }}</span>
                                    <span aria-hidden="true">&middot;</span>
                                @endif
                                <span>{{ ucfirst(str_replace('_', ' ', $opening->employment_type)) }}</span>
                                @if ($opening->closes_on)
                                    <span aria-hidden="true">&middot;</span>
                                    <span>Apply by {{ $opening->closes_on->format('d M Y') }}</span>
                                @endif
                            </p>

                            @if ($description = trim((string) $opening->getTranslation('description', 'en')))
                                <p class="mb-0 mt-2-5 mw-2xl fs-14 lh-relaxed text-ink-soft">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($description), 160) }}
                                </p>
                            @endif
                        </div>

                        <x-ui.button variant="secondary" size="md" href="{{ '#apply' }}" class="flex-shrink-0">
                            Apply
                        </x-ui.button>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.section>

    {{-- EMPLOYEE TESTIMONIALS — hidden until CareerPageContent::employeeTestimonials() has data --}}
    @if (count($testimonials))
        <x-ui.section tone="surface">
            <x-ui.eyebrow>From the team</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">In their own words.</x-ui.heading>

            <div class="row g-5 mt-3">
                @foreach ($testimonials as $testimonial)
                    <div class="col-sm-6">
                        <x-ui.pull-quote
                            variant="compact"
                            class="h-100"
                            :quote="$testimonial['quote']"
                            :name="$testimonial['name']"
                            :role="$testimonial['role']"
                        />
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- APPLICATION FORM --}}
    <x-ui.section tone="white" id="apply">
        <div class="row gy-10 gx-lg-14">
            <div class="col-lg-5">
                <x-ui.eyebrow>Apply</x-ui.eyebrow>
                <x-ui.heading size="lg" class="mt-3 text-radix-dark">Tell us about you.</x-ui.heading>

                <p class="mb-0 mt-4 mw-sm fs-15 lh-relaxed text-muted">
                    Pick a role if you&rsquo;re applying for one, or leave it as a general
                    application and attach your CV — we keep it on file for the next opening
                    that fits.
                </p>
            </div>

            <div class="col-lg-7">
                @if (session('applied'))
                    <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-10">
                        <p class="mb-0 font-display fs-17 fw-extrabold tracking-display text-radix-dark">
                            Application received.
                        </p>
                        <p class="mb-0 mt-2 mw-sm fs-15 lh-relaxed text-muted">
                            Thanks — we&rsquo;ve got your details and resume on file. Our team will
                            reach out if there&rsquo;s a fit.
                        </p>
                    </div>
                @else
                    <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="row g-5">
                        @csrf

                        <x-ui.text-field label="Full name" name="name" required :value="old('name')" class="col-12" />
                        <x-ui.text-field class="col-sm-6" label="Email" name="email" type="email" required :value="old('email')" />
                        <x-ui.text-field class="col-sm-6" label="Phone" name="phone" type="tel" :value="old('phone')" />

                        @if ($openings->isNotEmpty())
                            <x-ui.select-field
                                label="Position"
                                name="job_opening_slug"
                                placeholder="General application"
                                :options="$positionOptions"
                                :selected="old('job_opening_slug')"
                                class="col-12"
                            />
                        @endif

                        <div class="col-12">
                            <label for="resume" class="rx-label form-label">
                                Resume / CV<span aria-hidden="true" class="text-radix-red-deep">*</span>
                            </label>
                            <input
                                id="resume"
                                type="file"
                                name="resume"
                                accept=".pdf,.doc,.docx"
                                required
                                class="form-control rx-file"
                            >
                            @error('resume') <p class="mb-0 mt-1-5 fs-12-5 text-radix-red-deep">{{ $message }}</p> @enderror
                        </div>

                        <x-ui.text-field
                            label="Cover note"
                            name="cover_note"
                            textarea
                            placeholder="A few lines about why you'd be a good fit…"
                            :value="old('cover_note')"
                            class="col-12"
                        />

                        <div class="col-12">
                            <x-ui.button type="submit" variant="primary" size="lg">
                                Submit application
                            </x-ui.button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </x-ui.section>
</x-layouts.public>
