<?php

namespace App\Livewire\Admin\Blog;

use App\Models\Media;
use App\Models\Post;
use App\Models\PostCategory;
use App\Support\Html\RichText;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
#[Title('Post')]
class Form extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    public string $title = '';

    public string $excerpt = '';

    public string $body = '';

    public string $slug = '';

    public string $categoryId = '';

    public string $authorName = '';

    /** Datetime-local string; empty means draft. */
    public string $publishedAt = '';

    public bool $isFeatured = false;

    public string $readingMinutes = '';

    public string $metaTitle = '';

    public string $metaDescription = '';

    public $newImage = null;

    public string $imageAlt = '';

    public ?int $existingImageId = null;

    public ?string $existingImageUrl = null;

    public function mount(?Post $post = null): void
    {
        $this->authorize('blog.manage');

        if ($post) {
            $this->post = $post;
            $this->title = $post->getTranslation('title', 'en') ?? '';
            $this->excerpt = $post->getTranslation('excerpt', 'en') ?? '';
            $this->body = $post->getTranslation('body', 'en') ?? '';
            $this->slug = $post->slug;
            $this->categoryId = (string) ($post->post_category_id ?? '');
            $this->authorName = $post->author_name ?? '';
            $this->publishedAt = $post->published_at?->format('Y-m-d\TH:i') ?? '';
            $this->isFeatured = $post->is_featured;
            $this->readingMinutes = (string) ($post->reading_minutes ?? '');
            $this->metaTitle = $post->getTranslation('meta_title', 'en') ?? '';
            $this->metaDescription = $post->getTranslation('meta_description', 'en') ?? '';

            if ($image = $post->image) {
                $this->existingImageId = $image->id;
                $this->existingImageUrl = $image->url();
                $this->imageAlt = $image->altText();
            }
        }
    }

    /**
     * Fill the publish date from the SERVER clock.
     *
     * A datetime-local input carries no timezone, so a hand-typed value means
     * whatever the app decides it means. Letting the server stamp it removes
     * the author's browser clock — and any conversion they might do in their
     * head — from the equation entirely.
     */
    public function publishNow(): void
    {
        $this->authorize('blog.manage');

        $this->publishedAt = now()->format('Y-m-d\TH:i');
    }

    public function makeDraft(): void
    {
        $this->authorize('blog.manage');

        $this->publishedAt = '';
    }

    public function save(): void
    {
        $this->authorize('blog.manage');

        $this->slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->title);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($this->post?->id)],
            'categoryId' => ['nullable', 'string'],
            'authorName' => ['nullable', 'string', 'max:255'],
            'publishedAt' => ['nullable', 'date'],
            'isFeatured' => ['boolean'],
            'readingMinutes' => ['nullable', 'integer', 'min:1'],
            'metaTitle' => ['nullable', 'string', 'max:255'],
            'metaDescription' => ['nullable', 'string', 'max:255'],
            'newImage' => ['nullable', 'image', 'max:4096'],
            'imageAlt' => [Rule::requiredIf(fn () => $this->newImage || $this->existingImageId), 'nullable', 'string', 'max:255'],
        ]);

        $post = $this->post ?? new Post;
        $post->fill([
            'slug' => $validated['slug'],
            'post_category_id' => $validated['categoryId'] !== '' ? $validated['categoryId'] : null,
            'user_id' => $post->user_id ?? auth()->id(),
            'author_name' => $validated['authorName'] ?: null,
            'published_at' => $validated['publishedAt'] !== '' ? Carbon::parse($validated['publishedAt']) : null,
            'is_featured' => $validated['isFeatured'],
            'reading_minutes' => $validated['readingMinutes'] !== '' ? $validated['readingMinutes'] : null,
        ]);
        $post->setTranslation('title', 'en', $validated['title']);
        $post->setTranslation('excerpt', 'en', $validated['excerpt'] ?? '');
        // The body arrives as HTML from CKEditor. Everything that is not on the
        // allowlist is stripped here, once, so the stored value is safe to echo
        // unescaped on the public side (see App\Support\Html\RichText).
        $body = RichText::sanitize($validated['body'] ?? '');
        $post->setTranslation('body', 'en', RichText::isBlank($body) ? '' : $body);
        $post->setTranslation('meta_title', 'en', $validated['metaTitle'] ?? '');
        $post->setTranslation('meta_description', 'en', $validated['metaDescription'] ?? '');
        $post->save();

        $this->post = $post;
        $this->syncImage($post);

        /*
         * Say what actually happened, not just "saved". A post with no publish
         * date is invisible on the public site, and "Post saved." next to an
         * empty blog page is how an author concludes the site is broken when
         * in fact they simply left the date blank.
         */
        session()->flash('success', match (true) {
            $post->published_at === null => 'Saved as a draft — set a publish date to put it on the site.',
            $post->published_at->isFuture() => 'Saved. Scheduled to go live on '.$post->published_at->format('d M Y, H:i').'.',
            default => 'Published. It is live on the blog now.',
        });

        $this->redirect(route('admin.blog.index'), navigate: false);
    }

    private function syncImage(Post $post): void
    {
        if ($this->newImage) {
            if ($this->existingImageId) {
                $old = Media::find($this->existingImageId);
                if ($old) {
                    Storage::disk($old->disk ?? 'public')->delete($old->path);
                    $old->delete();
                }
            }

            $path = $this->newImage->store("posts/{$post->id}", 'public');

            $post->media()->create([
                'collection' => Media::COLLECTION_MAIN,
                'disk' => 'public',
                'path' => $path,
                'filename' => $this->newImage->getClientOriginalName(),
                'mime_type' => $this->newImage->getMimeType(),
                'size_bytes' => $this->newImage->getSize(),
                'alt' => ['en' => $this->imageAlt],
                'sort_order' => 0,
            ]);
        } elseif ($this->existingImageId) {
            Media::whereKey($this->existingImageId)->update(['alt' => ['en' => $this->imageAlt]]);
        }
    }

    public function render()
    {
        return view('livewire.admin.blog.form', [
            'categories' => PostCategory::ordered()->get(),
        ]);
    }
}
