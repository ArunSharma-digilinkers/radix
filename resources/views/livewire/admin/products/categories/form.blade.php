<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $category ? 'Edit category' : 'New category' }}</x-ui.heading>

    <x-admin.form-errors />


    <form wire:submit="save" class="mt-6 d-grid mw-xl gap-5">
        <x-ui.text-field label="Name" name="name" wire:model="name" required />
        <x-ui.text-field label="Slug" name="slug" wire:model="slug" placeholder="Auto-generated from name if left blank" />
        <x-ui.text-field label="Description" name="description" wire:model="description" textarea />

        <div class="d-grid grid-cols-2 gap-5">
            <x-ui.text-field label="Sort order" name="sort_order" type="number" wire:model="sort_order" />

            <label class="d-flex align-items-center gap-2 pt-6 fs-13-5 text-ink">
                <input type="checkbox" wire:model="is_active" class="form-check-input rx-check">
                Active
            </label>
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">Save category</x-ui.button>
            <a href="{{ route('admin.products.categories.index') }}" class="fs-13 fw-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
