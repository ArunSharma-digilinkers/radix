<div>
    <div class="flex items-center justify-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Export Markets</x-ui.heading>

        <a href="{{ route('admin.export.create') }}" class="rounded-btn bg-radix-red px-4 py-2 text-[0.8125rem] font-bold text-white hover:bg-radix-red-deep">
            New market
        </a>
    </div>

    <div class="mt-5 overflow-hidden rounded-card border border-hairline bg-white">
        <table class="w-full text-left text-[0.84375rem]">
            <thead class="border-b border-hairline bg-surface text-[0.71875rem] uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2.5">Country</th>
                    <th class="px-4 py-2.5">ISO</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($markets as $market)
                    <tr class="border-b border-hairline last:border-b-0">
                        <td class="px-4 py-2.5 font-medium text-ink">{{ $market->getTranslation('country_name', 'en') }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ $market->iso_code }}{{ $market->iso_numeric ? ' · '.$market->iso_numeric : '' }}</td>
                        <td class="px-4 py-2.5">
                            <button type="button" wire:click="toggleActive({{ $market->id }})" class="{{ $market->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                {{ $market->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <a href="{{ route('admin.export.edit', $market) }}" class="mr-3 font-medium text-radix-red-deep">Edit</a>
                            <button type="button" wire:click="delete({{ $market->id }})" wire:confirm="Delete this market?" class="font-medium text-meta hover:text-radix-red-deep">Delete</button>
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
