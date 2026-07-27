<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * A country Radix exports to.
 *
 * `iso_numeric` is the join to the homepage map: scripts/build-maps.mjs
 * highlights countries by the same ISO 3166-1 numeric code. Adding a market here
 * therefore also means adding its code to that script's SERVED list and running
 * `npm run build:maps` — the map is baked at build time and will not notice a new
 * database row on its own.
 */
class ExportMarket extends Model
{
    use HasTranslations, Listable;

    /** @var list<string> */
    public array $translatable = ['country_name', 'blurb'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'iso_numeric' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
