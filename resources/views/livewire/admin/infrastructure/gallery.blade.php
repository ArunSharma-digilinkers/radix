<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">Infrastructure</x-ui.heading>
    <p class="mt-1.5 text-[0.84375rem] text-meta">The factory/QC gallery and capacity figure shown on the public Infrastructure page.</p>

    <x-admin.form-errors />

    {{-- CAPACITY --}}
    <section class="mt-6 rounded-card border border-hairline bg-white p-5">
        <h2 class="font-display text-[0.9375rem] font-bold text-radix-dark">Capacity figure</h2>
        <p class="mt-1 text-[0.8125rem] text-meta">
            A single confirmed figure — e.g. "10,000 batteries/month". Leave blank to hide this
            section on the public page.
        </p>

        <form wire:submit="saveCapacity" class="mt-4 flex flex-wrap items-end gap-3">
            <div class="min-w-0 flex-1">
                <x-ui.text-field label="Capacity" name="capacity" wire:model="capacity" error="capacity" placeholder="e.g. 10,000 batteries / month" />
            </div>
            <x-ui.button type="submit" variant="secondary" size="md">Save</x-ui.button>
        </form>
    </section>

    {{-- GALLERY --}}
    <section class="mt-6 rounded-card border border-hairline bg-white p-5">
        <h2 class="font-display text-[0.9375rem] font-bold text-radix-dark">Photo &amp; video gallery</h2>

        <form wire:submit="upload" class="mt-4 grid gap-4 border-b border-hairline pb-6 sm:grid-cols-[1fr_1fr_auto]">
            <div>
                <label class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">Photo or video</label>
                <input type="file" wire:model="newFile" accept="image/*,video/mp4" class="mt-2 text-[0.84375rem]">
                @error('newFile') <p class="mt-1 text-[0.78125rem] text-radix-red-deep">{{ $message }}</p> @enderror
            </div>

            <x-ui.text-field label="Alt text (photos only)" name="new_file_alt" wire:model="newFileAlt" error="newFileAlt" />

            <x-ui.button type="submit" variant="secondary" size="md">
                <span wire:loading.remove wire:target="upload">Upload</span>
                <span wire:loading wire:target="upload">Uploading…</span>
            </x-ui.button>
        </form>

        <div class="mt-6 grid gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @forelse ($items as $item)
                <div wire:key="media-{{ $item->id }}" class="group relative overflow-hidden rounded-lg border border-hairline bg-surface-sunken">
                    @if (str_starts_with($item->mime_type ?? '', 'video/'))
                        <video src="{{ $item->url() }}" class="aspect-video w-full object-cover" muted></video>
                    @else
                        <img src="{{ $item->url() }}" alt="{{ $item->altText() }}" class="aspect-video w-full object-cover">
                    @endif

                    <button
                        type="button"
                        wire:click="delete({{ $item->id }})"
                        wire:confirm="Delete this file?"
                        class="absolute right-2 top-2 rounded-full bg-radix-dark/80 px-2.5 py-1 text-[0.71875rem] font-medium text-white opacity-0 transition-opacity group-hover:opacity-100"
                    >
                        Delete
                    </button>
                </div>
            @empty
                <p class="col-span-full py-6 text-center text-[0.84375rem] text-meta">Nothing uploaded yet.</p>
            @endforelse
        </div>
    </section>
</div>
