<?php

namespace App\Livewire\Admin\Careers;

use App\Models\JobOpening;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Careers')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    /** '' | draft | scheduled | published */
    public string $status = '';

    public bool $showTrashed = false;

    public function mount(): void
    {
        $this->authorize('careers.manage');
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'status', 'showTrashed'], true)) {
            $this->resetPage();
        }
    }

    public function toggleTrashed(): void
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $this->authorize('careers.manage');

        JobOpening::findOrFail($id)->delete();
        session()->flash('success', 'Opening deleted.');
    }

    public function restore(int $id): void
    {
        $this->authorize('careers.manage');

        JobOpening::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Opening restored.');
    }

    public function render()
    {
        $openings = JobOpening::query()
            ->when($this->showTrashed, fn ($query) => $query->onlyTrashed())
            ->when($this->search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('slug', 'like', '%'.$this->search.'%')
                ->orWhere('title->en', 'like', '%'.$this->search.'%')
                ->orWhere('department', 'like', '%'.$this->search.'%')))
            ->when($this->status === 'draft', fn ($query) => $query->whereNull('published_at'))
            ->when($this->status === 'scheduled', fn ($query) => $query->where('published_at', '>', now()))
            ->when($this->status === 'published', fn ($query) => $query->whereNotNull('published_at')->where('published_at', '<=', now()))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.admin.careers.index', ['openings' => $openings]);
    }
}
