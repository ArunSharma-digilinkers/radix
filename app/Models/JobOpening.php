<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class JobOpening extends Model
{
    use HasTranslations, SoftDeletes;

    public const TYPE_FULL_TIME = 'full_time';

    public const TYPE_PART_TIME = 'part_time';

    public const TYPE_CONTRACT = 'contract';

    public const TYPE_INTERNSHIP = 'internship';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_FULL_TIME,
        self::TYPE_PART_TIME,
        self::TYPE_CONTRACT,
        self::TYPE_INTERNSHIP,
    ];

    /** @var list<string> */
    public array $translatable = ['title', 'description', 'requirements'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'closes_on' => 'date'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Open means published and not past its closing date. A role that closed
     * yesterday should stop collecting applications on its own — nobody
     * remembers to unpublish.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function (Builder $q) {
                $q->whereNull('closes_on')->orWhere('closes_on', '>=', now()->toDateString());
            });
    }
}
