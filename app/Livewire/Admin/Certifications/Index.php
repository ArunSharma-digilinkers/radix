<?php

namespace App\Livewire\Admin\Certifications;

use App\Models\Certification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Certifications')]
class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('infrastructure.manage');
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('infrastructure.manage');

        $certification = Certification::findOrFail($id);
        $certification->update(['is_active' => ! $certification->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorize('infrastructure.manage');

        Certification::findOrFail($id)->delete();
        session()->flash('success', 'Certification deleted.');
    }

    public function render()
    {
        $certifications = Certification::query()->ordered()->paginate(20);

        return view('livewire.admin.certifications.index', ['certifications' => $certifications]);
    }
}
