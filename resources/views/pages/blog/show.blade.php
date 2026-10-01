{{--
    Article page.

    The body is the sanitised HTML written in the admin's CKEditor field, echoed
    unescaped and wrapped in `.rich-content` — the same rules the editor itself
    uses (see resources/scss/_content.scss), so what the author saw is what ships.
    It is safe to echo because App\Support\Html\RichText allowlisted it on save,
    not because anything here re-checks it.
--}}
<x-layouts.public :title="$post->seoTitle()" :description="$post->seoDescription()">
    <x-ui.section tone="surface" :reveal="false" class="rx-section--no-bottom">
        <nav aria-label="Breadcrumb" class="mb-6">
            <ol class="d-flex flex-wrap align-items-center gap-2 mb-0 font-mono fs-10 text-uppercase tracking-eyebrow text-meta">
                <li><a href="{{ route('blog.index') }}" class="rx-crumb-link">Blog</a></li>
                @if ($post->category)
                    <li aria-hidden="true">/</li>
                    <li>
                        <a href="{{ route('blog.category', $post->category) }}" class="rx-crumb-link">
                            {{ $post->category->getTranslation('name', 'en') }}
                        </a>
                    </li>
                @endif
            </ol>
        </nav>

        <x-ui.heading as="h1" size="xl" class="mw-3xl text-radix-dark">
            {{ $post->getTranslation('title', 'en') }}
        </x-ui.heading>

        @if ($excerpt = $post->getTranslation('excerpt', 'en'))
            <p class="mb-0 mt-5 mw-2xl fs-16 fs-sm-16-5 lh-relaxed text-lead">{{ $excerpt }}</p>
        @endif

        <p class="d-flex flex-wrap align-items-center column-gap-2-5 row-gap-1 mb-0 mt-7 pt-5 border-top border-line font-mono fs-10 text-uppercase tracking-eyebrow text-meta">
            <span class="text-ink">{{ $post->authorName() }}</span>
            <span aria-hidden="true">·</span>
            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d M Y') }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ $post->readingTime() }} min read</span>
        </p>
    </x-ui.section>

    <x-ui.section padding="flush-top">
        @if ($image = $post->image)
            <figure class="mb-0 overflow-hidden rounded-frame border border-hairline bg-surface-sunken">
                <img
                    src="{{ $image->url() }}"
                    alt="{{ $image->altText() }}"
                    class="w-100 object-fit-cover"
                >
            </figure>
        @endif

        {{-- 68ch is the editorial measure the concept uses for long-form copy. --}}
        <div class="rich-content rx-measure mt-10">
            {!! $post->getTranslation('body', 'en') !!}
        </div>

        <div class="mt-12 pt-6 border-top border-hairline">
            <a href="{{ route('blog.index') }}" class="fs-14 fw-bold text-radix-red-deep">
                <span aria-hidden="true">&larr;</span> All posts
            </a>
        </div>
    </x-ui.section>

    @if ($related->isNotEmpty())
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Keep reading</x-ui.eyebrow>

            <x-ui.heading as="h2" size="md" class="mt-3 text-radix-dark">Related posts</x-ui.heading>

            <div class="row g-6 mt-4">
                @foreach ($related as $item)
                    <div class="col-sm-6 col-lg-4">
                        <x-site.post-card :post="$item" />
                    </div>
                @endforeach
            </div>
        </x-ui.section>
    @endif
</x-layouts.public>
