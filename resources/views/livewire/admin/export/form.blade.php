<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $market ? 'Edit market' : 'New market' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 grid max-w-xl gap-5">
        <x-ui.text-field label="Country name" name="country_name" wire:model="countryName" error="countryName" required />
        <x-ui.text-field label="Slug" name="slug" wire:model="slug" placeholder="Auto-generated from country name if left blank" />

        <div class="grid grid-cols-2 gap-5">
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
                <p class="mt-1.5 text-[0.75rem] text-meta">
                    Optional, but it&rsquo;s what highlights this country on the export map — leave
                    blank and the map just won&rsquo;t light it up.
                </p>
            </div>
        </div>

        <x-ui.text-field label="Blurb" name="blurb" wire:model="blurb" textarea placeholder="A sentence or two about this market" />

        <div class="grid grid-cols-2 gap-5">
            <x-ui.text-field label="Sort order" name="sort_order" type="number" wire:model="sortOrder" error="sortOrder" />

            <label class="flex items-center gap-2 pt-6 text-[0.84375rem] text-ink">
                <input type="checkbox" wire:model="isActive" class="rounded border-line-control text-radix-red focus:ring-radix-red">
                Active
            </label>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save market</x-ui.button>
            <a href="{{ route('admin.export.index') }}" class="text-[0.8125rem] font-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
