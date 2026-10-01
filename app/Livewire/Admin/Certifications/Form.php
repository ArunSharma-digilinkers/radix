<?php

namespace App\Livewire\Admin\Certifications;

use App\Models\Certification;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
#[Title('Certification')]
class Form extends Component
{
    use WithFileUploads;

    public ?Certification $certification = null;

    public string $name = '';

    public string $issuer = '';

    public string $referenceNo = '';

    public string $issuedOn = '';

    public string $expiresOn = '';

    public int $sortOrder = 0;

    public bool $isActive = true;

    public $newImage = null;

    public ?int $existingImageId = null;

    public ?string $existingImageUrl = null;

    public function mount(?Certification $certification = null): void
    {
        $this->authorize('infrastructure.manage');

        if ($certification) {
            $this->certification = $certification;
            $this->name = $certification->getTranslation('name', 'en') ?? '';
            $this->issuer = (string) $certification->issuer;
            $this->referenceNo = (string) $certification->reference_no;
            $this->issuedOn = $certification->issued_on?->format('Y-m-d') ?? '';
            $this->expiresOn = $certification->expires_on?->format('Y-m-d') ?? '';
            $this->sortOrder = $certification->sort_order;
            $this->isActive = (bool) $certification->is_active;

            if ($image = $certification->certificateImage) {
                $this->existingImageId = $image->id;
                $this->existingImageUrl = $image->url();
            }
        }
    }

    public function save(): void
    {
        $this->authorize('infrastructure.manage');

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'issuer' => ['nullable', 'string', 'max:255'],
            'referenceNo' => ['nullable', 'string', 'max:255'],
            'issuedOn' => ['nullable', 'date'],
            'expiresOn' => ['nullable', 'date'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
            'newImage' => ['nullable', 'image', 'max:4096'],
        ]);

        $certification = $this->certification ?? new Certification;
        $certification->fill([
            'issuer' => $validated['issuer'] ?: null,
            'reference_no' => $validated['referenceNo'] ?: null,
            'issued_on' => $validated['issuedOn'] !== '' ? $validated['issuedOn'] : null,
            'expires_on' => $validated['expiresOn'] !== '' ? $validated['expiresOn'] : null,
            'sort_order' => $validated['sortOrder'],
            'is_active' => $validated['isActive'],
        ]);
        $certification->setTranslation('name', 'en', $validated['name']);
        $certification->save();

        $this->certification = $certification;
        $this->syncCertificateImage($certification);

        session()->flash('success', 'Certification saved.');

        $this->redirect(route('admin.certifications.index'), navigate: false);
    }

    private function syncCertificateImage(Certification $certification): void
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

        $path = $this->newImage->store("certifications/{$certification->id}", 'public');

        $certification->media()->create([
            'collection' => Media::COLLECTION_CERTIFICATE,
            'disk' => 'public',
            'path' => $path,
            'filename' => $this->newImage->getClientOriginalName(),
            'mime_type' => $this->newImage->getMimeType(),
            'size_bytes' => $this->newImage->getSize(),
            'alt' => ['en' => $this->name.' certificate'],
            'sort_order' => 0,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.certifications.form');
    }
}
