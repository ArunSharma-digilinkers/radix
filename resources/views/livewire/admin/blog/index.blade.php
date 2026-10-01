<div>
    <x-admin.page-header title="Blog" description="Posts, drafts and scheduled articles.">
        <x-slot:actions>
            <button type="button" wire:click="toggleTrashed" class="fs-13 fw-medium text-meta text-ink-hover">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.blog.categories.index') }}" class="fs-13 fw-medium text-meta text-ink-hover">
                Categories
            </a>
            <x-ui.button href="{{ route('admin.blog.create') }}">New post</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="d-grid gap-4 grid-cols-sm-3">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by title or slug…" class="form-control rx-ctl">

        <select wire:model.live="status" class="form-select rx-ctl">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
            <option value="published">Published</option>
        </select>

        <select wire:model.live="categoryId" class="form-select rx-ctl">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->getTranslation('name', 'en') }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-5 table-responsive rounded-card border border-hairline bg-white">
        <table class="table rx-table rx-table--fluid mb-0 fs-13-5">
            <thead class="border-bottom border-hairline bg-surface fs-11-5 text-uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2-5">Title</th>
                    <th class="px-4 py-2-5">Category</th>
                    <th class="px-4 py-2-5">Status</th>
                    <th class="px-4 py-2-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr class="border-bottom border-hairline">
                        <td class="px-4 py-2-5 fw-medium text-ink">{{ $post->getTranslation('title', 'en') }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $post->category?->getTranslation('name', 'en') ?? '—' }}</td>
                        <td class="px-4 py-2-5">
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
                        <td class="px-4 py-2-5 text-end">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $post->id }})" class="fw-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.blog.edit', $post) }}" class="me-3 fw-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Delete this post?" class="fw-medium text-meta text-radix-red-deep-hover">Delete</button>
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
