<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Users</x-ui.heading>

        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            New user
        </a>
    </div>

    @error('self') <p class="mt-4 rx-notice rx-notice--error">{{ $message }}</p> @enderror

    <div class="mt-5">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email…" class="form-control rx-ctl w-100 mw-sm">
    </div>

    <div class="mt-5 table-responsive rounded-card border border-hairline bg-white">
        <table class="table rx-table rx-table--fluid mb-0 fs-13-5">
            <thead class="border-bottom border-hairline bg-surface fs-11-5 text-uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2-5">Name</th>
                    <th class="px-4 py-2-5">Email</th>
                    <th class="px-4 py-2-5">Role</th>
                    <th class="px-4 py-2-5">Status</th>
                    <th class="px-4 py-2-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-bottom border-hairline">
                        <td class="px-4 py-2-5 fw-medium text-ink">
                            {{ $user->name }}
                            @if ($user->id === auth()->id()) <span class="text-meta">(you)</span> @endif
                        </td>
                        <td class="px-4 py-2-5 text-meta">{{ $user->email }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $user->roles->first()?->name ?? '—' }}</td>
                        <td class="px-4 py-2-5">
                            <button type="button" wire:click="toggleActive({{ $user->id }})" class="{{ $user->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-2-5 text-end">
                            <a href="{{ route('admin.users.edit', $user) }}" class="fw-medium text-radix-red-deep">Edit</a>
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
