<?php

namespace App\Livewire\Admin\Testimonials;

use App\Models\Media;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
#[Title('Testimonial')]
class Form extends Component
{
    use WithFileUploads;

    public ?Testimonial $testimonial = null;

    public string $quote = '';

    public string $authorName = '';

    public string $authorRole = '';

    public string $location = '';

    public string $type = Testimonial::TYPE_RETAIL;

    public bool $isFeatured = false;

    public int $sortOrder = 0;

    public bool $isActive = true;

    public $newImage = null;

    public ?int $existingImageId = null;

    public ?string $existingImageUrl = null;

    public function mount(?Testimonial $testimonial = null): void
    {
        $this->authorize('testimonials.manage');

        if ($testimonial) {
            $this->testimonial = $testimonial;
            $this->quote = $testimonial->getTranslation('quote', 'en') ?? '';
            $this->authorName = $testimonial->author_name;
            $this->authorRole = $testimonial->getTranslation('author_role', 'en') ?? '';
            $this->location = (string) $testimonial->location;
            $this->type = $testimonial->type;
            $this->isFeatured = (bool) $testimonial->is_featured;
            $this->sortOrder = $testimonial->sort_order;
            $this->isActive = (bool) $testimonial->is_active;

            if ($image = $testimonial->image) {
                $this->existingImageId = $image->id;
                $this->existingImageUrl = $image->url();
            }
        }
    }

    public function save(): void
    {
        $this->authorize('testimonials.manage');

        $validated = $this->validate([
            'quote' => ['required', 'string', 'max:2000'],
            'authorName' => ['required', 'string', 'max:255'],
            'authorRole' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(Testimonial::TYPES)],
            'isFeatured' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
            'newImage' => ['nullable', 'image', 'max:4096'],
        ]);

        $testimonial = $this->testimonial ?? new Testimonial;
        $testimonial->fill([
            'author_name' => $validated['authorName'],
            'location' => $validated['location'] ?: null,
            'type' => $validated['type'],
            'is_featured' => $validated['isFeatured'],
            'sort_order' => $validated['sortOrder'],
            'is_active' => $validated['isActive'],
        ]);
        $testimonial->setTranslation('quote', 'en', $validated['quote']);
        $testimonial->setTranslation('author_role', 'en', $validated['authorRole'] ?? '');
        $testimonial->save();

        $this->testimonial = $testimonial;
        $this->syncImage($testimonial);

        session()->flash('success', 'Testimonial saved.');

        $this->redirect(route('admin.testimonials.index'), navigate: false);
    }

    private function syncImage(Testimonial $testimonial): void
    {
        if (! $this->newImage) {
            return;
        }

        if ($this->existingImageId) {
            $old = Media::find($this->existingImageId);
            if ($old) {
                Storage::disk($old->disk ?? 'public')->delete($old->path);
                $old->delete();
            }
        }

        $path = $this->newImage->store("testimonials/{$testimonial->id}", 'public');

        $testimonial->media()->create([
            'collection' => Media::COLLECTION_MAIN,
            'disk' => 'public',
            'path' => $path,
            'filename' => $this->newImage->getClientOriginalName(),
            'mime_type' => $this->newImage->getMimeType(),
            'size_bytes' => $this->newImage->getSize(),
            'alt' => ['en' => $this->authorName],
            'sort_order' => 0,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.testimonials.form');
    }
}
