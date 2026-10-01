<div>
    <x-admin.page-header title="Blog" description="Posts, drafts and scheduled articles.">
        <x-slot:actions>
            <button type="button" wire:click="toggleTrashed" class="text-[0.8125rem] font-medium text-meta hover:text-ink">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.blog.categories.index') }}" class="text-[0.8125rem] font-medium text-meta hover:text-ink">
                Categories
            </a>
            <x-ui.button href="{{ route('admin.blog.create') }}">New post</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by title or slug…" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">

        <select wire:model.live="status" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
            <option value="published">Published</option>
        </select>

        <select wire:model.live="categoryId" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->getTranslation('name', 'en') }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-5 overflow-hidden rounded-card border border-hairline bg-white">
        <table class="w-full text-left text-[0.84375rem]">
            <thead class="border-b border-hairline bg-surface text-[0.71875rem] uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2.5">Title</th>
                    <th class="px-4 py-2.5">Category</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr class="border-b border-hairline last:border-b-0">
                        <td class="px-4 py-2.5 font-medium text-ink">{{ $post->getTranslation('title', 'en') }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ $post->category?->getTranslation('name', 'en') ?? '—' }}</td>
                        <td class="px-4 py-2.5">
                            @if ($showTrashed)
                                <span class="text-meta">Deleted</span>
                            @elseif (! $post->published_at)
                                <span class="text-meta">Draft</span>
                            @elseif ($post->published_at->isFuture())
                                <span class="text-radix-red-deep">Scheduled — {{ $post->published_at->format('d M Y, H:i') }}</span>
                            @else
                                <span class="text-ink-soft">Published — {{ $post->published_at->format('d M Y') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $post->id }})" class="font-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.blog.edit', $post) }}" class="mr-3 font-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Delete this post?" class="font-medium text-meta hover:text-radix-red-deep">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-meta">No posts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
</div>
