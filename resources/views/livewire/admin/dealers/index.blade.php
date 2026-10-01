<div>
    <div class="flex items-center justify-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Dealers</x-ui.heading>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="toggleTrashed" class="text-[0.8125rem] font-medium text-meta hover:text-ink">
                {{ $showTrashed ? 'Show active' : 'Show deleted' }}
            </button>
            <a href="{{ route('admin.dealers.create') }}" class="rounded-btn bg-radix-red px-4 py-2 text-[0.8125rem] font-bold text-white hover:bg-radix-red-deep">
                New dealer
            </a>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-3">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, city, state or PIN…" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">

        <select wire:model.live="type" class="rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">
            <option value="">All types</option>
            @foreach (\App\Models\Dealer::TYPES as $typeOption)
                <option value="{{ $typeOption }}">{{ ucfirst(str_replace('_', ' ', $typeOption)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-5 overflow-hidden rounded-card border border-hairline bg-white">
        <table class="w-full text-left text-[0.84375rem]">
            <thead class="border-b border-hairline bg-surface text-[0.71875rem] uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2.5">Name</th>
                    <th class="px-4 py-2.5">Type</th>
                    <th class="px-4 py-2.5">City</th>
                    <th class="px-4 py-2.5">State</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dealers as $dealer)
                    <tr class="border-b border-hairline last:border-b-0">
                        <td class="px-4 py-2.5 font-medium text-ink">{{ $dealer->name }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ ucfirst(str_replace('_', ' ', $dealer->type)) }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ $dealer->city }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ $dealer->state }}</td>
                        <td class="px-4 py-2.5">
                            @if ($showTrashed)
                                <span class="text-meta">Deleted</span>
                            @else
                                <button type="button" wire:click="toggleActive({{ $dealer->id }})" class="{{ $dealer->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                    {{ $dealer->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            @if ($showTrashed)
                                <button type="button" wire:click="restore({{ $dealer->id }})" class="font-medium text-radix-red-deep">Restore</button>
                            @else
                                <a href="{{ route('admin.dealers.edit', $dealer) }}" class="mr-3 font-medium text-radix-red-deep">Edit</a>
                                <button type="button" wire:click="delete({{ $dealer->id }})" wire:confirm="Delete this dealer?" class="font-medium text-meta hover:text-radix-red-deep">Delete</button>
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
