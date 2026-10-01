<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $testimonial ? 'Edit testimonial' : 'New testimonial' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 grid max-w-xl gap-5">
        <x-ui.text-field label="Quote" name="quote" wire:model="quote" textarea required />

        <div class="grid grid-cols-2 gap-5">
            <x-ui.text-field label="Author name" name="author_name" wire:model="authorName" error="authorName" required />
            <x-ui.text-field label="Author role" name="author_role" wire:model="authorRole" error="authorRole" placeholder="e.g. Distributor" />
        </div>

        <div class="grid grid-cols-2 gap-5">
            <x-ui.text-field label="Location" name="location" wire:model="location" placeholder="e.g. Lucknow" />

            <x-ui.select-field
                label="Type"
                name="type"
                wire:model="type"
                :options="collect(\App\Models\Testimonial::TYPES)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all()"
            />
        </div>

        <section class="border-t border-hairline pt-5">
            <h2 class="font-display text-[0.9375rem] font-bold text-radix-dark">Photo</h2>

            @if ($existingImageUrl && ! $newImage)
                <img src="{{ $existingImageUrl }}" alt="" class="mt-3 h-20 w-20 rounded-full border border-hairline object-cover">
            @endif

            <div class="mt-3">
                <label class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">Upload / replace photo</label>
                <input type="file" wire:model="newImage" class="mt-2 text-[0.84375rem]">
                @error('newImage') <p class="mt-1 text-[0.78125rem] text-radix-red-deep">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="grid grid-cols-2 gap-5">
            <x-ui.text-field label="Sort order" name="sort_order" type="number" wire:model="sortOrder" error="sortOrder" />

            <div class="flex flex-col gap-2 pt-6">
                <label class="flex items-center gap-2 text-[0.84375rem] text-ink">
                    <input type="checkbox" wire:model="isFeatured" class="rounded border-line-control text-radix-red focus:ring-radix-red">
                    Featured (shown as the large quote)
                </label>
                <label class="flex items-center gap-2 text-[0.84375rem] text-ink">
                    <input type="checkbox" wire:model="isActive" class="rounded border-line-control text-radix-red focus:ring-radix-red">
                    Active
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save testimonial</x-ui.button>
            <a href="{{ route('admin.testimonials.index') }}" class="text-[0.8125rem] font-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
