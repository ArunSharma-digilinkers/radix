<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Careers</x-ui.heading>

        <div class="d-flex align-items-center gap-3">
            <button type="button" wire:click="toggleTrashed" class="fs-13 fw-medium text-meta text-ink-hover">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.careers.applications.index') }}" class="fs-13 fw-medium text-meta text-ink-hover">
                Applications
            </a>
            <a href="{{ route('admin.careers.create') }}" class="btn btn-primary btn-sm">
                New opening
            </a>
        </div>
    </div>

    <div class="mt-5 d-grid gap-4 grid-cols-sm-2">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by title, slug or department…" class="form-control rx-ctl">

        <select wire:model.live="status" class="form-select rx-ctl">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
            <option value="published">Published</option>
        </select>
    </div>

    <div class="mt-5 table-responsive rounded-card border border-hairline bg-white">
        <table class="table rx-table rx-table--fluid mb-0 fs-13-5">
            <thead class="border-bottom border-hairline bg-surface fs-11-5 text-uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2-5">Title</th>
                    <th class="px-4 py-2-5">Department</th>
                    <th class="px-4 py-2-5">Status</th>
                    <th class="px-4 py-2-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($openings as $opening)
                    <tr class="border-bottom border-hairline">
                        <td class="px-4 py-2-5 fw-medium text-ink">{{ $opening->getTranslation('title', 'en') }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $opening->department ?? '—' }}</td>
                        <td class="px-4 py-2-5">
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
                        <td class="px-4 py-2-5 text-end">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $opening->id }})" class="fw-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.careers.edit', $opening) }}" class="me-3 fw-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $opening->id }})" wire:confirm="Delete this opening?" class="fw-medium text-meta text-radix-red-deep-hover">Delete</button>
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
