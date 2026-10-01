<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Blog Categories</x-ui.heading>

        <div class="d-flex align-items-center gap-3">
            <button type="button" wire:click="toggleTrashed" class="fs-13 fw-medium text-meta text-ink-hover">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.blog.categories.create') }}" class="btn btn-primary btn-sm">
                New category
            </a>
        </div>
    </div>

    <div class="mt-5 table-responsive rounded-card border border-hairline bg-white">
        <table class="table rx-table rx-table--fluid mb-0 fs-13-5">
            <thead class="border-bottom border-hairline bg-surface fs-11-5 text-uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2-5">Name</th>
                    <th class="px-4 py-2-5">Slug</th>
                    <th class="px-4 py-2-5">Posts</th>
                    <th class="px-4 py-2-5">Status</th>
                    <th class="px-4 py-2-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-bottom border-hairline">
                        <td class="px-4 py-2-5 fw-medium text-ink">{{ $category->getTranslation('name', 'en') }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $category->slug }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $category->posts()->count() }}</td>
                        <td class="px-4 py-2-5">
                            @if ($showTrashed)
                                <span class="text-meta">Deleted</span>
                            @else
                                <button type="button" wire:click="toggleActive({{ $category->id }})" class="{{ $category->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            @endif
                        </td>
                        <td class="px-4 py-2-5 text-end">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $category->id }})" class="fw-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.blog.categories.edit', $category) }}" class="me-3 fw-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Delete this category?" class="fw-medium text-meta text-radix-red-deep-hover">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-meta">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</div>
