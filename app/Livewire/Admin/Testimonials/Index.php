<?php

namespace App\Livewire\Admin\Testimonials;

use App\Models\Testimonial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Testimonials')]
class Index extends Component
{
    use WithPagination;

    public bool $showTrashed = false;

    public function mount(): void
    {
        $this->authorize('testimonials.manage');
    }

    public function toggleTrashed(): void
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('testimonials.manage');

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update(['is_active' => ! $testimonial->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorize('testimonials.manage');

        Testimonial::findOrFail($id)->delete();
        session()->flash('success', 'Testimonial deleted.');
    }

    public function restore(int $id): void
    {
        $this->authorize('testimonials.manage');

        Testimonial::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Testimonial restored.');
    }

    public function render()
    {
        $testimonials = Testimonial::query()
            ->when($this->showTrashed, fn ($query) => $query->onlyTrashed())
            ->ordered()
            ->paginate(20);

        return view('livewire.admin.testimonials.index', ['testimonials' => $testimonials]);
    }
}
