<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $certification ? 'Edit certification' : 'New certification' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 grid max-w-xl gap-5">
        <x-ui.text-field label="Name" name="name" wire:model="name" required placeholder="e.g. ISO 9001:2015" />

        <div class="grid grid-cols-2 gap-5">
            <x-ui.text-field label="Issuer" name="issuer" wire:model="issuer" />
            <x-ui.text-field label="Reference no." name="reference_no" wire:model="referenceNo" error="referenceNo" />
        </div>

        <div class="grid grid-cols-2 gap-5">
            <x-ui.text-field label="Issued on" name="issued_on" type="date" wire:model="issuedOn" error="issuedOn" />
            <x-ui.text-field label="Expires on" name="expires_on" type="date" wire:model="expiresOn" error="expiresOn" />
        </div>

        <section class="border-t border-hairline pt-5">
            <h2 class="font-display text-[0.9375rem] font-bold text-radix-dark">Certificate image</h2>

            @if ($existingImageUrl && ! $newImage)
                <img src="{{ $existingImageUrl }}" alt="" class="mt-3 h-32 w-32 rounded-lg border border-hairline object-cover">
            @endif

            <div class="mt-3">
                <label class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">Upload / replace scan</label>
                <input type="file" wire:model="newImage" class="mt-2 text-[0.84375rem]">
                @error('newImage') <p class="mt-1 text-[0.78125rem] text-radix-red-deep">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="grid grid-cols-2 gap-5">
            <x-ui.text-field label="Sort order" name="sort_order" type="number" wire:model="sortOrder" error="sortOrder" />

            <label class="flex items-center gap-2 pt-6 text-[0.84375rem] text-ink">
                <input type="checkbox" wire:model="isActive" class="rounded border-line-control text-radix-red focus:ring-radix-red">
                Active
            </label>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save certification</x-ui.button>
            <a href="{{ route('admin.certifications.index') }}" class="text-[0.8125rem] font-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
