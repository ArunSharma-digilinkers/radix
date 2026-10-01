<div>
    <x-ui.heading as="h1" size="md" class="text-radix-dark">{{ $product ? 'Edit product' : 'New product' }}</x-ui.heading>

    <x-admin.form-errors />


    <form wire:submit="save" class="mt-6 grid max-w-4xl gap-8">
        {{-- CORE --}}
        <section class="grid gap-5 sm:grid-cols-2">
            <x-ui.text-field label="Name" name="name" wire:model="name" required class="sm:col-span-2" />
            <x-ui.text-field label="Slug" name="slug" wire:model="slug" placeholder="Auto-generated from name if left blank" class="sm:col-span-2" />
            <x-ui.text-field label="Pitch (one-liner)" name="pitch" wire:model="pitch" class="sm:col-span-2" />
            <x-ui.text-field label="Description" name="description" wire:model="description" textarea class="sm:col-span-2" />
            <x-ui.text-field label="Use cases" name="use_cases" wire:model="useCases" textarea class="sm:col-span-2" />

            <x-ui.select-field
                label="Kind"
                name="kind"
                wire:model="kind"
                :options="collect(\App\Models\Product::KINDS)->mapWithKeys(fn ($k) => [$k => ucfirst(str_replace('_', ' ', $k))])->all()"
            />

            <x-ui.select-field
                label="Category"
                name="category_id"
                wire:model="categoryId"
                placeholder="No category"
                :options="$categories->mapWithKeys(fn ($c) => [(string) $c->id => $c->getTranslation('name', 'en')])->all()"
            />

            <x-ui.text-field label="Sort order" name="sort_order" type="number" wire:model="sortOrder" />

            <div class="flex items-end gap-5 pb-2">
                <label class="flex items-center gap-2 text-[0.84375rem] text-ink">
                    <input type="checkbox" wire:model="isActive" class="rounded border-line-control text-radix-red focus:ring-radix-red"> Active
                </label>
                <label class="flex items-center gap-2 text-[0.84375rem] text-ink">
                    <input type="checkbox" wire:model="isFeatured" class="rounded border-line-control text-radix-red focus:ring-radix-red"> Featured
                </label>
            </div>
        </section>

        {{-- MAIN IMAGE --}}
        <section class="border-t border-hairline pt-6">
            <h2 class="font-display text-sm font-bold text-radix-dark">Main image</h2>

            @if ($existingImageUrl && ! $newImage)
                <img src="{{ $existingImageUrl }}" alt="" class="mt-3 h-24 w-24 rounded-lg border border-hairline object-cover">
            @endif

            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">Upload / replace image</label>
                    <input type="file" wire:model="newImage" class="mt-2 text-[0.84375rem]">
                    @error('newImage') <p class="mt-1 text-[0.78125rem] text-radix-red-deep">{{ $message }}</p> @enderror
                </div>
                <x-ui.text-field label="Alt text" name="image_alt" wire:model="imageAlt" placeholder="Describe the image for screen readers" />
            </div>
        </section>

        {{-- GALLERY --}}
        <section class="border-t border-hairline pt-6">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-sm font-bold text-radix-dark">Gallery</h2>
                <button type="button" wire:click="addGalleryItem" class="text-[0.8125rem] font-medium text-radix-red-deep">+ Add image</button>
            </div>

            @foreach ($galleryItems as $index => $item)
                <div wire:key="gallery-{{ $index }}" class="mt-4 grid items-end gap-4 border-t border-hairline pt-4 sm:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <label class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">Image</label>
                        <input type="file" wire:model="galleryFiles.{{ $index }}" class="mt-2 text-[0.84375rem]">
                    </div>
                    <x-ui.text-field label="Alt text" name="gallery_alt_{{ $index }}" wire:model="galleryItems.{{ $index }}.alt" />
                    <button type="button" wire:click="removeGalleryItem({{ $index }})" class="pb-2.5 text-[0.8125rem] font-medium text-meta hover:text-radix-red-deep">Remove</button>
                </div>
            @endforeach
        </section>

        {{-- VARIANTS --}}
        <section class="border-t border-hairline pt-6">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-sm font-bold text-radix-dark">Variants</h2>
                <button type="button" wire:click="addVariant" class="text-[0.8125rem] font-medium text-radix-red-deep">+ Add variant</button>
            </div>
            @error('variants') <p class="mt-2 text-[0.78125rem] text-radix-red-deep">{{ $message }}</p> @enderror

            @foreach ($variants as $index => $variant)
                <div wire:key="variant-{{ $index }}" class="mt-4 grid gap-3 border-t border-hairline pt-4 sm:grid-cols-3">
                    <x-ui.text-field label="Model code" name="variant_code_{{ $index }}" wire:model="variants.{{ $index }}.model_code" required />
                    <x-ui.text-field label="Name" name="variant_name_{{ $index }}" wire:model="variants.{{ $index }}.name" />
                    <x-ui.text-field label="Capacity (Ah)" name="variant_capacity_{{ $index }}" type="number" wire:model="variants.{{ $index }}.capacity_ah" />
                    <x-ui.text-field label="Voltage" name="variant_voltage_{{ $index }}" type="number" wire:model="variants.{{ $index }}.voltage" />
                    <x-ui.text-field label="Warranty (months)" name="variant_warranty_{{ $index }}" type="number" wire:model="variants.{{ $index }}.warranty_months" />
                    <x-ui.text-field label="Weight (kg)" name="variant_weight_{{ $index }}" type="number" wire:model="variants.{{ $index }}.weight_kg" />
                    <x-ui.text-field label="Dimensions (mm)" name="variant_dims_{{ $index }}" wire:model="variants.{{ $index }}.dimensions_mm" class="sm:col-span-2" />
                    <div class="flex items-end justify-between">
                        <label class="flex items-center gap-2 text-[0.84375rem] text-ink">
                            <input type="checkbox" wire:model="variants.{{ $index }}.is_active" class="rounded border-line-control text-radix-red focus:ring-radix-red"> Active
                        </label>
                        <button type="button" wire:click="removeVariant({{ $index }})" class="text-[0.8125rem] font-medium text-meta hover:text-radix-red-deep">Remove</button>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- SPECS --}}
        <section class="border-t border-hairline pt-6">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-sm font-bold text-radix-dark">Specs</h2>
                <button type="button" wire:click="addSpec" class="text-[0.8125rem] font-medium text-radix-red-deep">+ Add spec</button>
            </div>

            @foreach ($specs as $index => $spec)
                <div wire:key="spec-{{ $index }}" class="mt-4 grid items-end gap-3 border-t border-hairline pt-4 sm:grid-cols-[1fr_1fr_auto]">
                    <x-ui.text-field label="Label" name="spec_label_{{ $index }}" wire:model="specs.{{ $index }}.label" required />
                    <x-ui.text-field label="Value" name="spec_value_{{ $index }}" wire:model="specs.{{ $index }}.value" required />
                    <button type="button" wire:click="removeSpec({{ $index }})" class="pb-2.5 text-[0.8125rem] font-medium text-meta hover:text-radix-red-deep">Remove</button>
                </div>
            @endforeach
        </section>

        {{-- FAQS --}}
        <section class="border-t border-hairline pt-6">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-sm font-bold text-radix-dark">FAQs</h2>
                <button type="button" wire:click="addFaq" class="text-[0.8125rem] font-medium text-radix-red-deep">+ Add FAQ</button>
            </div>

            @foreach ($faqs as $index => $faq)
                <div wire:key="faq-{{ $index }}" class="mt-4 grid gap-3 border-t border-hairline pt-4">
                    <x-ui.text-field label="Question" name="faq_question_{{ $index }}" wire:model="faqs.{{ $index }}.question" required />
                    <x-ui.text-field label="Answer" name="faq_answer_{{ $index }}" wire:model="faqs.{{ $index }}.answer" textarea required />
                    <div class="flex items-center justify-end">
                        <button type="button" wire:click="removeFaq({{ $index }})" class="text-[0.8125rem] font-medium text-meta hover:text-radix-red-deep">Remove</button>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- DOCUMENTS --}}
        <section class="border-t border-hairline pt-6">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-sm font-bold text-radix-dark">Documents</h2>
                <button type="button" wire:click="addDocument" class="text-[0.8125rem] font-medium text-radix-red-deep">+ Add document</button>
            </div>

            @foreach ($documents as $index => $document)
                <div wire:key="document-{{ $index }}" class="mt-4 grid items-end gap-3 border-t border-hairline pt-4 sm:grid-cols-[1fr_auto_1fr_auto]">
                    <x-ui.text-field label="Title" name="doc_title_{{ $index }}" wire:model="documents.{{ $index }}.title" />

                    <x-ui.select-field
                        label="Type"
                        name="doc_type_{{ $index }}"
                        wire:model="documents.{{ $index }}.type"
                        :options="collect(\App\Models\ProductDocument::TYPES)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all()"
                    />

                    <div>
                        <label class="block font-mono text-[0.625rem] uppercase tracking-eyebrow text-meta">File (PDF)</label>
                        <input type="file" wire:model="documentFiles.{{ $index }}" class="mt-2 text-[0.84375rem]">
                        @if ($document['existing_path'] && empty($documentFiles[$index]))
                            <p class="mt-1 text-[0.75rem] text-meta">Existing file on record.</p>
                        @endif
                    </div>

                    <button type="button" wire:click="removeDocument({{ $index }})" class="pb-2.5 text-[0.8125rem] font-medium text-meta hover:text-radix-red-deep">Remove</button>
                </div>
            @endforeach
        </section>

        <div class="flex items-center gap-3 border-t border-hairline pt-6">
            <x-ui.button type="submit" variant="primary" size="lg">Save product</x-ui.button>
            <a href="{{ route('admin.products.index') }}" class="text-[0.8125rem] font-medium text-meta">Cancel</a>
        </div>
    </form>
</div>
