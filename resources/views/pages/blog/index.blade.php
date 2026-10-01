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

        <x-ui.heading as="h1" size="hero" class="mt-4 max-w-2xl text-radix-dark">
            @if ($category)
                {{ $category->getTranslation('name', 'en') }}
            @else
                Notes from the <span class="text-radix-red">factory floor</span>.
            @endif
        </x-ui.heading>

        <p class="mt-5 max-w-xl text-base leading-relaxed text-lead sm:text-[1.03125rem]">
            @if ($category && ($blurb = trim((string) $category->getTranslation('description', 'en'))))
                {{ $blurb }}
            @else
                Maintenance tips, industry news and updates from a battery manufacturer of 25+ years.
            @endif
        </p>

        @if ($categories->isNotEmpty())
            <nav aria-label="Post categories" class="mt-8">
                <ul class="flex flex-wrap gap-2">
                    <li>
                        <a
                            href="{{ route('blog.index') }}"
                            @class([
                                'inline-flex rounded-full border px-3.5 py-2 text-[0.8125rem] font-medium transition-colors',
                                'border-radix-dark bg-radix-dark text-on-dark' => ! $category,
                                'border-line bg-white text-ink hover:border-line-control' => (bool) $category,
                            ])
                            @if (! $category) aria-current="page" @endif
                        >All posts</a>
                    </li>

                    @foreach ($categories as $item)
                        @php $isActive = $category && $category->is($item); @endphp

                        <li>
                            <a
                                href="{{ route('blog.category', $item) }}"
                                @class([
                                    'inline-flex rounded-full border px-3.5 py-2 text-[0.8125rem] font-medium transition-colors',
                                    'border-radix-dark bg-radix-dark text-on-dark' => $isActive,
                                    'border-line bg-white text-ink hover:border-line-control' => ! $isActive,
                                ])
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
                <p class="font-display text-[1.25rem] font-extrabold tracking-display text-radix-dark">
                    No posts yet.
                </p>
                <p class="mx-auto mt-2 max-w-sm text-[0.9375rem] text-muted">
                    @if ($category)
                        Nothing has been published in this category yet.
                        <a href="{{ route('blog.index') }}" class="font-semibold text-radix-red-deep">See all posts</a>.
                    @else
                        The first articles are on their way.
                    @endif
                </p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-site.post-card :post="$post" />
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
