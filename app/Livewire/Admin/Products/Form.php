<?php

namespace App\Livewire\Admin\Products;

use App\Models\Media;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductDocument;
use App\Models\ProductFaq;
use App\Models\ProductSpec;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Create and edit in one component (mount(?Product $product)). Specs, FAQs,
 * variants and documents are plain in-memory arrays, added/removed with the
 * rest of the form and persisted together on save — not autosaved per
 * keystroke, not separate Livewire child components. Reordering is manual
 * up/down rather than wire:sort: these rows aren't independently persisted
 * until save, and wire:sort's documented pattern targets an already-saved
 * Eloquent collection with real primary keys.
 *
 * Uploaded files are kept in their own parallel arrays ($documentFiles,
 * $galleryFiles) rather than nested inside $documents/$galleryItems.
 * Livewire's property synthesizer supports a single UploadedFile property or
 * an array of UploadedFiles, but not an UploadedFile nested inside a mixed
 * associative array alongside scalar metadata — that shape isn't
 * synthesizable and throws at hydration time.
 */
#[Layout('layouts::admin')]
#[Title('Product')]
class Form extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public string $name = '';

    public string $pitch = '';

    public string $description = '';

    public string $useCases = '';

    public string $slug = '';

    public string $kind = Product::KIND_BATTERY;

    public string $categoryId = '';

    public int $sortOrder = 0;

    public bool $isActive = true;

    public bool $isFeatured = false;

    public $newImage = null;

    public string $imageAlt = '';

    public ?int $existingImageId = null;

    public ?string $existingImageUrl = null;

    /** @var array<int, array{id: ?int, model_code: string, name: string, capacity_ah: string, voltage: string, warranty_months: string, dimensions_mm: string, weight_kg: string, is_active: bool}> */
    public array $variants = [];

    /** @var array<int, array{id: ?int, label: string, value: string, group: string}> */
    public array $specs = [];

    /** @var array<int, array{id: ?int, question: string, answer: string, is_active: bool}> */
    public array $faqs = [];

    /** @var array<int, array{id: ?int, title: string, type: string, existing_path: ?string}> */
    public array $documents = [];

    // Uploaded files for each documents() row, aligned by array index.
    /** @var array<int, mixed> */
    public array $documentFiles = [];

    /** @var array<int, array{id: ?int, alt: string, existing_path: ?string}> */
    public array $galleryItems = [];

    // Uploaded files for each galleryItems() row, aligned by array index.
    /** @var array<int, mixed> */
    public array $galleryFiles = [];

    public function mount(?Product $product = null): void
    {
        $this->authorize('products.manage');

        if ($product) {
            $this->product = $product;
            $this->name = $product->getTranslation('name', 'en') ?? '';
            $this->pitch = $product->getTranslation('pitch', 'en') ?? '';
            $this->description = $product->getTranslation('description', 'en') ?? '';
            $this->useCases = $product->getTranslation('use_cases', 'en') ?? '';
            $this->slug = $product->slug;
            $this->kind = $product->kind;
            $this->categoryId = (string) ($product->product_category_id ?? '');
            $this->sortOrder = $product->sort_order;
            $this->isActive = $product->is_active;
            $this->isFeatured = $product->is_featured;

            if ($image = $product->image) {
                $this->existingImageId = $image->id;
                $this->existingImageUrl = $image->url();
                $this->imageAlt = $image->altText();
            }

            $this->variants = $product->variants->map(fn (ProductVariant $v) => [
                'id' => $v->id,
                'model_code' => $v->model_code,
                'name' => $v->getTranslation('name', 'en') ?? '',
                'capacity_ah' => (string) ($v->capacity_ah ?? ''),
                'voltage' => (string) ($v->voltage ?? ''),
                'warranty_months' => (string) ($v->warranty_months ?? ''),
                'dimensions_mm' => (string) ($v->dimensions_mm ?? ''),
                'weight_kg' => (string) ($v->weight_kg ?? ''),
                'is_active' => $v->is_active,
            ])->all();

            $this->specs = $product->specs->map(fn (ProductSpec $s) => [
                'id' => $s->id,
                'label' => $s->getTranslation('label', 'en') ?? '',
                'value' => $s->getTranslation('value', 'en') ?? '',
                'group' => $s->group ?? '',
            ])->all();

            $this->faqs = $product->faqs->map(fn (ProductFaq $f) => [
                'id' => $f->id,
                'question' => $f->getTranslation('question', 'en') ?? '',
                'answer' => $f->getTranslation('answer', 'en') ?? '',
                'is_active' => $f->is_active,
            ])->all();

            $this->documents = $product->documents->map(fn (ProductDocument $d) => [
                'id' => $d->id,
                'title' => $d->getTranslation('title', 'en') ?? '',
                'type' => $d->type,
                'existing_path' => $d->path,
            ])->all();
            $this->documentFiles = array_fill(0, count($this->documents), null);

            $this->galleryItems = $product->gallery->map(fn (Media $m) => [
                'id' => $m->id,
                'alt' => $m->altText(),
                'existing_path' => $m->path,
            ])->all();
            $this->galleryFiles = array_fill(0, count($this->galleryItems), null);
        }
    }

    public function addVariant(): void
    {
        $this->variants[] = ['id' => null, 'model_code' => '', 'name' => '', 'capacity_ah' => '', 'voltage' => '', 'warranty_months' => '', 'dimensions_mm' => '', 'weight_kg' => '', 'is_active' => true];
    }

    public function removeVariant(int $index): void
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function addSpec(): void
    {
        $this->specs[] = ['id' => null, 'label' => '', 'value' => '', 'group' => ''];
    }

    public function removeSpec(int $index): void
    {
        unset($this->specs[$index]);
        $this->specs = array_values($this->specs);
    }

    public function addFaq(): void
    {
        $this->faqs[] = ['id' => null, 'question' => '', 'answer' => '', 'is_active' => true];
    }

    public function removeFaq(int $index): void
    {
        unset($this->faqs[$index]);
        $this->faqs = array_values($this->faqs);
    }

    public function addDocument(): void
    {
        $this->documents[] = ['id' => null, 'title' => '', 'type' => ProductDocument::TYPE_DATASHEET, 'existing_path' => null];
        $this->documentFiles[] = null;
    }

    public function removeDocument(int $index): void
    {
        unset($this->documents[$index], $this->documentFiles[$index]);
        $this->documents = array_values($this->documents);
        $this->documentFiles = array_values($this->documentFiles);
    }

    public function addGalleryItem(): void
    {
        $this->galleryItems[] = ['id' => null, 'alt' => '', 'existing_path' => null];
        $this->galleryFiles[] = null;
    }

    public function removeGalleryItem(int $index): void
    {
        unset($this->galleryItems[$index], $this->galleryFiles[$index]);
        $this->galleryItems = array_values($this->galleryItems);
        $this->galleryFiles = array_values($this->galleryFiles);
    }

    public function save(): void
    {
        $this->authorize('products.manage');

        $this->slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        $modelCodes = array_map(fn ($v) => $v['model_code'], $this->variants);
        if (count($modelCodes) !== count(array_unique($modelCodes))) {
            $this->addError('variants', 'Model codes must be unique within this product.');

            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'pitch' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'useCases' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($this->product?->id)],
            'kind' => ['required', Rule::in(Product::KINDS)],
            'categoryId' => ['nullable', 'string'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
            'isFeatured' => ['boolean'],

            'newImage' => ['nullable', 'image', 'max:4096'],
            'imageAlt' => [Rule::requiredIf(fn () => $this->newImage || $this->existingImageId), 'nullable', 'string', 'max:255'],

            'variants.*.model_code' => ['required', 'string', 'max:255'],
            'variants.*.name' => ['nullable', 'string'],
            'variants.*.capacity_ah' => ['nullable', 'numeric'],
            'variants.*.voltage' => ['nullable', 'numeric'],
            'variants.*.warranty_months' => ['nullable', 'integer'],
            'variants.*.dimensions_mm' => ['nullable', 'string', 'max:64'],
            'variants.*.weight_kg' => ['nullable', 'numeric'],

            'specs.*.label' => ['required', 'string'],
            'specs.*.value' => ['required', 'string'],

            'faqs.*.question' => ['required', 'string'],
            'faqs.*.answer' => ['required', 'string'],

            'documents.*.title' => ['nullable', 'string'],
            'documents.*.type' => ['nullable', Rule::in(ProductDocument::TYPES)],
            'documentFiles.*' => ['nullable', 'file', 'max:10240'],

            'galleryItems.*.alt' => ['nullable', 'string', 'max:255'],
            'galleryFiles.*' => ['nullable', 'image', 'max:4096'],
        ]);

        DB::transaction(function () use ($validated): void {
            $product = $this->product ?? new Product;
            $product->fill([
                'slug' => $validated['slug'],
                'product_category_id' => $validated['categoryId'] !== '' ? $validated['categoryId'] : null,
                'kind' => $validated['kind'],
                'sort_order' => $validated['sortOrder'],
                'is_active' => $validated['isActive'],
                'is_featured' => $validated['isFeatured'],
            ]);
            $product->setTranslation('name', 'en', $validated['name']);
            $product->setTranslation('pitch', 'en', $validated['pitch'] ?? '');
            $product->setTranslation('description', 'en', $validated['description'] ?? '');
            $product->setTranslation('use_cases', 'en', $validated['useCases'] ?? '');
            $product->save();

            $this->product = $product;

            $this->syncMainImage($product);
            $this->syncGallery($product);
            $this->syncVariants($product);
            $this->syncSpecs($product);
            $this->syncFaqs($product);
            $this->syncDocuments($product);
        });

        session()->flash('success', 'Product saved.');

        $this->redirect(route('admin.products.index'), navigate: false);
    }

    private function syncMainImage(Product $product): void
    {
        if ($this->newImage) {
            if ($this->existingImageId) {
                $old = Media::find($this->existingImageId);
                if ($old) {
                    Storage::disk($old->disk ?? 'public')->delete($old->path);
                    $old->delete();
                }
            }

            $path = $this->newImage->store("products/{$product->id}", 'public');

            $product->media()->create([
                'collection' => Media::COLLECTION_MAIN,
                'disk' => 'public',
                'path' => $path,
                'filename' => $this->newImage->getClientOriginalName(),
                'mime_type' => $this->newImage->getMimeType(),
                'size_bytes' => $this->newImage->getSize(),
                'alt' => ['en' => $this->imageAlt],
                'sort_order' => 0,
            ]);
        } elseif ($this->existingImageId) {
            Media::whereKey($this->existingImageId)->update(['alt' => ['en' => $this->imageAlt]]);
        }
    }

    private function syncGallery(Product $product): void
    {
        $keptIds = [];

        foreach ($this->galleryItems as $index => $item) {
            $file = $this->galleryFiles[$index] ?? null;

            if ($file) {
                $path = $file->store("products/{$product->id}/gallery", 'public');

                $media = $product->media()->create([
                    'collection' => Media::COLLECTION_GALLERY,
                    'disk' => 'public',
                    'path' => $path,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                    'alt' => ['en' => $item['alt']],
                    'sort_order' => $index,
                ]);
                $keptIds[] = $media->id;
            } elseif ($item['id']) {
                Media::whereKey($item['id'])->update(['alt' => ['en' => $item['alt']], 'sort_order' => $index]);
                $keptIds[] = $item['id'];
            }
        }

        foreach ($product->gallery as $media) {
            if (! in_array($media->id, $keptIds, true)) {
                Storage::disk($media->disk ?? 'public')->delete($media->path);
                $media->delete();
            }
        }
    }

    private function syncVariants(Product $product): void
    {
        $keptIds = [];

        foreach ($this->variants as $index => $row) {
            $variant = $row['id'] ? ProductVariant::find($row['id']) : new ProductVariant;
            $variant->product_id = $product->id;
            $variant->model_code = $row['model_code'];
            $variant->capacity_ah = $row['capacity_ah'] !== '' ? $row['capacity_ah'] : null;
            $variant->voltage = $row['voltage'] !== '' ? $row['voltage'] : null;
            $variant->warranty_months = $row['warranty_months'] !== '' ? $row['warranty_months'] : null;
            $variant->dimensions_mm = $row['dimensions_mm'] !== '' ? $row['dimensions_mm'] : null;
            $variant->weight_kg = $row['weight_kg'] !== '' ? $row['weight_kg'] : null;
            $variant->is_active = $row['is_active'];
            $variant->sort_order = $index;
            $variant->setTranslation('name', 'en', $row['name'] ?? '');
            $variant->save();
            $keptIds[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keptIds ?: [0])->delete();
    }

    private function syncSpecs(Product $product): void
    {
        $keptIds = [];

        foreach ($this->specs as $index => $row) {
            $spec = $row['id'] ? ProductSpec::find($row['id']) : new ProductSpec;
            $spec->product_id = $product->id;
            $spec->group = $row['group'] ?: null;
            $spec->sort_order = $index;
            $spec->setTranslation('label', 'en', $row['label']);
            $spec->setTranslation('value', 'en', $row['value']);
            $spec->save();
            $keptIds[] = $spec->id;
        }

        $product->specs()->whereNotIn('id', $keptIds ?: [0])->delete();
    }

    private function syncFaqs(Product $product): void
    {
        $keptIds = [];

        foreach ($this->faqs as $index => $row) {
            $faq = $row['id'] ? ProductFaq::find($row['id']) : new ProductFaq;
            $faq->product_id = $product->id;
            $faq->is_active = $row['is_active'];
            $faq->sort_order = $index;
            $faq->setTranslation('question', 'en', $row['question']);
            $faq->setTranslation('answer', 'en', $row['answer']);
            $faq->save();
            $keptIds[] = $faq->id;
        }

        $product->faqs()->whereNotIn('id', $keptIds ?: [0])->delete();
    }

    private function syncDocuments(Product $product): void
    {
        $keptIds = [];

        foreach ($this->documents as $index => $row) {
            $file = $this->documentFiles[$index] ?? null;

            if ($file) {
                $path = $file->store("product-documents/{$product->id}", 'public');

                $document = new ProductDocument;
                $document->product_id = $product->id;
                $document->disk = 'public';
                $document->path = $path;
                $document->mime_type = $file->getMimeType();
                $document->size_bytes = $file->getSize();
                $document->type = $row['type'] ?: ProductDocument::TYPE_DATASHEET;
                $document->sort_order = $index;
                $document->setTranslation('title', 'en', $row['title'] ?: $file->getClientOriginalName());
                $document->save();
                $keptIds[] = $document->id;
            } elseif ($row['id']) {
                $document = ProductDocument::find($row['id']);
                if ($document) {
                    $document->type = $row['type'] ?: $document->type;
                    $document->sort_order = $index;
                    $document->setTranslation('title', 'en', $row['title']);
                    $document->save();
                    $keptIds[] = $document->id;
                }
            }
        }

        foreach ($product->documents as $document) {
            if (! in_array($document->id, $keptIds, true)) {
                Storage::disk($document->disk)->delete($document->path);
                $document->delete();
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.products.form', [
            'categories' => ProductCategory::ordered()->get(),
        ]);
    }
}
