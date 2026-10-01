<?php

namespace Tests\Feature;

use App\Models\Dealer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public dealer locator. The line these tests defend is that a visitor
 * searching by city, state or PIN only ever sees active, real dealer
 * records — never an inactive listing, and never a placeholder.
 */
class DealerLocatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_index_lists_active_dealers_only(): void
    {
        $active = Dealer::factory()->create(['name' => 'Kanpur Battery House']);
        $inactive = Dealer::factory()->create(['name' => 'Retired Battery House', 'is_active' => false]);

        $this->get(route('dealers.index'))
            ->assertOk()
            ->assertSee($active->name)
            ->assertDontSee($inactive->name);
    }

    public function test_the_index_renders_an_empty_state_rather_than_placeholder_dealers(): void
    {
        $this->get(route('dealers.index'))
            ->assertOk()
            ->assertSee('No dealers found.');
    }

    public function test_searching_by_city_returns_only_matching_dealers(): void
    {
        $kanpur = Dealer::factory()->create(['name' => 'Kanpur Battery House', 'city' => 'Kanpur']);
        $lucknow = Dealer::factory()->create(['name' => 'Lucknow Battery House', 'city' => 'Lucknow']);

        $this->get(route('dealers.index', ['location' => 'Kanpur']))
            ->assertOk()
            ->assertSee($kanpur->name)
            ->assertDontSee($lucknow->name);
    }

    public function test_searching_by_pincode_returns_only_matching_dealers(): void
    {
        $match = Dealer::factory()->create(['name' => 'Pincode Match Store', 'pincode' => '208001']);
        $other = Dealer::factory()->create(['name' => 'Elsewhere Store', 'pincode' => '226001']);

        $this->get(route('dealers.index', ['location' => '208001']))
            ->assertOk()
            ->assertSee($match->name)
            ->assertDontSee($other->name);
    }

    public function test_filtering_by_type_returns_only_that_type(): void
    {
        $retail = Dealer::factory()->create(['name' => 'Retail Battery House', 'type' => Dealer::TYPE_RETAIL]);
        $distributor = Dealer::factory()->distributor()->create(['name' => 'Distributor Battery House']);

        $this->get(route('dealers.index', ['type' => Dealer::TYPE_DISTRIBUTOR]))
            ->assertOk()
            ->assertSee($distributor->name)
            ->assertDontSee($retail->name);
    }

    public function test_a_soft_deleted_dealer_is_not_listed(): void
    {
        $dealer = Dealer::factory()->create(['name' => 'Closed Battery House']);
        $dealer->delete();

        $this->get(route('dealers.index'))
            ->assertOk()
            ->assertDontSee($dealer->name);
    }

    public function test_an_invalid_type_is_rejected(): void
    {
        $this->get(route('dealers.index', ['type' => 'not-a-real-type']))
            ->assertSessionHasErrors('type');
    }
}
