<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * For models with an `is_active` flag but no manual ordering — dealers are
 * ordered by distance or name, redirects not at all.
 *
 * Kept separate from {@see Listable} so that `ordered()` is only ever available
 * on tables that actually have a `sort_order` column.
 */
trait Activatable
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
