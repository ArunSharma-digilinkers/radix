<?php

namespace App\Livewire\Admin\Dealers;

use App\Models\Dealer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Dealers')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $type = '';

    public bool $showTrashed = false;

    public function mount(): void
    {
        $this->authorize('dealers.manage');
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'type', 'showTrashed'], true)) {
            $this->resetPage();
        }
    }

    public function toggleTrashed(): void
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('dealers.manage');

        $dealer = Dealer::findOrFail($id);
        $dealer->update(['is_active' => ! $dealer->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorize('dealers.manage');

        Dealer::findOrFail($id)->delete();
        session()->flash('success', 'Dealer deleted.');
    }

    public function restore(int $id): void
    {
        $this->authorize('dealers.manage');

        Dealer::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Dealer restored.');
    }

    public function render()
    {
        $dealers = Dealer::query()
            ->when($this->showTrashed, fn ($query) => $query->onlyTrashed())
            // Admin search reuses the same anchored scope the public locator
            // uses, plus the dealer name — an admin looking a specific record
            // up is more likely to type its name than a visitor is.
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->when($this->type !== '', fn ($query) => $query->ofType($this->type))
            ->orderBy('state')
            ->orderBy('city')
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.dealers.index', ['dealers' => $dealers]);
    }
}
