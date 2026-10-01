<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $opening ? 'Edit opening' : 'New opening' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 grid max-w-2xl gap-8">
        <section class="grid gap-5 sm:grid-cols-2">
            <x-ui.text-field label="Title" name="title" wire:model="title" required class="sm:col-span-2" />
            <x-ui.text-field label="Slug" name="slug" wire:model="slug" placeholder="Auto-generated from title if left blank" class="sm:col-span-2" />

            <x-ui.text-field label="Department" name="department" wire:model="department" />
            <x-ui.text-field label="Location" name="location" wire:model="location" />

            <x-ui.select-field
                label="Employment type"
                name="employment_type"
                wire:model="employmentType"
                error="employmentType"
                :options="collect(\App\Models\JobOpening::TYPES)->mapWithKeys(fn ($t) => [$t => ucfirst(str_replace('_', ' ', $t))])->all()"
            />

            <x-ui.text-field label="Closes on" name="closes_on" type="date" wire:model="closesOn" error="closesOn" />
        </section>

        <section class="grid gap-5 sm:grid-cols-2">
            <x-ui.text-field label="Description" name="description" wire:model="description" textarea class="sm:col-span-2" />
            <x-ui.text-field label="Requirements" name="requirements" wire:model="requirements" textarea class="sm:col-span-2" />
        </section>

        <section class="grid gap-5 border-t border-hairline pt-6 sm:grid-cols-2">
            <h2 class="font-display text-[1.0625rem] font-extrabold tracking-display text-radix-dark sm:col-span-2">Publishing</h2>

            <div class="sm:col-span-2">
                {{-- Same server-clock convention as the blog post form: a
                     datetime-local input carries no timezone. --}}
                <x-ui.text-field
                    label="Publish at ({{ now()->format('T') }}) — blank = draft"
                    name="published_at"
                    type="datetime-local"
                    wire:model.live="publishedAt"
                    error="publishedAt"
                />

                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <button type="button" wire:click="publishNow" class="text-[0.78125rem] font-semibold text-radix-red-deep hover:underline">
                        Publish now
                    </button>

                    @if ($publishedAt !== '')
                        <button type="button" wire:click="makeDraft" class="text-[0.78125rem] font-medium text-meta hover:text-ink">
                            Back to draft
                        </button>
                    @endif

                    <span class="ml-auto font-mono text-[0.5625rem] uppercase tracking-eyebrow text-meta">
                        @if ($publishedAt === '')
                            Draft — not on the site
                        @elseif (\Illuminate\Support\Carbon::parse($publishedAt)->isFuture())
                            Scheduled
                        @else
                            Live on the careers page
                        @endif
                    </span>
                </div>
            </div>
        </section>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">
                <span wire:loading.remove wire:target="save">Save opening</span>
                <span wire:loading wire:target="save">Saving…</span>
            </x-ui.button>
            <a href="{{ route('admin.careers.index') }}" class="text-[0.8125rem] font-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
