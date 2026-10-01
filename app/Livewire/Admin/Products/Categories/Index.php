<?php

namespace App\Livewire\Admin\Products\Categories;

use App\Models\ProductCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Product Categories')]
class Index extends Component
{
    use WithPagination;

    public bool $showTrashed = false;

    public function mount(): void
    {
        $this->authorize('products.manage');
    }

    public function toggleTrashed(): void
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('products.manage');

        $category = ProductCategory::findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorize('products.manage');

        ProductCategory::findOrFail($id)->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function restore(int $id): void
    {
        $this->authorize('products.manage');

        ProductCategory::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Category restored.');
    }

    public function render()
    {
        $categories = ProductCategory::query()
            ->when($this->showTrashed, fn ($query) => $query->onlyTrashed())
            ->ordered()
            ->paginate(20);

        return view('livewire.admin.products.categories.index', ['categories' => $categories]);
    }
}
