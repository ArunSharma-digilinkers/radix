<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A captured lead — the site's primary conversion (brief §2).
 *
 * Soft deletes only. A lead removed by mistake is lost revenue, and the sales
 * role needs a trail of what came in and when.
 */
class Enquiry extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_PRODUCT = 'product';

    public const TYPE_DEALER = 'dealer';

    public const TYPE_EXPORT = 'export';

    public const TYPE_CAREER = 'career';

    public const TYPE_GENERAL = 'general';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_PRODUCT,
        self::TYPE_DEALER,
        self::TYPE_EXPORT,
        self::TYPE_CAREER,
        self::TYPE_GENERAL,
    ];

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_QUALIFIED = 'qualified';

    public const STATUS_WON = 'won';

    public const STATUS_LOST = 'lost';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_CONTACTED,
        self::STATUS_QUALIFIED,
        self::STATUS_WON,
        self::STATUS_LOST,
    ];

    protected $guarded = ['id'];

    /**
     * Request metadata is kept for spam triage, not for display.
     *
     * @var list<string>
     */
    protected $hidden = ['ip_address', 'user_agent', 'internal_notes'];

    protected function casts(): array
    {
        return ['responded_at' => 'datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** Anything not yet won or lost — the working inbox. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_WON, self::STATUS_LOST]);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeAwaitingResponse(Builder $query): Builder
    {
        return $query->whereNull('responded_at');
    }

    /**
     * The brief promises a reply within one business day, so "overdue" is a
     * thing the inbox needs to be able to show.
     */
    public function scopeOverdue(Builder $query, int $hours = 24): Builder
    {
        return $query->awaitingResponse()->where('created_at', '<', now()->subHours($hours));
    }

    public function markResponded(): void
    {
        $this->forceFill([
            'responded_at' => now(),
            'status' => $this->status === self::STATUS_NEW ? self::STATUS_CONTACTED : $this->status,
        ])->save();
    }
}
