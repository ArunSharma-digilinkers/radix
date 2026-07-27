<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes shared by content models that are both toggleable and manually ordered.
 *
 * Requires `is_active` and `sort_order` columns. A model with only the former
 * should use {@see Activatable} instead — otherwise `ordered()` compiles a query
 * against a column that does not exist.
 */
trait Listable
{
    use Activatable;

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * What the public site should show: active, in the intended order.
     */
    public function scopeForDisplay(Builder $query): Builder
    {
        return $query->active()->ordered();
    }
}
