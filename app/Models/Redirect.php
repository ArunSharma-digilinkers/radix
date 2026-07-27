<?php

namespace App\Models;

use App\Models\Concerns\Activatable;
use Illuminate\Database\Eloquent\Model;

/**
 * One old URL to its clean replacement.
 *
 * The current site uses query-string URLs like ?row=31 (brief §6). Each one needs
 * a 301 at cutover or its accumulated search ranking is discarded. Storing them
 * in a table rather than a config array means the team can add redirects as
 * stragglers turn up in Search Console, without a deploy.
 */
class Redirect extends Model
{
    use Activatable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'hits' => 'integer',
            'last_hit_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Records that a redirect fired. Useful for spotting which legacy URLs still
     * carry traffic, and which rules can eventually be retired.
     */
    public function recordHit(): void
    {
        $this->newQuery()
            ->whereKey($this->getKey())
            ->update(['hits' => $this->getRawOriginal('hits') + 1, 'last_hit_at' => now()]);
    }
}
