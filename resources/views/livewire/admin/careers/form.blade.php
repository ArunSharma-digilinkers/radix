<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $opening ? 'Edit opening' : 'New opening' }}</x-ui.heading>

    <x-admin.form-errors />

    <form wire:submit="save" class="mt-6 d-grid mw-2xl gap-8">
        <section class="d-grid gap-5 grid-cols-sm-2">
            <x-ui.text-field label="Title" name="title" wire:model="title" required class="grid-col-span-sm-2" />
            <x-ui.text-field label="Slug" name="slug" wire:model="slug" placeholder="Auto-generated from title if left blank" class="grid-col-span-sm-2" />

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

        <section class="d-grid gap-5 grid-cols-sm-2">
            <x-ui.text-field label="Description" name="description" wire:model="description" textarea class="grid-col-span-sm-2" />
            <x-ui.text-field label="Requirements" name="requirements" wire:model="requirements" textarea class="grid-col-span-sm-2" />
        </section>

        <section class="d-grid gap-5 border-top border-hairline pt-6 grid-cols-sm-2">
            <h2 class="font-display fs-17 fw-extrabold tracking-display text-radix-dark grid-col-span-sm-2">Publishing</h2>

            <div class="grid-col-span-sm-2">
                {{-- Same server-clock convention as the blog post form: a
                     datetime-local input carries no timezone. --}}
                <x-ui.text-field
                    label="Publish at ({{ now()->format('T') }}) — blank = draft"
                    name="published_at"
                    type="datetime-local"
                    wire:model.live="publishedAt"
                    error="publishedAt"
                />

                <div class="mt-2 d-flex flex-wrap align-items-center gap-3">
                    <button type="button" wire:click="publishNow" class="fs-12-5 fw-semibold text-radix-red-deep rx-hover-underline">
                        Publish now
                    </button>

                    @if ($publishedAt !== '')
                        <button type="button" wire:click="makeDraft" class="fs-12-5 fw-medium text-meta text-ink-hover">
                            Back to draft
                        </button>
                    @endif

                    <span class="ms-auto font-mono fs-9 text-uppercase tracking-eyebrow text-meta">
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

        <div class="d-flex align-items-center gap-3">
            <x-ui.button type="submit" variant="primary" size="lg">
                <span wire:loading.remove wire:target="save">Save opening</span>
                <span wire:loading wire:target="save">Saving…</span>
            </x-ui.button>
            <a href="{{ route('admin.careers.index') }}" class="fs-13 fw-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
