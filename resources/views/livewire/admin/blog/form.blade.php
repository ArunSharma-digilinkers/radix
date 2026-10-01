<div>
    <x-admin.page-header
        :title="$post ? 'Edit post' : 'New post'"
        :description="$post ? 'Last saved '.$post->updated_at->diffForHumans().'.' : 'Drafts stay private until you set a publish date.'"
    >
        <x-slot:actions>
            <a href="{{ route('admin.blog.index') }}" class="fs-13 fw-medium text-meta text-ink-hover">Back to posts</a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.form-errors />


    <form wire:submit="save" class="d-grid gap-8">
        <section class="d-grid gap-5 grid-cols-sm-2">
            <x-ui.text-field label="Title" name="title" wire:model="title" required class="grid-col-span-sm-2" />
            <x-ui.text-field label="Slug" name="slug" wire:model="slug" placeholder="Auto-generated from title if left blank" class="grid-col-span-sm-2" />
            <x-ui.text-field label="Excerpt" name="excerpt" wire:model="excerpt" textarea placeholder="One or two sentences for the card grid and search results" class="grid-col-span-sm-2" />
        </section>

        <section>
            {{--
                The editor is inside wire:ignore (see the component), so its
                value arrives via $wire.set rather than wire:model. `:value` is
                the post's stored HTML — already sanitised on the way in.
            --}}
            <x-ui.rich-text
                label="Body"
                name="body"
                model="body"
                :value="$body"
                :upload-url="Gate::allows('media.manage') ? route('admin.editor.uploads') : null"
                hint="Headings, images, links, lists, quotes and tables. Anything else is stripped when the post is saved."
            />
        </section>

        <section class="d-grid gap-5 border-top border-hairline pt-6 grid-cols-sm-2">
            <h2 class="font-display fs-17 fw-extrabold tracking-display text-radix-dark grid-col-span-sm-2">Publishing</h2>

            <x-ui.select-field
                label="Category"
                name="category_id"
                wire:model="categoryId"
                placeholder="No category"
                :options="$categories->mapWithKeys(fn ($c) => [(string) $c->id => $c->getTranslation('name', 'en')])->all()"
            />

            <x-ui.text-field label="Author name (overrides account name)" name="author_name" wire:model="authorName" placeholder="e.g. Team Radix" />

            <div>
                {{--
                    The timezone is spelled out because a datetime-local input
                    does not carry one: the value means whatever the app's
                    timezone says, and a post typed in one zone and read in
                    another silently becomes "scheduled".
                --}}
                <x-ui.text-field
                    label="Publish at ({{ now()->format('T') }}) — blank = draft"
                    name="published_at"
                    type="datetime-local"
                    wire:model.live="publishedAt"
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

                    {{-- Live status, so the outcome of the date is never a surprise after saving. --}}
                    <span class="ms-auto font-mono fs-9 text-uppercase tracking-eyebrow text-meta">
                        @if ($publishedAt === '')
                            Draft — not on the site
                        @elseif (\Illuminate\Support\Carbon::parse($publishedAt)->isFuture())
                            Scheduled
                        @else
                            Live on the blog
                        @endif
                    </span>
                </div>
            </div>

            <x-ui.text-field label="Reading minutes" name="reading_minutes" type="number" wire:model="readingMinutes" placeholder="Left blank, the site estimates it" />

            <label class="d-flex align-items-center gap-2 fs-13-5 text-ink grid-col-span-sm-2">
                <input type="checkbox" wire:model="isFeatured" class="form-check-input rx-check">
                Feature this post
            </label>
        </section>

        <x-admin.seo-panel base-path="/blog/" />

        <section class="border-top border-hairline pt-6">
            <h2 class="font-display fs-17 fw-extrabold tracking-display text-radix-dark">Main image</h2>

            @if ($existingImageUrl && ! $newImage)
                <img src="{{ $existingImageUrl }}" alt="" class="mt-3 h-24 w-24 rounded-lg border border-hairline object-fit-cover">
            @endif

            <div class="mt-3 d-grid gap-4 grid-cols-sm-2">
                <div>
                    <label class="d-block font-mono fs-10 text-uppercase tracking-eyebrow text-meta">Upload / replace image</label>
                    <input type="file" wire:model="newImage" class="mt-2 fs-13-5">
                    @error('newImage') <p class="mt-1 fs-12-5 text-radix-red-deep">{{ $message }}</p> @enderror
                </div>
                <x-ui.text-field label="Alt text" name="image_alt" wire:model="imageAlt" placeholder="Describe the image for screen readers" />
            </div>
        </section>

        <div class="d-flex align-items-center gap-3 border-top border-hairline pt-6">
            <x-ui.button type="submit" variant="primary" size="lg">
                <span wire:loading.remove wire:target="save">Save post</span>
                <span wire:loading wire:target="save">Saving…</span>
            </x-ui.button>
            <a href="{{ route('admin.blog.index') }}" class="fs-13 fw-medium text-meta text-ink-hover">Cancel</a>
        </div>
    </form>
</div>
