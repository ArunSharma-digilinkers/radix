<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * Leadership and team profiles for the About page.
 *
 * Blocked on client input: current photos and bios are on the §11.2 list, and
 * the existing site's headcount contradicts itself. Nothing goes in here until
 * Radix confirms it.
 */
class TeamMember extends Model
{
    use HasMedia, HasTranslations, Listable, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['role', 'bio'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_leadership' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeLeadership(Builder $query): Builder
    {
        return $query->where('is_leadership', true);
    }
}
