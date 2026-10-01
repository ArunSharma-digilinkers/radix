<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Testimonials</x-ui.heading>

        <div class="d-flex align-items-center gap-3">
            <button type="button" wire:click="toggleTrashed" class="fs-13 fw-medium text-meta text-ink-hover">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm">
                New testimonial
            </a>
        </div>
    </div>

    <div class="mt-5 table-responsive rounded-card border border-hairline bg-white">
        <table class="table rx-table rx-table--fluid mb-0 fs-13-5">
            <thead class="border-bottom border-hairline bg-surface fs-11-5 text-uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2-5">Author</th>
                    <th class="px-4 py-2-5">Type</th>
                    <th class="px-4 py-2-5">Featured</th>
                    <th class="px-4 py-2-5">Status</th>
                    <th class="px-4 py-2-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($testimonials as $testimonial)
                    <tr class="border-bottom border-hairline">
                        <td class="px-4 py-2-5 fw-medium text-ink">{{ $testimonial->author_name }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ ucfirst($testimonial->type) }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $testimonial->is_featured ? 'Yes' : '—' }}</td>
                        <td class="px-4 py-2-5">
                            @if ($showTrashed)
                                <span class="text-meta">Deleted</span>
                            @else
                                <button type="button" wire:click="toggleActive({{ $testimonial->id }})" class="{{ $testimonial->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                    {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            @endif
                        </td>
                        <td class="px-4 py-2-5 text-end">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $testimonial->id }})" class="fw-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="me-3 fw-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $testimonial->id }})" wire:confirm="Delete this testimonial?" class="fw-medium text-meta text-radix-red-deep-hover">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-meta">No testimonials yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $testimonials->links() }}</div>
</div>
