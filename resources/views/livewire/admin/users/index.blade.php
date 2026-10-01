<div>
    <div class="flex items-center justify-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Users</x-ui.heading>

        <a href="{{ route('admin.users.create') }}" class="rounded-btn bg-radix-red px-4 py-2 text-[0.8125rem] font-bold text-white hover:bg-radix-red-deep">
            New user
        </a>
    </div>

    @error('self') <p class="mt-4 rounded-lg bg-radix-red/10 px-3.5 py-2.5 text-[0.8125rem] text-radix-red-deep">{{ $message }}</p> @enderror

    <div class="mt-5">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email…" class="w-full max-w-sm rounded-btn border border-line-control px-3 py-2 text-[0.84375rem] focus:border-radix-red focus:outline-none">
    </div>

    <div class="mt-5 overflow-hidden rounded-card border border-hairline bg-white">
        <table class="w-full text-left text-[0.84375rem]">
            <thead class="border-b border-hairline bg-surface text-[0.71875rem] uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2.5">Name</th>
                    <th class="px-4 py-2.5">Email</th>
                    <th class="px-4 py-2.5">Role</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-hairline last:border-b-0">
                        <td class="px-4 py-2.5 font-medium text-ink">
                            {{ $user->name }}
                            @if ($user->id === auth()->id()) <span class="text-meta">(you)</span> @endif
                        </td>
                        <td class="px-4 py-2.5 text-meta">{{ $user->email }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ $user->roles->first()?->name ?? '—' }}</td>
                        <td class="px-4 py-2.5">
                            <button type="button" wire:click="toggleActive({{ $user->id }})" class="{{ $user->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-radix-red-deep">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-meta">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
