<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Applications</x-ui.heading>

        <a href="{{ route('admin.careers.index') }}" class="fs-13 fw-medium text-meta text-ink-hover">
            Back to openings
        </a>
    </div>

    <div class="mt-5 d-flex flex-wrap align-items-center justify-content-between gap-4">
        <div class="d-flex gap-1 rounded-btn border border-hairline bg-white p-1">
            @foreach (['open' => 'Open', 'all' => 'All'] as $value => $label)
                <button
                    type="button"
                    wire:click="$set('filter', '{{ $value }}')"
                    class="rounded-btn px-3 py-1-5 fs-13 fw-medium{{ $filter === $value ? 'bg-radix-red text-white' : 'text-meta hover:text-ink' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name or email…" class="form-control rx-ctl">
    </div>

    <div class="mt-5 d-flex flex-column gap-4">
        @forelse ($applications as $application)
            <div wire:key="application-{{ $application->id }}" class="rounded-card border border-hairline bg-white p-5">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                    <div>
                        <p class="font-display fs-16 fw-bold text-ink">{{ $application->name }}</p>
                        <p class="mt-0-5 fs-13 text-meta">
                            {{ $application->email }}
                            @if ($application->phone) &middot; {{ $application->phone }} @endif
                            &middot; {{ $application->opening?->getTranslation('title', 'en') ?? 'General application' }}
                        </p>
                        @if ($application->cover_note)
                            <p class="mt-2 mw-2xl fs-13-5 lh-relaxed text-ink-soft">{{ $application->cover_note }}</p>
                        @endif
                    </div>

                    <div class="text-end">
                        <select
                            wire:change="updateStatus({{ $application->id }}, $event.target.value)"
                            class="form-select rx-ctl"
                        >
                            @foreach (\App\Models\JobApplication::STATUSES as $statusOption)
                                <option value="{{ $statusOption }}" @selected($application->status === $statusOption)>
                                    {{ str_replace('_', ' ', $statusOption) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1-5 fs-12 text-meta">{{ $application->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-3 border-top border-hairline pt-4 grid-cols-sm-fr-auto">
                    <textarea
                        wire:model.blur="noteDrafts.{{ $application->id }}"
                        rows="2"
                        placeholder="Internal notes…"
                        class="form-control rx-ctl w-100"
                    ></textarea>

                    <div class="d-flex align-items-end gap-2">
                        @if ($application->resume_path)
                            <button type="button" wire:click="download({{ $application->id }})" class="rounded-btn border border-line-control px-3 py-2 fs-12-5 fw-medium text-ink bg-surface-hover">
                                Download resume
                            </button>
                        @endif
                        <button type="button" wire:click="saveNote({{ $application->id }})" class="btn btn-primary btn-sm">
                            Save note
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-card border border-hairline bg-white p-6 text-center text-meta">
                No applications here.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $applications->links() }}</div>
</div>
