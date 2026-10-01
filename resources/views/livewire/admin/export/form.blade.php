<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $market ? 'Edit market' : 'New market' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 d-grid mw-xl gap-5">
        <x-ui.text-field label="Country name" name="country_name" wire:model="countryName" error="countryName" required />
        <x-ui.text-field label="Slug" name="slug" wire:model="slug" placeholder="Auto-generated from country name if left blank" />

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="ISO code (2 letters)" name="iso_code" wire:model="isoCode" error="isoCode" placeholder="e.g. NG" required />

            <div>
                <x-ui.text-field
                    label="ISO 3166-1 numeric"
                    name="iso_numeric"
                    type="number"
                    wire:model="isoNumeric"
                    error="isoNumeric"
                    placeholder="e.g. 566"
                />
                <p class="mt-1-5 fs-12 text-meta">
                    Optional, but it&rsquo;s what highlights this country on the export map — leave
                    blank and the map just won&rsquo;t light it up.
                </p>
            </div>
        </div>

        <x-ui.text-field label="Blurb" name="blurb" wire:model="blurb" textarea placeholder="A sentence or two about this market" />

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="Sort order" name="sort_order" type="number" wire:model="sortOrder" error="sortOrder" />

            <label class="d-flex align-items-center gap-2 pt-6 fs-13-5 text-ink">
                <input type="checkbox" wire:model="isActive" class="form-check-input rx-check">
                Active
            </label>
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save market</x-ui.button>
            <a href="{{ route('admin.export.index') }}" class="fs-13 fw-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
