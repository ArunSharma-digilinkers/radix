<div>
    <div class="d-flex align-items-center justify-content-between">
        <x-ui.heading as="h1" size="md" class="text-radix-dark">Certifications</x-ui.heading>

        <a href="{{ route('admin.certifications.create') }}" class="btn btn-primary btn-sm">
            New certification
        </a>
    </div>

    <div class="mt-5 table-responsive rounded-card border border-hairline bg-white">
        <table class="table rx-table rx-table--fluid mb-0 fs-13-5">
            <thead class="border-bottom border-hairline bg-surface fs-11-5 text-uppercase tracking-eyebrow text-meta">
                <tr>
                    <th class="px-4 py-2-5">Name</th>
                    <th class="px-4 py-2-5">Issuer</th>
                    <th class="px-4 py-2-5">Expires</th>
                    <th class="px-4 py-2-5">Status</th>
                    <th class="px-4 py-2-5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($certifications as $certification)
                    <tr class="border-bottom border-hairline">
                        <td class="px-4 py-2-5 fw-medium text-ink">{{ $certification->getTranslation('name', 'en') }}</td>
                        <td class="px-4 py-2-5 text-meta">{{ $certification->issuer ?? '—' }}</td>
                        <td class="px-4 py-2-5 text-meta">
                            @if ($certification->hasExpired())
                                <span class="text-radix-red-deep">Expired {{ $certification->expires_on->format('d M Y') }}</span>
                            @elseif ($certification->expires_on)
                                {{ $certification->expires_on->format('d M Y') }}
                            @else
                                Never
                            @endif
                        </td>
                        <td class="px-4 py-2-5">
                            <button type="button" wire:click="toggleActive({{ $certification->id }})" class="{{ $certification->is_active ? 'text-ink-soft' : 'text-meta' }}">
                                {{ $certification->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-2-5 text-end">
                            <a href="{{ route('admin.certifications.edit', $certification) }}" class="me-3 fw-medium text-radix-red-deep">Edit</a>
                            <button type="button" wire:click="delete({{ $certification->id }})" wire:confirm="Delete this certification?" class="fw-medium text-meta text-radix-red-deep-hover">Delete</button>
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
