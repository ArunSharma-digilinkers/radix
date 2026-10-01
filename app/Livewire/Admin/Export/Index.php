<?php

namespace App\Livewire\Admin\Export;

use App\Models\ExportMarket;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Export Markets')]
class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('export.manage');
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('export.manage');

        $market = ExportMarket::findOrFail($id);
        $market->update(['is_active' => ! $market->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorize('export.manage');

        ExportMarket::findOrFail($id)->delete();
        session()->flash('success', 'Market deleted.');
    }

    public function render()
    {
        $markets = ExportMarket::query()->ordered()->paginate(20);

        return view('livewire.admin.export.index', ['markets' => $markets]);
    }
}
