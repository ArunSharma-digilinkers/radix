<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $testimonial ? 'Edit testimonial' : 'New testimonial' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 d-grid mw-xl gap-5">
        <x-ui.text-field label="Quote" name="quote" wire:model="quote" textarea required />

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="Author name" name="author_name" wire:model="authorName" error="authorName" required />
            <x-ui.text-field label="Author role" name="author_role" wire:model="authorRole" error="authorRole" placeholder="e.g. Distributor" />
        </div>

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="Location" name="location" wire:model="location" placeholder="e.g. Lucknow" />

            <x-ui.select-field
                label="Type"
                name="type"
                wire:model="type"
                :options="collect(\App\Models\Testimonial::TYPES)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all()"
            />
        </div>

        <section class="border-top border-hairline pt-5">
            <h2 class="font-display fs-15 fw-bold text-radix-dark">Photo</h2>

            @if ($existingImageUrl && ! $newImage)
                <img src="{{ $existingImageUrl }}" alt="" class="mt-3 h-20 w-20 rounded-pill border border-hairline object-fit-cover">
            @endif

            <div class="mt-3">
                <label class="d-block font-mono fs-10 text-uppercase tracking-eyebrow text-meta">Upload / replace photo</label>
                <input type="file" wire:model="newImage" class="mt-2 fs-13-5">
                @error('newImage') <p class="mt-1 fs-12-5 text-radix-red-deep">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="Sort order" name="sort_order" type="number" wire:model="sortOrder" error="sortOrder" />

            <div class="d-flex flex-column gap-2 pt-6">
                <label class="d-flex align-items-center gap-2 fs-13-5 text-ink">
                    <input type="checkbox" wire:model="isFeatured" class="form-check-input rx-check">
                    Featured (shown as the large quote)
                </label>
                <label class="d-flex align-items-center gap-2 fs-13-5 text-ink">
                    <input type="checkbox" wire:model="isActive" class="form-check-input rx-check">
                    Active
                </label>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save testimonial</x-ui.button>
            <a href="{{ route('admin.testimonials.index') }}" class="fs-13 fw-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
