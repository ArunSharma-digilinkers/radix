<?php

namespace App\Models;

use App\Models\Concerns\Activatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A stocked dealer, distributor or service centre.
 *
 * Powers the locator page the brief asks for as a new addition (§4): 650+
 * dealers are a major trust asset that is currently invisible on the site.
 */
class Dealer extends Model
{
    use Activatable, HasFactory, SoftDeletes;

    public const TYPE_RETAIL = 'retail';

    public const TYPE_DISTRIBUTOR = 'distributor';

    public const TYPE_SERVICE_CENTRE = 'service_centre';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_RETAIL,
        self::TYPE_DISTRIBUTOR,
        self::TYPE_SERVICE_CENTRE,
    ];

    /** Mean radius of the Earth in kilometres, for the distance calculation. */
    private const EARTH_RADIUS_KM = 6371;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Free-text search across the fields a visitor would actually type.
     *
     * Deliberately not a `LIKE %term%` over every column: a leading wildcard
     * cannot use an index, and this table will hold 650+ rows growing over time.
     * City, state and pincode are indexed and anchored.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('city', 'like', $term.'%')
                ->orWhere('state', 'like', $term.'%')
                ->orWhere('pincode', 'like', $term.'%')
                ->orWhere('name', 'like', '%'.$term.'%');
        });
    }

    /**
     * Orders by great-circle distance from a point, nearest first, and exposes
     * the distance as `distance_km`.
     *
     * A bounding box narrows the candidate set before the trigonometry runs, so
     * the expensive calculation only touches rows that could plausibly win.
     * Dealers without coordinates are excluded — they are still findable by city
     * through scopeSearch().
     *
     * Uses the haversine form (asin of a square root) rather than the spherical
     * law of cosines (acos of a dot product). Two reasons: acos needs its input
     * clamped to avoid a domain error when two points coincide, and the clamp
     * function differs between MySQL (`LEAST`) and SQLite, which the test suite
     * runs on. Haversine's argument cannot leave the valid range at these
     * distances, so the query is both portable and accurate for short spans.
     */
    public function scopeNearest(Builder $query, float $latitude, float $longitude, int $withinKm = 100): Builder
    {
        $latitudeDelta = $withinKm / 111.0;
        $longitudeDelta = $withinKm / max(1.0, 111.0 * cos(deg2rad($latitude)));

        $haversine = '(2 * ? * asin(sqrt('
            .'sin(radians(latitude - ?) / 2) * sin(radians(latitude - ?) / 2)'
            .' + cos(radians(?)) * cos(radians(latitude))'
            .' * sin(radians(longitude - ?) / 2) * sin(radians(longitude - ?) / 2)'
            .')))';

        $bindings = [self::EARTH_RADIUS_KM, $latitude, $latitude, $latitude, $longitude, $longitude];

        return $query
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$latitude - $latitudeDelta, $latitude + $latitudeDelta])
            ->whereBetween('longitude', [$longitude - $longitudeDelta, $longitude + $longitudeDelta])
            ->selectRaw('*, '.$haversine.' as distance_km', $bindings)
            // Repeated in WHERE rather than filtered with HAVING: MySQL accepts a
            // HAVING on a select alias without GROUP BY, SQLite rejects it, and the
            // test suite runs on SQLite. Same row-by-row cost either way.
            ->whereRaw($haversine.' <= ?', [...$bindings, $withinKm])
            ->orderBy('distance_km');
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}
