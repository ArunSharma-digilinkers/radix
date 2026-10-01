<?php

namespace App\Livewire\Admin\Infrastructure;

use App\Models\Media;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Manages the two Infrastructure-page facts CLAUDE.md §8 blocks until Radix
 * supplies them: the factory/QC gallery and the capacity figure. Both are
 * real content — SiteSetting::galleryOwner()'s media and the
 * 'infrastructure_capacity' setting — starting empty, not placeholder.
 */
#[Layout('layouts::admin')]
#[Title('Infrastructure')]
class Gallery extends Component
{
    use WithFileUploads;

    public string $capacity = '';

    public $newFile = null;

    public string $newFileAlt = '';

    public function mount(): void
    {
        $this->authorize('infrastructure.manage');

        $this->capacity = (string) SiteSetting::get('infrastructure_capacity', '');
    }

    public function saveCapacity(): void
    {
        $this->authorize('infrastructure.manage');

        $validated = $this->validate(['capacity' => ['nullable', 'string', 'max:255']]);

        SiteSetting::updateOrCreate(
            ['key' => 'infrastructure_capacity'],
            ['value' => $validated['capacity'], 'group' => 'infrastructure']
        );

        session()->flash('success', 'Capacity figure saved.');
    }

    public function upload(): void
    {
        $this->authorize('infrastructure.manage');

        $validated = $this->validate([
            'newFile' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:20480'],
            'newFileAlt' => [Rule::requiredIf(fn () => $this->newFile && str_starts_with($this->newFile->getMimeType() ?? '', 'image/')), 'nullable', 'string', 'max:255'],
        ]);

        $owner = SiteSetting::galleryOwner();
        $path = $this->newFile->store('infrastructure', 'public');

        $owner->media()->create([
            'collection' => Media::COLLECTION_FACTORY,
            'disk' => 'public',
            'path' => $path,
            'filename' => $this->newFile->getClientOriginalName(),
            'mime_type' => $this->newFile->getMimeType(),
            'size_bytes' => $this->newFile->getSize(),
            'alt' => ['en' => $validated['newFileAlt'] ?? ''],
            'sort_order' => $owner->media()->where('collection', Media::COLLECTION_FACTORY)->count(),
        ]);

        $this->reset(['newFile', 'newFileAlt']);
        session()->flash('success', 'Uploaded.');
    }

    public function delete(int $id): void
    {
        $this->authorize('infrastructure.manage');

        $media = Media::findOrFail($id);
        Storage::disk($media->disk ?? 'public')->delete($media->path);
        $media->delete();

        session()->flash('success', 'Deleted.');
    }

    public function render()
    {
        $items = SiteSetting::galleryOwner()->media()->where('collection', Media::COLLECTION_FACTORY)->get();

        return view('livewire.admin.infrastructure.gallery', ['items' => $items]);
    }
}
