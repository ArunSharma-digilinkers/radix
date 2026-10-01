<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $dealer ? 'Edit dealer' : 'New dealer' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 d-grid mw-2xl gap-5">
        <div class="d-grid gap-5 grid-cols-sm-2">
            <x-ui.text-field label="Name" name="name" wire:model="name" required class="grid-col-span-sm-2" />

            <x-ui.select-field
                label="Type"
                name="type"
                wire:model="type"
                :options="collect(\App\Models\Dealer::TYPES)->mapWithKeys(fn ($t) => [$t => ucfirst(str_replace('_', ' ', $t))])->all()"
            />

            <x-ui.text-field label="Contact person" name="contact_person" wire:model="contactPerson" error="contactPerson" />
        </div>

        <div class="d-grid gap-5 grid-cols-sm-3">
            <x-ui.text-field label="Phone" name="phone" wire:model="phone" />
            <x-ui.text-field label="WhatsApp" name="whatsapp" wire:model="whatsapp" />
            <x-ui.text-field label="Email" name="email" type="email" wire:model="email" />
        </div>

        <x-ui.text-field label="Address" name="address_line" wire:model="addressLine" error="addressLine" />

        <div class="d-grid gap-5 grid-cols-sm-4">
            <x-ui.text-field label="City" name="city" wire:model="city" required class="grid-col-span-sm-2" />
            <x-ui.text-field label="State" name="state" wire:model="state" required class="grid-col-span-sm-2" />
        </div>

        <div class="d-grid gap-5 grid-cols-sm-4">
            <x-ui.text-field label="PIN code" name="pincode" wire:model="pincode" />
            <x-ui.text-field label="Country code" name="country_code" wire:model="countryCode" error="countryCode" required />
        </div>

        {{-- Optional: only needed for nearest-first ordering (Phase 5). A
             dealer without these is still findable by city/state/PIN. --}}
        <div class="d-grid gap-5 grid-cols-sm-4">
            <x-ui.text-field label="Latitude" name="latitude" wire:model="latitude" placeholder="e.g. 26.4499" />
            <x-ui.text-field label="Longitude" name="longitude" wire:model="longitude" placeholder="e.g. 80.3319" />
        </div>

        <label class="d-flex align-items-center gap-2 fs-13-5 text-ink">
            <input type="checkbox" wire:model="isActive" class="form-check-input rx-check">
            Active
        </label>
        @error('isActive') <p class="fs-12-5 text-radix-red-deep">{{ $message }}</p> @enderror

        <div class="d-flex align-items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save dealer</x-ui.button>
            <a href="{{ route('admin.dealers.index') }}" class="fs-13 fw-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
