{{--
    Article page.

    The body is the sanitised HTML written in the admin's CKEditor field, echoed
    unescaped and wrapped in `.rich-content` — the same rules the editor itself
    uses (see resources/css/app.css), so what the author saw is what ships.
    It is safe to echo because App\Support\Html\RichText allowlisted it on save,
    not because anything here re-checks it.
--}}
<x-layouts.public :title="$post->seoTitle()" :description="$post->seoDescription()">
    <x-ui.section tone="surface" :reveal="false" class="pb-8!">
        <nav aria-label="Breadcrumb" class="mb-6">
            <ol class="flex flex-wrap items-center gap-2 font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">
                <li><a href="{{ route('blog.index') }}" class="hover:text-ink">Blog</a></li>
                @if ($post->category)
                    <li aria-hidden="true">/</li>
                    <li>
                        <a href="{{ route('blog.category', $post->category) }}" class="hover:text-ink">
                            {{ $post->category->getTranslation('name', 'en') }}
                        </a>
                    </li>
                @endif
            </ol>
        </nav>

        <x-ui.heading as="h1" size="xl" class="max-w-3xl text-radix-dark">
            {{ $post->getTranslation('title', 'en') }}
        </x-ui.heading>

        @if ($excerpt = $post->getTranslation('excerpt', 'en'))
            <p class="mt-5 max-w-2xl text-base leading-relaxed text-lead sm:text-[1.03125rem]">{{ $excerpt }}</p>
        @endif

        <p class="mt-7 flex flex-wrap items-center gap-x-2.5 gap-y-1 border-t border-line pt-5 font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">
            <span class="text-ink">{{ $post->authorName() }}</span>
            <span aria-hidden="true">·</span>
            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d M Y') }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ $post->readingTime() }} min read</span>
        </p>
    </x-ui.section>

    <x-ui.section padding="flush-top">
        @if ($image = $post->image)
            <figure class="overflow-hidden rounded-frame border border-hairline bg-surface-sunken">
                <img
                    src="{{ $image->url() }}"
                    alt="{{ $image->altText() }}"
                    class="w-full object-cover"
                >
            </figure>
        @endif

        {{-- 68ch is the editorial measure the concept uses for long-form copy. --}}
        <div class="rich-content mt-10 max-w-[68ch]">
            {!! $post->getTranslation('body', 'en') !!}
        </div>

        <div class="mt-12 border-t border-hairline pt-6">
            <a href="{{ route('blog.index') }}" class="text-sm font-bold text-radix-red-deep">
                <span aria-hidden="true">&larr;</span> All posts
            </a>
        </div>
    </x-ui.section>

    @if ($related->isNotEmpty())
        <x-ui.section tone="surface">
            <x-ui.eyebrow>Keep reading</x-ui.eyebrow>

            <x-ui.heading as="h2" size="md" class="mt-3 text-radix-dark">Related posts</x-ui.heading>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $item)
                    <x-site.post-card :post="$item" />
                @endforeach
            </div>
        </x-ui.section>
    @endif
</x-layouts.public>
