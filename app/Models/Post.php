<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory, HasMedia, HasTranslations, SoftDeletes;

    /**
     * Byline used when a post has no linked author, matching the concept.
     */
    public const DEFAULT_AUTHOR = 'Team Radix';

    /** @var list<string> */
    public array $translatable = ['title', 'excerpt', 'body', 'meta_title', 'meta_description'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'reading_minutes' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Published means "has a publish date that has passed" — so scheduling works
     * without a second flag, and a future date simply keeps the post hidden.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Other posts in the same category — the "related posts" the brief asks for.
     */
    public function scopeRelatedTo(Builder $query, self $post, int $limit = 3): Builder
    {
        return $query->published()
            ->whereKeyNot($post->getKey())
            ->where('post_category_id', $post->post_category_id)
            ->latestFirst()
            ->limit($limit);
    }

    public function authorName(): string
    {
        return $this->author_name ?: ($this->author?->name ?? self::DEFAULT_AUTHOR);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
}
