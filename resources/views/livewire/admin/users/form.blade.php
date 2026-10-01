<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $user ? 'Edit user' : 'New user' }}</x-ui.heading>

    <x-admin.form-errors />


    <form wire:submit="save" class="mt-6 d-grid mw-xl gap-5">
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

        <label class="d-flex align-items-center gap-2 fs-13-5 text-ink">
            <input type="checkbox" wire:model="is_active" class="form-check-input rx-check">
            Active
        </label>
        @error('is_active') <p class="fs-12-5 text-radix-red-deep">{{ $message }}</p> @enderror

        <div class="d-flex align-items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save user</x-ui.button>
            <a href="{{ route('admin.users.index') }}" class="fs-13 fw-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
