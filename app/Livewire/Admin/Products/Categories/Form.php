<?php

namespace App\Livewire\Admin\Products\Categories;

use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Product Category')]
class Form extends Component
{
    public ?ProductCategory $category = null;

    public string $name = '';

    public string $description = '';

    public string $slug = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    public function mount(?ProductCategory $category = null): void
    {
        $this->authorize('products.manage');

        if ($category) {
            $this->category = $category;
            $this->name = $category->getTranslation('name', 'en') ?? '';
            $this->description = $category->getTranslation('description', 'en') ?? '';
            $this->slug = $category->slug;
            $this->sort_order = $category->sort_order;
            $this->is_active = $category->is_active;
        }
    }

    public function save(): void
    {
        $this->authorize('products.manage');

        $this->slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('product_categories', 'slug')->ignore($this->category?->id)],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $category = $this->category ?? new ProductCategory;
        $category->fill([
            'slug' => $validated['slug'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'],
        ]);
        $category->setTranslation('name', 'en', $validated['name']);
        $category->setTranslation('description', 'en', $validated['description'] ?? '');
        $category->save();

        session()->flash('success', 'Category saved.');

        $this->redirect(route('admin.products.categories.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.products.categories.form');
    }
}
