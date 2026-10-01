<div>
    <div class="flex items-center justify-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Enquiries</x-ui.heading>

        <button type="button" wire:click="export" class="rounded-btn border border-line-control px-4 py-2 text-[0.8125rem] font-medium text-ink hover:bg-surface">
            Export CSV
        </button>
    </div>

    <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-1 rounded-btn border border-hairline bg-white p-1">
            @foreach (['open' => 'Open', 'overdue' => 'Overdue', 'all' => 'All'] as $value => $label)
                <button
                    type="button"
                    wire:click="$set('filter', '{{ $value }}')"
                    class="rounded-btn px-3 py-1.5 text-[0.8125rem] font-medium {{ $filter === $value ? 'bg-radix-red text-white' : 'text-meta hover:text-ink' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, email, company…" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">
    </div>

    <div class="mt-5 flex flex-col gap-4">
        @forelse ($enquiries as $enquiry)
            <div wire:key="enquiry-{{ $enquiry->id }}" class="rounded-card border border-hairline bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-display text-base font-bold text-ink">{{ $enquiry->name }}</p>
                        <p class="mt-0.5 text-[0.8125rem] text-meta">
                            {{ ucfirst($enquiry->type) }} · {{ $enquiry->email ?? $enquiry->phone ?? 'No contact given' }}
                            @if ($enquiry->company) · {{ $enquiry->company }} @endif
                            @if ($enquiry->city) · {{ $enquiry->city }} @endif
                        </p>
                        <p class="mt-2 text-[0.84375rem] leading-relaxed text-ink-soft">{{ $enquiry->message }}</p>
                    </div>

                    <div class="text-right">
                        <span class="rounded-full bg-surface-sunken px-2.5 py-1 text-[0.71875rem] font-medium uppercase tracking-eyebrow text-ink-soft">
                            {{ str_replace('_', ' ', $enquiry->status) }}
                        </span>
                        <p class="mt-1.5 text-[0.75rem] text-meta">{{ $enquiry->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-3 border-t border-hairline pt-4 sm:grid-cols-[1fr_auto]">
                    <textarea
                        wire:model.blur="noteDrafts.{{ $enquiry->id }}"
                        rows="2"
                        placeholder="Internal notes…"
                        class="w-full rounded-lg border border-line-control bg-surface-sunken px-3 py-2 text-[0.8125rem] text-ink focus:border-radix-red focus:outline-none"
                    ></textarea>

                    <div class="flex items-end gap-2">
                        <button type="button" wire:click="saveNote({{ $enquiry->id }})" class="rounded-btn border border-line-control px-3 py-2 text-[0.78125rem] font-medium text-ink hover:bg-surface">
                            Save note
                        </button>

                        @if (! $enquiry->responded_at)
                            <button type="button" wire:click="markResponded({{ $enquiry->id }})" class="rounded-btn bg-radix-red px-3 py-2 text-[0.78125rem] font-bold text-white hover:bg-radix-red-deep">
                                Mark responded
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-card border border-hairline bg-white p-6 text-center text-meta">
                No enquiries here.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $enquiries->links() }}</div>
</div>
