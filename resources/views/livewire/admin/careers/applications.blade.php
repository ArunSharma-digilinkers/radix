<div>
    <div class="flex items-center justify-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Applications</x-ui.heading>

        <a href="{{ route('admin.careers.index') }}" class="text-[0.8125rem] font-medium text-meta hover:text-ink">
            Back to openings
        </a>
    </div>

    <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-1 rounded-btn border border-hairline bg-white p-1">
            @foreach (['open' => 'Open', 'all' => 'All'] as $value => $label)
                <button
                    type="button"
                    wire:click="$set('filter', '{{ $value }}')"
                    class="rounded-btn px-3 py-1.5 text-[0.8125rem] font-medium {{ $filter === $value ? 'bg-radix-red text-white' : 'text-meta hover:text-ink' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name or email…" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">
    </div>

    <div class="mt-5 flex flex-col gap-4">
        @forelse ($applications as $application)
            <div wire:key="application-{{ $application->id }}" class="rounded-card border border-hairline bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-display text-base font-bold text-ink">{{ $application->name }}</p>
                        <p class="mt-0.5 text-[0.8125rem] text-meta">
                            {{ $application->email }}
                            @if ($application->phone) &middot; {{ $application->phone }} @endif
                            &middot; {{ $application->opening?->getTranslation('title', 'en') ?? 'General application' }}
                        </p>
                        @if ($application->cover_note)
                            <p class="mt-2 max-w-2xl text-[0.84375rem] leading-relaxed text-ink-soft">{{ $application->cover_note }}</p>
                        @endif
                    </div>

                    <div class="text-right">
                        <select
                            wire:change="updateStatus({{ $application->id }}, $event.target.value)"
                            class="rounded-full border-0 bg-surface-sunken px-2.5 py-1 text-[0.71875rem] font-medium uppercase tracking-eyebrow text-ink-soft focus:outline-none focus:ring-1 focus:ring-radix-red"
                        >
                            @foreach (\App\Models\JobApplication::STATUSES as $statusOption)
                                <option value="{{ $statusOption }}" @selected($application->status === $statusOption)>
                                    {{ str_replace('_', ' ', $statusOption) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-[0.75rem] text-meta">{{ $application->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-3 border-t border-hairline pt-4 sm:grid-cols-[1fr_auto]">
                    <textarea
                        wire:model.blur="noteDrafts.{{ $application->id }}"
                        rows="2"
                        placeholder="Internal notes…"
                        class="w-full rounded-lg border border-line-control bg-surface-sunken px-3 py-2 text-[0.8125rem] text-ink focus:border-radix-red focus:outline-none"
                    ></textarea>

                    <div class="flex items-end gap-2">
                        @if ($application->resume_path)
                            <button type="button" wire:click="download({{ $application->id }})" class="rounded-btn border border-line-control px-3 py-2 text-[0.78125rem] font-medium text-ink hover:bg-surface">
                                Download resume
                            </button>
                        @endif
                        <button type="button" wire:click="saveNote({{ $application->id }})" class="rounded-btn bg-radix-red px-3 py-2 text-[0.78125rem] font-bold text-white hover:bg-radix-red-deep">
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
