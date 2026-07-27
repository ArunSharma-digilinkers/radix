<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * An individual model within a product line, with the typed specs the brief's
 * comparison table needs: capacity, warranty, dimensions, weight.
 *
 * These columns are typed rather than free-form because the finder filters and
 * sorts on them. Anything that does not need sorting belongs in ProductSpec.
 */
class ProductVariant extends Model
{
    use HasTranslations, Listable, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['name'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'capacity_ah' => 'decimal:2',
            'voltage' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'warranty_months' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProductDocument::class);
    }

    /**
     * Warranty is quoted to customers in years but stored in months, because
     * some lines are warranted in 18- or 30-month terms.
     */
    public function warrantyInYears(): ?float
    {
        return $this->warranty_months ? round($this->warranty_months / 12, 1) : null;
    }
}
