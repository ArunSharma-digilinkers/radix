<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">Infrastructure</x-ui.heading>
    <p class="mt-1-5 fs-13-5 text-meta">The factory/QC gallery and capacity figure shown on the public Infrastructure page.</p>

    <x-admin.form-errors />

    {{-- CAPACITY --}}
    <section class="mt-6 rounded-card border border-hairline bg-white p-5">
        <h2 class="font-display fs-15 fw-bold text-radix-dark">Capacity figure</h2>
        <p class="mt-1 fs-13 text-meta">
            A single confirmed figure — e.g. "10,000 batteries/month". Leave blank to hide this
            section on the public page.
        </p>

        <form wire:submit="saveCapacity" class="mt-4 d-flex flex-wrap align-items-end gap-3">
            <div class="min-w-0 flex-1">
                <x-ui.text-field label="Capacity" name="capacity" wire:model="capacity" error="capacity" placeholder="e.g. 10,000 batteries / month" />
            </div>
            <x-ui.button type="submit" variant="secondary" size="md">Save</x-ui.button>
        </form>
    </section>

    {{-- GALLERY --}}
    <section class="mt-6 rounded-card border border-hairline bg-white p-5">
        <h2 class="font-display fs-15 fw-bold text-radix-dark">Photo &amp; video gallery</h2>

        <form wire:submit="upload" class="mt-4 d-grid gap-4 border-bottom border-hairline pb-6 grid-cols-sm-fr-fr-auto">
            <div>
                <label class="d-block font-mono fs-10 text-uppercase tracking-eyebrow text-meta">Photo or video</label>
                <input type="file" wire:model="newFile" accept="image/*,video/mp4" class="mt-2 fs-13-5">
                @error('newFile') <p class="mt-1 fs-12-5 text-radix-red-deep">{{ $message }}</p> @enderror
            </div>

            <x-ui.text-field label="Alt text (photos only)" name="new_file_alt" wire:model="newFileAlt" error="newFileAlt" />

            <x-ui.button type="submit" variant="secondary" size="md">
                <span wire:loading.remove wire:target="upload">Upload</span>
                <span wire:loading wire:target="upload">Uploading…</span>
            </x-ui.button>
        </form>

        <div class="mt-6 d-grid gap-4 grid-cols-sm-3 grid-cols-lg-4">
            @forelse ($items as $item)
                <div wire:key="media-{{ $item->id }}" class="rx-hover-group position-relative overflow-hidden rounded-lg border border-hairline bg-surface-sunken">
                    @if (str_starts_with($item->mime_type ?? '', 'video/'))
                        <video src="{{ $item->url() }}" class="rx-aspect-video w-100 object-fit-cover" muted></video>
                    @else
                        <img src="{{ $item->url() }}" alt="{{ $item->altText() }}" class="rx-aspect-video w-100 object-fit-cover">
                    @endif

                    <button
                        type="button"
                        wire:click="delete({{ $item->id }})"
                        wire:confirm="Delete this file?"
                        class="rx-hover-reveal position-absolute end-0 top-0 m-2 rounded-pill border-0 px-2-5 py-1 fs-11-5 fw-medium text-white"
                    >
                        Delete
                    </button>
                </div>
            @empty
                <p class="grid-col-span-full py-6 text-center fs-13-5 text-meta">Nothing uploaded yet.</p>
            @endforelse
        </div>
    </section>
</div>
