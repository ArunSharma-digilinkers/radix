<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * Groups product lines for the hub filters and the battery finder — e.g.
 * lead-acid vs lithium vs solar. Not the same thing as a product line.
 */
class ProductCategory extends Model
{
    use HasTranslations, Listable, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
