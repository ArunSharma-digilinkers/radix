<?php

namespace Tests\Feature\Models;

use App\Models\Dealer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealerTest extends TestCase
{
    use RefreshDatabase;

    /** Lucknow, roughly. */
    private const LUCKNOW = [26.8467, 80.9462];

    public function test_it_finds_the_nearest_dealers_first(): void
    {
        [$lat, $lng] = self::LUCKNOW;

        $faraway = Dealer::factory()->at($lat + 0.5, $lng + 0.5)->create(['name' => 'Far']);
        $nearby = Dealer::factory()->at($lat + 0.01, $lng + 0.01)->create(['name' => 'Near']);

        $results = Dealer::nearest($lat, $lng)->get();

        $this->assertSame(['Near', 'Far'], $results->pluck('name')->all());
        $this->assertLessThan(
            $results->firstWhere('name', 'Far')->distance_km,
            $results->firstWhere('name', 'Near')->distance_km
        );
    }

    public function test_it_excludes_dealers_beyond_the_radius(): void
    {
        [$lat, $lng] = self::LUCKNOW;

        Dealer::factory()->at($lat, $lng)->create(['name' => 'In range']);
        Dealer::factory()->at($lat + 5, $lng + 5)->create(['name' => 'Another state']);

        $names = Dealer::nearest($lat, $lng, withinKm: 50)->pluck('name')->all();

        $this->assertSame(['In range'], $names);
    }

    /**
     * The client's dealer list may arrive without coordinates (open question 3 in
     * the plan). Those records must not break the locator, and must still be
     * reachable by a city search.
     */
    public function test_dealers_without_coordinates_are_skipped_by_distance_but_found_by_name(): void
    {
        [$lat, $lng] = self::LUCKNOW;

        Dealer::factory()->withoutCoordinates()->create(['name' => 'Unmapped', 'city' => 'Kanpur']);
        Dealer::factory()->at($lat, $lng)->create(['name' => 'Mapped', 'city' => 'Lucknow']);

        $this->assertSame(['Mapped'], Dealer::nearest($lat, $lng)->pluck('name')->all());
        $this->assertSame(['Unmapped'], Dealer::search('Kanpur')->pluck('name')->all());
    }

    public function test_search_matches_city_state_and_pincode(): void
    {
        Dealer::factory()->create(['name' => 'A', 'city' => 'Lucknow', 'pincode' => '226001']);
        Dealer::factory()->create(['name' => 'B', 'city' => 'Kanpur', 'pincode' => '208001']);

        $this->assertSame(['A'], Dealer::search('Lucknow')->pluck('name')->all());
        $this->assertSame(['A'], Dealer::search('226')->pluck('name')->all());
        $this->assertCount(2, Dealer::search('Uttar Pradesh')->get());
    }

    public function test_an_empty_search_term_does_not_filter_anything_out(): void
    {
        Dealer::factory()->count(3)->create();

        $this->assertCount(3, Dealer::search('   ')->get());
        $this->assertCount(3, Dealer::search(null)->get());
    }

    public function test_inactive_dealers_stay_out_of_public_results(): void
    {
        Dealer::factory()->create(['name' => 'Open']);
        Dealer::factory()->create(['name' => 'Closed', 'is_active' => false]);

        $this->assertSame(['Open'], Dealer::active()->pluck('name')->all());
    }
}
