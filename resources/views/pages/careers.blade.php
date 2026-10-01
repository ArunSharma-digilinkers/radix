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
    (Livewire\Admin\Careers\Applications) — unlike Contact's enquiry form,
    which stays markup-only pending the rest of the Phase 5 lead pipeline.
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

        <x-ui.heading as="h1" size="hero" class="mt-4 max-w-2xl text-radix-dark">
            Build the power behind <span class="text-radix-red">every home</span>.
        </x-ui.heading>

        <p class="mt-5 max-w-xl text-base leading-relaxed text-lead sm:text-[1.03125rem]">
            25+ years of manufacturing, a nationwide dealer network and an export business —
            run by people who fit it and forget it, every day.
        </p>

        <div class="mt-7 flex flex-wrap gap-3.5">
            <x-ui.button variant="primary" size="lg" href="#positions">See open positions</x-ui.button>
            <x-ui.button variant="secondary" size="lg" href="#apply">Send your CV</x-ui.button>
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

    {{-- BENEFITS — hidden until CareerPageContent::benefits() has data --}}
    @if (count($benefits))
        <x-ui.section tone="white" padding="flush-top">
            <x-ui.eyebrow>Why work here</x-ui.eyebrow>
            <x-ui.heading size="lg" class="mt-3 text-radix-dark">What you get.</x-ui.heading>

            <ul class="mt-6 grid gap-x-10 gap-y-3 sm:grid-cols-2">
                @foreach ($benefits as $benefit)
                    <li class="flex items-start gap-2.5 text-[0.9375rem] leading-relaxed text-ink-soft">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="mt-0.5 size-4 shrink-0 text-radix-red-deep">
                            <circle cx="12" cy="12" r="8.5" />
                            <path d="m8.2 12.3 2.6 2.6 5-5.4" />
                        </svg>
                        {{ $benefit }}
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

            <div class="mt-7 grid gap-4 sm:grid-cols-3">
                @foreach ($culturePhotos as $photo)
                    <div class="aspect-[4/3] overflow-hidden rounded-card bg-surface-sunken">
                        <img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}" loading="lazy" class="h-full w-full object-cover">
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
                <p class="max-w-lg text-[0.9375rem] leading-relaxed text-muted">
                    We don&rsquo;t have a live vacancy at the moment, but we&rsquo;re always glad
                    to hear from good people. Send us your CV below and we&rsquo;ll reach out
                    when something opens up.
                </p>
                <div class="mt-5">
                    <x-ui.button variant="primary" size="md" href="#apply">Send your CV</x-ui.button>
                </div>
            </div>
        @else
            <div class="mt-6 grid gap-4">
                @foreach ($openings as $opening)
                    <div class="flex flex-wrap items-start justify-between gap-4 rounded-card border border-hairline bg-white p-5">
                        <div>
                            <p class="font-display text-[1.0625rem] font-extrabold tracking-display text-radix-dark">
                                {{ $opening->getTranslation('title', 'en') }}
                            </p>

                            <p class="mt-1.5 flex flex-wrap items-center gap-x-2 text-[0.8125rem] text-meta">
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
                                <p class="mt-2.5 max-w-2xl text-[0.875rem] leading-relaxed text-ink-soft">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($description), 160) }}
                                </p>
                            @endif
                        </div>

                        <x-ui.button variant="secondary" size="md" href="{{ '#apply' }}" class="shrink-0">
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

            <div class="mt-7 grid gap-5 sm:grid-cols-2">
                @foreach ($testimonials as $testimonial)
                    <x-ui.pull-quote
                        variant="compact"
                        :quote="$testimonial['quote']"
                        :name="$testimonial['name']"
                        :role="$testimonial['role']"
                    />
                @endforeach
            </div>
        </x-ui.section>
    @endif

    {{-- APPLICATION FORM --}}
    <x-ui.section tone="white" id="apply">
        <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-14">
            <div>
                <x-ui.eyebrow>Apply</x-ui.eyebrow>
                <x-ui.heading size="lg" class="mt-3 text-radix-dark">Tell us about you.</x-ui.heading>

                <p class="mt-4 max-w-sm text-[0.9375rem] leading-relaxed text-muted">
                    Pick a role if you&rsquo;re applying for one, or leave it as a general
                    application and attach your CV — we keep it on file for the next opening
                    that fits.
                </p>
            </div>

            @if (session('applied'))
                <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-10">
                    <p class="font-display text-[1.0625rem] font-extrabold tracking-display text-radix-dark">
                        Application received.
                    </p>
                    <p class="mt-2 max-w-sm text-[0.9375rem] leading-relaxed text-muted">
                        Thanks — we&rsquo;ve got your details and resume on file. Our team will
                        reach out if there&rsquo;s a fit.
                    </p>
                </div>
            @else
                <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="grid gap-5 sm:grid-cols-2">
                    @csrf

                    <x-ui.text-field label="Full name" name="name" required :value="old('name')" class="sm:col-span-2" />
                    <x-ui.text-field label="Email" name="email" type="email" required :value="old('email')" />
                    <x-ui.text-field label="Phone" name="phone" type="tel" :value="old('phone')" />

                    @if ($openings->isNotEmpty())
                        <x-ui.select-field
                            label="Position"
                            name="job_opening_slug"
                            placeholder="General application"
                            :options="$positionOptions"
                            :selected="old('job_opening_slug')"
                            class="sm:col-span-2"
                        />
                    @endif

                    <div class="sm:col-span-2">
                        <label for="resume" class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">
                            Resume / CV<span aria-hidden="true" class="text-radix-red-deep">*</span>
                        </label>
                        <input
                            id="resume"
                            type="file"
                            name="resume"
                            accept=".pdf,.doc,.docx"
                            required
                            class="mt-2 w-full text-[0.84375rem] text-ink file:mr-3 file:rounded-btn file:border-0 file:bg-surface file:px-3.5 file:py-2 file:text-[0.8125rem] file:font-semibold file:text-radix-dark hover:file:bg-surface-sunken"
                        >
                        @error('resume') <p class="mt-1.5 text-[0.78125rem] text-radix-red-deep">{{ $message }}</p> @enderror
                    </div>

                    <x-ui.text-field
                        label="Cover note"
                        name="cover_note"
                        textarea
                        placeholder="A few lines about why you'd be a good fit…"
                        :value="old('cover_note')"
                        class="sm:col-span-2"
                    />

                    <x-ui.button type="submit" variant="primary" size="lg" class="sm:col-span-2 sm:w-fit">
                        Submit application
                    </x-ui.button>
                </form>
            @endif
        </div>
    </x-ui.section>
</x-layouts.public>
