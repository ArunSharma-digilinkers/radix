{{--
    Blog index — card grid, filterable by category (brief §4).

    Nothing on this page is placeholder copy: the grid renders whatever the
    admin has actually published, and an empty database renders an honest empty
    state rather than invented articles (CLAUDE.md §8).
--}}
<x-layouts.public
    :title="$category ? $category->getTranslation('name', 'en').' — Blog' : 'Blog'"
    :description="$category
        ? trim((string) $category->getTranslation('description', 'en')) ?: 'Articles from Radix Power Solutions on '.$category->getTranslation('name', 'en').'.'
        : 'Battery maintenance tips, industry news and company updates from Radix Power Solutions.'"
>
    <x-ui.section tone="surface" :reveal="false">
        <x-ui.eyebrow>Insights</x-ui.eyebrow>

        <x-ui.heading as="h1" size="hero" class="mt-4 mw-2xl text-radix-dark">
            @if ($category)
                {{ $category->getTranslation('name', 'en') }}
            @else
                Notes from the <span class="text-radix-red">factory floor</span>.
            @endif
        </x-ui.heading>

        <p class="mb-0 mt-5 mw-xl fs-16 fs-sm-16-5 lh-relaxed text-lead">
            @if ($category && ($blurb = trim((string) $category->getTranslation('description', 'en'))))
                {{ $blurb }}
            @else
                Maintenance tips, industry news and updates from a battery manufacturer of 25+ years.
            @endif
        </p>

        @if ($categories->isNotEmpty())
            <nav aria-label="Post categories" class="mt-8">
                <ul class="d-flex flex-wrap gap-2">
                    <li>
                        <a
                            href="{{ route('blog.index') }}"
                            @class(['rx-chip rx-chip--link', 'rx-chip--active' => ! $category])
                            @if (! $category) aria-current="page" @endif
                        >All posts</a>
                    </li>

                    @foreach ($categories as $item)
                        @php $isActive = $category && $category->is($item); @endphp

                        <li>
                            <a
                                href="{{ route('blog.category', $item) }}"
                                @class(['rx-chip rx-chip--link', 'rx-chip--active' => $isActive])
                                @if ($isActive) aria-current="page" @endif
                            >{{ $item->getTranslation('name', 'en') }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif
    </x-ui.section>

    <x-ui.section>
        @if ($posts->isEmpty())
            <div class="rounded-frame border border-hairline bg-surface-raised px-6 py-14 text-center">
                <p class="mb-0 font-display fs-20 fw-extrabold tracking-display text-radix-dark">
                    No posts yet.
                </p>
                <p class="mx-auto mb-0 mt-2 mw-sm fs-15 text-muted">
                    @if ($category)
                        Nothing has been published in this category yet.
                        <a href="{{ route('blog.index') }}" class="fw-semibold text-radix-red-deep">See all posts</a>.
                    @else
                        The first articles are on their way.
                    @endif
                </p>
            </div>
        @else
            <div class="row g-6">
                @foreach ($posts as $post)
                    <div class="col-sm-6 col-lg-4">
                        <x-site.post-card :post="$post" />
                    </div>
                @endforeach
            </div>

            @if ($posts->hasPages())
                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </x-ui.section>
</x-layouts.public>
