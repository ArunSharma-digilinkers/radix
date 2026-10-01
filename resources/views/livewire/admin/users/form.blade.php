<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $user ? 'Edit user' : 'New user' }}</x-ui.heading>

    <x-admin.form-errors />


    <form wire:submit="save" class="mt-6 grid max-w-xl gap-5">
        <x-ui.text-field label="Name" name="name" wire:model="name" required />
        <x-ui.text-field label="Email" name="email" type="email" wire:model="email" required />

        <x-ui.select-field
            label="Role"
            name="role"
            wire:model="role"
            placeholder="Select a role"
            :options="$roles->mapWithKeys(fn ($r) => [$r => ucwords(str_replace('-', ' ', $r))])->all()"
        />

        <x-ui.text-field
            :label="$user ? 'New password (leave blank to keep current)' : 'Password'"
            name="password"
            type="password"
            wire:model="password"
            :required="! $user"
        />
        <x-ui.text-field label="Confirm password" name="password_confirmation" type="password" wire:model="password_confirmation" />

        <label class="flex items-center gap-2 text-[0.84375rem] text-ink">
            <input type="checkbox" wire:model="is_active" class="rounded border-line-control text-radix-red focus:ring-radix-red">
            Active
        </label>
        @error('is_active') <p class="text-[0.78125rem] text-radix-red-deep">{{ $message }}</p> @enderror

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save user</x-ui.button>
            <a href="{{ route('admin.users.index') }}" class="text-[0.8125rem] font-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
