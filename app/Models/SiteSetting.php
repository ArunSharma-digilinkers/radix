<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

/**
 * Editable key/value settings.
 *
 * This is where the figures that appear across the site live — 25+ years, 650+
 * distributors, 10L+ customers, contact numbers. Brief §6 traces the current
 * site's contradictory team-size claims to exactly the opposite approach, so no
 * template may hardcode these.
 */
class SiteSetting extends Model
{
    use HasTranslations;

    public const CAST_STRING = 'string';

    public const CAST_TEXT = 'text';

    public const CAST_HTML = 'html';

    public const CAST_NUMBER = 'number';

    public const CAST_BOOLEAN = 'boolean';

    public const CAST_JSON = 'json';

    /** @var list<string> */
    public const CASTS = [
        self::CAST_STRING,
        self::CAST_TEXT,
        self::CAST_HTML,
        self::CAST_NUMBER,
        self::CAST_BOOLEAN,
        self::CAST_JSON,
    ];

    private const CACHE_KEY = 'site_settings';

    /** @var list<string> */
    public array $translatable = ['value'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_translatable' => 'boolean'];
    }

    protected static function booted(): void
    {
        // Settings are read on nearly every request and written rarely.
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * @return array<string, mixed>
     */
    public static function all1D(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => static::query()->pluck('value', 'key')->all()
        );
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all1D()[$key] ?? $default;
    }
}
