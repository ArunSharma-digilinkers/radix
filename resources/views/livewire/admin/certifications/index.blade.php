<div>
    <div class="flex items-center justify-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Certifications</x-ui.heading>

        <a href="{{ route('admin.certifications.create') }}" class="rounded-btn bg-radix-red px-4 py-2 text-[0.8125rem] font-bold text-white hover:bg-radix-red-deep">
            New certification
        </a>
    </div>

    <div class="mt-5 overflow-hidden rounded-card border border-hairline bg-white">
        <table class="w-full text-left text-[0.84375rem]">
            <thead class="border-b border-hairline bg-surface text-[0.71875rem] uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2.5">Name</th>
                    <th class="px-4 py-2.5">Issuer</th>
                    <th class="px-4 py-2.5">Expires</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($certifications as $certification)
                    <tr class="border-b border-hairline last:border-b-0">
                        <td class="px-4 py-2.5 font-medium text-ink">{{ $certification->getTranslation('name', 'en') }}</td>
                        <td class="px-4 py-2.5 text-meta">{{ $certification->issuer ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-meta">
                            @if ($certification->hasExpired())
                                <span class="text-radix-red-deep">Expired {{ $certification->expires_on->format('d M Y') }}</span>
                            @elseif ($certification->expires_on)
                                {{ $certification->expires_on->format('d M Y') }}
                            @else
                                Never
                            @endif
                        </td>
                        <td class="px-4 py-2.5">
                            <button type="button" wire:click="toggleActive({{ $certification->id }})" class="{{ $certification->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                {{ $certification->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <a href="{{ route('admin.certifications.edit', $certification) }}" class="mr-3 font-medium text-radix-red-deep">Edit</a>
                            <button type="button" wire:click="delete({{ $certification->id }})" wire:confirm="Delete this certification?" class="font-medium text-meta hover:text-radix-red-deep">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-meta">No certifications yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $certifications->links() }}</div>
</div>
