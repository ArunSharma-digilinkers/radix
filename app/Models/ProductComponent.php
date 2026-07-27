<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * One part of a bundled Solar Power Generating System.
 *
 * Brief §4 asks that the solar page present panel + battery + inverter + charge
 * controller as a single solution, with each component also explained on its own.
 */
class ProductComponent extends Model
{
    use HasTranslations;

    public const TYPE_PANEL = 'panel';

    public const TYPE_BATTERY = 'battery';

    public const TYPE_INVERTER = 'inverter';

    public const TYPE_CHARGE_CONTROLLER = 'charge_controller';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_PANEL,
        self::TYPE_BATTERY,
        self::TYPE_INVERTER,
        self::TYPE_CHARGE_CONTROLLER,
    ];

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['specs' => 'array', 'sort_order' => 'integer'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
