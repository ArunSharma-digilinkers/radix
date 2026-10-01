<div>
    <div class="flex items-center justify-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Testimonials</x-ui.heading>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="toggleTrashed" class="text-[0.8125rem] font-medium text-meta hover:text-ink">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.testimonials.create') }}" class="rounded-btn bg-radix-red px-4 py-2 text-[0.8125rem] font-bold text-white hover:bg-radix-red-deep">
                New testimonial
            </a>
        </div>
    </div>

    <div class="mt-5 overflow-hidden rounded-card border border-hairline bg-white">
        <table class="w-full text-left text-[0.84375rem]">
            <thead class="border-b border-hairline bg-surface text-[0.71875rem] uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2.5">Author</th>
                    <th class="px-4 py-2.5">Type</th>
                    <th class="px-4 py-2.5">Featured</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($testimonials as $testimonial)
                    <tr class="border-b border-hairline last:border-b-0">
                        <td class="px-4 py-2.5 font-medium text-ink">{{ $testimonial->author_name }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ ucfirst($testimonial->type) }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ $testimonial->is_featured ? 'Yes' : '—' }}</td>
                        <td class="px-4 py-2.5">
                            @if ($showTrashed)
                                <span class="text-meta">Deleted</span>
                            @else
                                <button type="button" wire:click="toggleActive({{ $testimonial->id }})" class="{{ $testimonial->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                    {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $testimonial->id }})" class="font-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="mr-3 font-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $testimonial->id }})" wire:confirm="Delete this testimonial?" class="font-medium text-meta hover:text-radix-red-deep">Delete</button>
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
