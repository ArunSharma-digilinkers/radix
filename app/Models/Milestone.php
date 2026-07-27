<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/** One entry on the About page timeline (brief §4: 2001 to the 2025 lithium launch). */
class Milestone extends Model
{
    use HasTranslations, Listable;

    /** @var list<string> */
    public array $translatable = ['title', 'description'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['year' => 'integer', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function scopeChronological(Builder $query): Builder
    {
        return $query->orderBy('year')->orderBy('sort_order');
    }
}
