<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class Media extends Model
{
    use HasTranslations;

    public const COLLECTION_MAIN = 'main';

    public const COLLECTION_GALLERY = 'gallery';

    public const COLLECTION_FACTORY = 'factory';

    public const COLLECTION_CERTIFICATE = 'certificate';

    public const COLLECTIONS = [
        self::COLLECTION_MAIN,
        self::COLLECTION_GALLERY,
        self::COLLECTION_FACTORY,
        self::COLLECTION_CERTIFICATE,
    ];

    /** @var list<string> */
    public array $translatable = ['alt', 'caption'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Alt text is an accessibility requirement (brief §7), but an image that is
     * purely decorative should carry an empty alt rather than a description —
     * so this returns '' rather than a filename when none is set.
     */
    public function altText(): string
    {
        return $this->alt ?? '';
    }
}
