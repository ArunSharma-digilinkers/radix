<?php

namespace App\Livewire\Admin\Blog;

use App\Models\Post;
use App\Models\PostCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Blog')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    /** '' | draft | scheduled | published */
    public string $status = '';

    public string $categoryId = '';

    public bool $showTrashed = false;

    public function mount(): void
    {
        $this->authorize('blog.manage');
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'status', 'categoryId', 'showTrashed'], true)) {
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
        $this->authorize('blog.manage');

        Post::findOrFail($id)->delete();
        session()->flash('success', 'Post deleted.');
    }

    public function restore(int $id): void
    {
        $this->authorize('blog.manage');

        Post::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Post restored.');
    }

    public function render()
    {
        $posts = Post::query()
            ->with('category')
            ->when($this->showTrashed, fn ($query) => $query->onlyTrashed())
            ->when($this->search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('slug', 'like', '%'.$this->search.'%')
                ->orWhere('title->en', 'like', '%'.$this->search.'%')))
            ->when($this->categoryId !== '', fn ($query) => $query->where('post_category_id', $this->categoryId))
            ->when($this->status === 'draft', fn ($query) => $query->whereNull('published_at'))
            ->when($this->status === 'scheduled', fn ($query) => $query->where('published_at', '>', now()))
            ->when($this->status === 'published', fn ($query) => $query->published())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.admin.blog.index', [
            'posts' => $posts,
            'categories' => PostCategory::ordered()->get(),
        ]);
    }
}
