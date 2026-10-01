<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Export Markets</x-ui.heading>

        <a href="{{ route('admin.export.create') }}" class="btn btn-primary btn-sm">
            New market
        </a>
    </div>

    <div class="mt-5 table-responsive rounded-card border border-hairline bg-white">
        <table class="table rx-table rx-table--fluid mb-0 fs-13-5">
            <thead class="border-bottom border-hairline bg-surface fs-11-5 text-uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2-5">Country</th>
                    <th class="px-4 py-2-5">ISO</th>
                    <th class="px-4 py-2-5">Status</th>
                    <th class="px-4 py-2-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($markets as $market)
                    <tr class="border-bottom border-hairline">
                        <td class="px-4 py-2-5 fw-medium text-ink">{{ $market->getTranslation('country_name', 'en') }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $market->iso_code }}{{ $market->iso_numeric ? ' · '.$market->iso_numeric : '' }}</td>
                        <td class="px-4 py-2-5">
                            <button type="button" wire:click="toggleActive({{ $market->id }})" class="{{ $market->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                {{ $market->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-2-5 text-end">
                            <a href="{{ route('admin.export.edit', $market) }}" class="me-3 fw-medium text-radix-red-deep">Edit</a>
                            <button type="button" wire:click="delete({{ $market->id }})" wire:confirm="Delete this market?" class="fw-medium text-meta text-radix-red-deep-hover">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-meta">No export markets yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $markets->links() }}</div>
</div>
