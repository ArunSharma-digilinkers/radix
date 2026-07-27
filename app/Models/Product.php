<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * A product *line* — one of the eight in brief §4 — not an individual SKU.
 * Individual models live in {@see ProductVariant}.
 */
class Product extends Model
{
    use HasFactory, HasMedia, HasTranslations, Listable, SoftDeletes;

    /**
     * A plain battery line.
     */
    public const KIND_BATTERY = 'battery';

    /**
     * The Solar Power Generating System: panel + battery + inverter + charge
     * controller sold as one bundle. Brief §6 flags that this is missing from the
     * current site entirely and is a higher-value sale, so it renders differently
     * — each component explained, plus the system as a whole.
     */
    public const KIND_SOLAR_SYSTEM = 'solar_system';

    /** @var list<string> */
    public const KINDS = [self::KIND_BATTERY, self::KIND_SOLAR_SYSTEM];

    /** @var list<string> */
    public array $translatable = [
        'name', 'pitch', 'description', 'use_cases', 'meta_title', 'meta_description',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Products are addressed by slug, never by id — /products/solar-batteries,
     * not ?row=31 (brief §6).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function specs(): HasMany
    {
        return $this->hasMany(ProductSpec::class)->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class)->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProductDocument::class)->orderBy('sort_order');
    }

    /**
     * Populated only for {@see self::KIND_SOLAR_SYSTEM}.
     */
    public function components(): HasMany
    {
        return $this->hasMany(ProductComponent::class)->orderBy('sort_order');
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function datasheets(): HasMany
    {
        return $this->documents()->where('type', ProductDocument::TYPE_DATASHEET);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfKind(Builder $query, string $kind): Builder
    {
        return $query->where('kind', $kind);
    }

    public function isSolarSystem(): bool
    {
        return $this->kind === self::KIND_SOLAR_SYSTEM;
    }
}
