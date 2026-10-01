<?php

namespace App\Livewire\Admin\Careers;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Same shape as Enquiries\Inbox, and the same reason: never declare a public
 * JobApplication (or collection of them) property. $hidden on the model
 * (resume_path, resume_disk, internal_notes) only governs toArray()/toJson(),
 * not what a bound model property would expose in Livewire's wire snapshot —
 * so everything this component acts on is a scalar id/string, and the
 * applications themselves are only ever local variables inside render().
 */
#[Layout('layouts::admin')]
#[Title('Applications')]
class Applications extends Component
{
    use WithPagination;

    /** open | all */
    public string $filter = 'open';

    public string $search = '';

    /** @var array<int, string> Keyed by application id — populated fresh each render(). */
    public array $noteDrafts = [];

    public function mount(): void
    {
        $this->authorize('careers.manage');
    }

    public function updating($property): void
    {
        if (in_array($property, ['filter', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function updateStatus(int $id, string $status): void
    {
        $this->authorize('careers.manage');

        if (! in_array($status, JobApplication::STATUSES, true)) {
            return;
        }

        JobApplication::whereKey($id)->update(['status' => $status]);
    }

    public function saveNote(int $id): void
    {
        $this->authorize('careers.manage');

        JobApplication::whereKey($id)->update(['internal_notes' => $this->noteDrafts[$id] ?? '']);
        session()->flash('success', 'Note saved.');
    }

    public function download(int $id)
    {
        $this->authorize('careers.manage');

        $application = JobApplication::findOrFail($id);

        abort_unless($application->resume_path, 404);

        return Storage::disk($application->resume_disk)->download($application->resume_path);
    }

    private function baseQuery()
    {
        return JobApplication::query()
            ->with('opening')
            ->when($this->filter === 'open', fn ($query) => $query->whereNotIn('status', [
                JobApplication::STATUS_REJECTED,
                JobApplication::STATUS_HIRED,
            ]))
            ->when($this->search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')));
    }

    public function render()
    {
        $applications = $this->baseQuery()->orderByDesc('created_at')->paginate(20);

        foreach ($applications as $application) {
            $this->noteDrafts[$application->id] ??= $application->internal_notes ?? '';
        }

        return view('livewire.admin.careers.applications', ['applications' => $applications]);
    }
}
