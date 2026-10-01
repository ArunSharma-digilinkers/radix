<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Enquiries</x-ui.heading>

        <button type="button" wire:click="export" class="rounded-btn border border-line-control px-4 py-2 fs-13 fw-medium text-ink bg-surface-hover">
            Export CSV
        </button>
    </div>

    <div class="mt-5 d-flex flex-wrap align-items-center justify-content-between gap-4">
        <div class="d-flex gap-1 rounded-btn border border-hairline bg-white p-1">
            @foreach (['open' => 'Open', 'overdue' => 'Overdue', 'all' => 'All'] as $value => $label)
                <button
                    type="button"
                    wire:click="$set('filter', '{{ $value }}')"
                    class="rounded-btn px-3 py-1-5 fs-13 fw-medium{{ $filter === $value ? 'bg-radix-red text-white' : 'text-meta hover:text-ink' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, email, company…" class="form-control rx-ctl">
    </div>

    <div class="mt-5 d-flex flex-column gap-4">
        @forelse ($enquiries as $enquiry)
            <div wire:key="enquiry-{{ $enquiry->id }}" class="rounded-card border border-hairline bg-white p-5">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                    <div>
                        <p class="font-display fs-16 fw-bold text-ink">{{ $enquiry->name }}</p>
                        <p class="mt-0-5 fs-13 text-meta">
                            {{ ucfirst($enquiry->type) }} · {{ $enquiry->email ?? $enquiry->phone ?? 'No contact given' }}
                            @if ($enquiry->company) · {{ $enquiry->company }} @endif
                            @if ($enquiry->city) · {{ $enquiry->city }} @endif
                        </p>
                        <p class="mt-2 fs-13-5 lh-relaxed text-ink-soft">{{ $enquiry->message }}</p>
                    </div>

                    <div class="text-end">
                        <span class="rounded-pill bg-surface-sunken px-2-5 py-1 fs-11-5 fw-medium text-uppercase tracking-eyebrow text-ink-soft">
                            {{ str_replace('_', ' ', $enquiry->status) }}
                        </span>
                        <p class="mt-1-5 fs-12 text-meta">{{ $enquiry->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-3 border-top border-hairline pt-4 grid-cols-sm-fr-auto">
                    <textarea
                        wire:model.blur="noteDrafts.{{ $enquiry->id }}"
                        rows="2"
                        placeholder="Internal notes…"
                        class="form-control rx-ctl w-100"
                    ></textarea>

                    <div class="d-flex align-items-end gap-2">
                        <button type="button" wire:click="saveNote({{ $enquiry->id }})" class="rounded-btn border border-line-control px-3 py-2 fs-12-5 fw-medium text-ink bg-surface-hover">
                            Save note
                        </button>

                        @if (! $enquiry->responded_at)
                            <button type="button" wire:click="markResponded({{ $enquiry->id }})" class="btn btn-primary btn-sm">
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
