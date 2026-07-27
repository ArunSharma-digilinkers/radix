<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/** "Request a call back" widget (brief §7, lead capture). */
class CallbackRequest extends Model
{
    use SoftDeletes;

    public const STATUS_NEW = 'new';

    public const STATUS_CALLED = 'called';

    public const STATUS_UNREACHABLE = 'unreachable';

    public const STATUS_CLOSED = 'closed';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_CALLED,
        self::STATUS_UNREACHABLE,
        self::STATUS_CLOSED,
    ];

    protected $guarded = ['id'];

    /** @var list<string> */
    protected $hidden = ['ip_address', 'internal_notes'];
}
