<?php

namespace App\Livewire\Admin\Blog\Categories;

use App\Models\PostCategory;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Blog Category')]
class Form extends Component
{
    public ?PostCategory $category = null;

    public string $name = '';

    public string $description = '';

    public string $slug = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    public function mount(?PostCategory $category = null): void
    {
        $this->authorize('blog.manage');

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
        $this->authorize('blog.manage');

        $this->slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('post_categories', 'slug')->ignore($this->category?->id)],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $category = $this->category ?? new PostCategory;
        $category->fill([
            'slug' => $validated['slug'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'],
        ]);
        $category->setTranslation('name', 'en', $validated['name']);
        $category->setTranslation('description', 'en', $validated['description'] ?? '');
        $category->save();

        session()->flash('success', 'Category saved.');

        $this->redirect(route('admin.blog.categories.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.blog.categories.form');
    }
}
