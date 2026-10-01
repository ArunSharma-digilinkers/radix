<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $dealer ? 'Edit dealer' : 'New dealer' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 grid max-w-2xl gap-5">
        <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.text-field label="Name" name="name" wire:model="name" required class="sm:col-span-2" />

            <x-ui.select-field
                label="Type"
                name="type"
                wire:model="type"
                :options="collect(\App\Models\Dealer::TYPES)->mapWithKeys(fn ($t) => [$t => ucfirst(str_replace('_', ' ', $t))])->all()"
            />

            <x-ui.text-field label="Contact person" name="contact_person" wire:model="contactPerson" error="contactPerson" />
        </div>

        <div class="grid gap-5 sm:grid-cols-3">
            <x-ui.text-field label="Phone" name="phone" wire:model="phone" />
            <x-ui.text-field label="WhatsApp" name="whatsapp" wire:model="whatsapp" />
            <x-ui.text-field label="Email" name="email" type="email" wire:model="email" />
        </div>

        <x-ui.text-field label="Address" name="address_line" wire:model="addressLine" error="addressLine" />

        <div class="grid gap-5 sm:grid-cols-4">
            <x-ui.text-field label="City" name="city" wire:model="city" required class="sm:col-span-2" />
            <x-ui.text-field label="State" name="state" wire:model="state" required class="sm:col-span-2" />
        </div>

        <div class="grid gap-5 sm:grid-cols-4">
            <x-ui.text-field label="PIN code" name="pincode" wire:model="pincode" />
            <x-ui.text-field label="Country code" name="country_code" wire:model="countryCode" error="countryCode" required />
        </div>

        {{-- Optional: only needed for nearest-first ordering (Phase 5). A
             dealer without these is still findable by city/state/PIN. --}}
        <div class="grid gap-5 sm:grid-cols-4">
            <x-ui.text-field label="Latitude" name="latitude" wire:model="latitude" placeholder="e.g. 26.4499" />
            <x-ui.text-field label="Longitude" name="longitude" wire:model="longitude" placeholder="e.g. 80.3319" />
        </div>

        <label class="flex items-center gap-2 text-[0.84375rem] text-ink">
            <input type="checkbox" wire:model="isActive" class="rounded border-line-control text-radix-red focus:ring-radix-red">
            Active
        </label>
        @error('isActive') <p class="text-[0.78125rem] text-radix-red-deep">{{ $message }}</p> @enderror

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save dealer</x-ui.button>
            <a href="{{ route('admin.dealers.index') }}" class="text-[0.8125rem] font-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
