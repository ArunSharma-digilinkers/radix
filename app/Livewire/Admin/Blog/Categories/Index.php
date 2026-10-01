<?php

namespace App\Livewire\Admin\Blog\Categories;

use App\Models\PostCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Blog Categories')]
class Index extends Component
{
    use WithPagination;

    public bool $showTrashed = false;

    public function mount(): void
    {
        $this->authorize('blog.manage');
    }

    public function toggleTrashed(): void
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('blog.manage');

        $category = PostCategory::findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorize('blog.manage');

        PostCategory::findOrFail($id)->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function restore(int $id): void
    {
        $this->authorize('blog.manage');

        PostCategory::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Category restored.');
    }

    public function render()
    {
        $categories = PostCategory::query()
            ->when($this->showTrashed, fn ($query) => $query->onlyTrashed())
            ->ordered()
            ->paginate(20);

        return view('livewire.admin.blog.categories.index', ['categories' => $categories]);
    }
}
