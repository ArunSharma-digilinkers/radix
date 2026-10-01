<div>
    <div class="flex items-center justify-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Careers</x-ui.heading>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="toggleTrashed" class="text-[0.8125rem] font-medium text-meta hover:text-ink">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.careers.applications.index') }}" class="text-[0.8125rem] font-medium text-meta hover:text-ink">
                Applications
            </a>
            <a href="{{ route('admin.careers.create') }}" class="rounded-btn bg-radix-red px-4 py-2 text-[0.8125rem] font-bold text-white hover:bg-radix-red-deep">
                New opening
            </a>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by title, slug or department…" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">

        <select wire:model.live="status" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
            <option value="published">Published</option>
        </select>
    </div>

    <div class="mt-5 overflow-hidden rounded-card border border-hairline bg-white">
        <table class="w-full text-left text-[0.84375rem]">
            <thead class="border-b border-hairline bg-surface text-[0.71875rem] uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2.5">Title</th>
                    <th class="px-4 py-2.5">Department</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($openings as $opening)
                    <tr class="border-b border-hairline last:border-b-0">
                        <td class="px-4 py-2.5 font-medium text-ink">{{ $opening->getTranslation('title', 'en') }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ $opening->department ?? '—' }}</td>
                        <td class="px-4 py-2.5">
                            @if ($showTrashed)
                                <span class="text-meta">Deleted</span>
                            @elseif (! $opening->published_at)
                                <span class="text-meta">Draft</span>
                            @elseif ($opening->published_at->isFuture())
                                <span class="text-radix-red-deep">Scheduled — {{ $opening->published_at->format('d M Y, H:i') }}</span>
                            @elseif ($opening->closes_on && $opening->closes_on->isPast())
                                <span class="text-meta">Closed — {{ $opening->closes_on->format('d M Y') }}</span>
                            @else
                                <span class="text-ink-soft">Published — {{ $opening->published_at->format('d M Y') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $opening->id }})" class="font-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.careers.edit', $opening) }}" class="mr-3 font-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $opening->id }})" wire:confirm="Delete this opening?" class="font-medium text-meta hover:text-radix-red-deep">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-meta">No openings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $openings->links() }}</div>
</div>
