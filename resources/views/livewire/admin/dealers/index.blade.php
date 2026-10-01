<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Dealers</x-ui.heading>

        <div class="d-flex align-items-center gap-3">
            <button type="button" wire:click="toggleTrashed" class="fs-13 fw-medium text-meta text-ink-hover">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.dealers.create') }}" class="btn btn-primary btn-sm">
                New dealer
            </a>
        </div>
    </div>

    <div class="mt-5 d-grid gap-4 grid-cols-sm-3">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, city, state or PIN…" class="form-control rx-ctl">

        <select wire:model.live="type" class="form-select rx-ctl">
            <option value="">All types</option>
            @foreach (\App\Models\Dealer::TYPES as $typeOption)
                <option value="{{ $typeOption }}">{{ ucfirst(str_replace('_', ' ', $typeOption)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-5 table-responsive rounded-card border border-hairline bg-white">
        <table class="table rx-table rx-table--fluid mb-0 fs-13-5">
            <thead class="border-bottom border-hairline bg-surface fs-11-5 text-uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2-5">Name</th>
                    <th class="px-4 py-2-5">Type</th>
                    <th class="px-4 py-2-5">City</th>
                    <th class="px-4 py-2-5">State</th>
                    <th class="px-4 py-2-5">Status</th>
                    <th class="px-4 py-2-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dealers as $dealer)
                    <tr class="border-bottom border-hairline">
                        <td class="px-4 py-2-5 fw-medium text-ink">{{ $dealer->name }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ ucfirst(str_replace('_', ' ', $dealer->type)) }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $dealer->city }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $dealer->state }}</td>
                        <td class="px-4 py-2-5">
                            @if ($showTrashed)
                                <span class="text-meta">Deleted</span>
                            @else
                                <button type="button" wire:click="toggleActive({{ $dealer->id }})" class="{{ $dealer->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                    {{ $dealer->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            @endif
                        </td>
                        <td class="px-4 py-2-5 text-end">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $dealer->id }})" class="fw-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.dealers.edit', $dealer) }}" class="me-3 fw-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $dealer->id }})" wire:confirm="Delete this dealer?" class="fw-medium text-meta text-radix-red-deep-hover">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-meta">No dealers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $dealers->links() }}</div>
</div>
