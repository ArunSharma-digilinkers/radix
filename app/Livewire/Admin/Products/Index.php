<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Products')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $kind = '';

    public string $categoryId = '';

    public bool $showTrashed = false;

    public function mount(): void
    {
        $this->authorize('products.manage');
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'kind', 'categoryId', 'showTrashed'], true)) {
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
        $this->authorize('products.manage');

        $product = Product::findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorize('products.manage');

        Product::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted.');
    }

    public function restore(int $id): void
    {
        $this->authorize('products.manage');

        Product::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Product restored.');
    }

    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->when($this->showTrashed, fn ($query) => $query->onlyTrashed())
            ->when($this->search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('slug', 'like', '%'.$this->search.'%')
                ->orWhere('name->en', 'like', '%'.$this->search.'%')))
            ->when($this->kind !== '', fn ($query) => $query->ofKind($this->kind))
            ->when($this->categoryId !== '', fn ($query) => $query->where('product_category_id', $this->categoryId))
            ->ordered()
            ->paginate(20);

        return view('livewire.admin.products.index', [
            'products' => $products,
            'categories' => ProductCategory::ordered()->get(),
        ]);
    }
}
