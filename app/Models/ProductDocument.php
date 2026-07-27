<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

/**
 * A downloadable file — datasheet, certificate, manual.
 *
 * Export buyers ask for these up front (brief §3), so download_count is tracked:
 * it tells us which specs buyers actually want before they enquire.
 */
class ProductDocument extends Model
{
    use HasTranslations, Listable;

    public const TYPE_DATASHEET = 'datasheet';

    public const TYPE_CERTIFICATE = 'certificate';

    public const TYPE_MANUAL = 'manual';

    public const TYPE_WARRANTY = 'warranty';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_DATASHEET,
        self::TYPE_CERTIFICATE,
        self::TYPE_MANUAL,
        self::TYPE_WARRANTY,
    ];

    /** @var list<string> */
    public array $translatable = ['title'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'download_count' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
