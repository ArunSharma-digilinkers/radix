<?php

namespace App\Livewire\Admin\Careers;

use App\Models\JobOpening;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Job Opening')]
class Form extends Component
{
    public ?JobOpening $opening = null;

    public string $title = '';

    public string $slug = '';

    public string $department = '';

    public string $location = '';

    public string $employmentType = JobOpening::TYPE_FULL_TIME;

    public string $description = '';

    public string $requirements = '';

    /** Datetime-local string; empty means draft. Same convention as the blog post form. */
    public string $publishedAt = '';

    public string $closesOn = '';

    public function mount(?JobOpening $opening = null): void
    {
        $this->authorize('careers.manage');

        if ($opening) {
            $this->opening = $opening;
            $this->title = $opening->getTranslation('title', 'en') ?? '';
            $this->slug = $opening->slug;
            $this->department = (string) $opening->department;
            $this->location = (string) $opening->location;
            $this->employmentType = $opening->employment_type;
            $this->description = $opening->getTranslation('description', 'en') ?? '';
            $this->requirements = $opening->getTranslation('requirements', 'en') ?? '';
            $this->publishedAt = $opening->published_at?->format('Y-m-d\TH:i') ?? '';
            $this->closesOn = $opening->closes_on?->format('Y-m-d') ?? '';
        }
    }

    public function publishNow(): void
    {
        $this->authorize('careers.manage');

        $this->publishedAt = now()->format('Y-m-d\TH:i');
    }

    public function makeDraft(): void
    {
        $this->authorize('careers.manage');

        $this->publishedAt = '';
    }

    public function save(): void
    {
        $this->authorize('careers.manage');

        $this->slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->title);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('job_openings', 'slug')->ignore($this->opening?->id)],
            'department' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employmentType' => ['required', Rule::in(JobOpening::TYPES)],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'publishedAt' => ['nullable', 'date'],
            'closesOn' => ['nullable', 'date'],
        ]);

        $opening = $this->opening ?? new JobOpening;
        $opening->fill([
            'slug' => $validated['slug'],
            'department' => $validated['department'] ?: null,
            'location' => $validated['location'] ?: null,
            'employment_type' => $validated['employmentType'],
            'published_at' => $validated['publishedAt'] !== '' ? Carbon::parse($validated['publishedAt']) : null,
            'closes_on' => $validated['closesOn'] !== '' ? $validated['closesOn'] : null,
        ]);
        $opening->setTranslation('title', 'en', $validated['title']);
        $opening->setTranslation('description', 'en', $validated['description'] ?? '');
        $opening->setTranslation('requirements', 'en', $validated['requirements'] ?? '');
        $opening->save();

        $this->opening = $opening;

        session()->flash('success', match (true) {
            $opening->published_at === null => 'Saved as a draft — set a publish date to put it on the site.',
            $opening->published_at->isFuture() => 'Saved. Scheduled to go live on '.$opening->published_at->format('d M Y, H:i').'.',
            default => 'Published. It is live on the careers page now.',
        });

        $this->redirect(route('admin.careers.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.careers.form');
    }
}
