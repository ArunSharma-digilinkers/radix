<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Contracts\View\View;

/**
 * Public blog — card grid, category pages, article page (brief §4).
 *
 * Every list goes through Post::published(), which is "has a publish date that
 * has passed". Drafts and scheduled posts are therefore invisible here without
 * a second flag to keep in sync, and a scheduled post appears on its own at the
 * minute it is due.
 *
 * URLs are clean and permanent (CLAUDE.md §4): /blog, /blog/category/{slug},
 * /blog/{slug}. The category filter is a real path rather than ?category= so
 * it can be linked, shared and indexed like any other page.
 */
class BlogController extends Controller
{
    private const PER_PAGE = 9;

    public function index(): View
    {
        return view('pages.blog.index', [
            'category' => null,
            'posts' => $this->query()->paginate(self::PER_PAGE),
            'categories' => PostCategory::forDisplay()->get(),
        ]);
    }

    public function category(PostCategory $category): View
    {
        abort_unless($category->is_active, 404);

        return view('pages.blog.index', [
            'category' => $category,
            'posts' => $this->query()->where('post_category_id', $category->id)->paginate(self::PER_PAGE),
            'categories' => PostCategory::forDisplay()->get(),
        ]);
    }

    public function show(Post $post): View
    {
        // Route model binding resolves by slug and already excludes soft
        // deletes; publication is the check it cannot make for us.
        abort_unless($post->isPublished(), 404);

        return view('pages.blog.show', [
            'post' => $post->load(['category', 'image', 'author']),
            'related' => Post::relatedTo($post)->with(['category', 'image'])->get(),
        ]);
    }

    private function query()
    {
        return Post::published()
            ->with(['category', 'image'])
            ->latestFirst();
    }
}
