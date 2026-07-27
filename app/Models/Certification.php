<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * ISO, BIS and similar. Brief §6 notes none are visible on the current site,
 * despite being exactly what export buyers look for.
 *
 * The certificate image attaches through HasMedia.
 */
class Certification extends Model
{
    use HasFactory, HasMedia, HasTranslations, Listable;

    /** @var list<string> */
    public array $translatable = ['name'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'expires_on' => 'date',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * An expired certificate on a manufacturer's site is worse than none, so
     * expiry is filterable and surfaced in the admin.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('expires_on')->orWhere('expires_on', '>=', now()->toDateString());
        });
    }

    public function hasExpired(): bool
    {
        return $this->expires_on !== null && $this->expires_on->isPast();
    }
}
