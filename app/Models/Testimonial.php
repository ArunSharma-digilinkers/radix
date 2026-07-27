<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * A named customer quote. Brief §6: the current site has none, and §4 asks for
 * testimonials with real names — anonymous praise reads as invented.
 */
class Testimonial extends Model
{
    use HasMedia, HasTranslations, Listable, SoftDeletes;

    public const TYPE_RETAIL = 'retail';

    public const TYPE_DISTRIBUTOR = 'distributor';

    public const TYPE_FLEET = 'fleet';

    public const TYPE_EXPORT = 'export';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_RETAIL,
        self::TYPE_DISTRIBUTOR,
        self::TYPE_FLEET,
        self::TYPE_EXPORT,
    ];

    /** @var list<string> */
    public array $translatable = ['quote', 'author_role'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
