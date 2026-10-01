<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $certification ? 'Edit certification' : 'New certification' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 d-grid mw-xl gap-5">
        <x-ui.text-field label="Name" name="name" wire:model="name" required placeholder="e.g. ISO 9001:2015" />

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="Issuer" name="issuer" wire:model="issuer" />
            <x-ui.text-field label="Reference no." name="reference_no" wire:model="referenceNo" error="referenceNo" />
        </div>

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="Issued on" name="issued_on" type="date" wire:model="issuedOn" error="issuedOn" />
            <x-ui.text-field label="Expires on" name="expires_on" type="date" wire:model="expiresOn" error="expiresOn" />
        </div>

        <section class="border-top border-hairline pt-5">
            <h2 class="font-display fs-15 fw-bold text-radix-dark">Certificate image</h2>

            @if ($existingImageUrl && ! $newImage)
                <img src="{{ $existingImageUrl }}" alt="" class="mt-3 h-32 w-32 rounded-lg border border-hairline object-fit-cover">
            @endif

            <div class="mt-3">
                <label class="d-block font-mono fs-10 text-uppercase tracking-eyebrow text-meta">Upload / replace scan</label>
                <input type="file" wire:model="newImage" class="mt-2 fs-13-5">
                @error('newImage') <p class="mt-1 fs-12-5 text-radix-red-deep">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="Sort order" name="sort_order" type="number" wire:model="sortOrder" error="sortOrder" />

            <label class="d-flex align-items-center gap-2 pt-6 fs-13-5 text-ink">
                <input type="checkbox" wire:model="isActive" class="form-check-input rx-check">
                Active
            </label>
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save certification</x-ui.button>
            <a href="{{ route('admin.certifications.index') }}" class="fs-13 fw-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
