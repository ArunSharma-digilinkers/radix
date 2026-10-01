<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\ExportMarket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_renders(): void
    {
        $this->get(route('export.index'))
            ->assertOk()
            ->assertSee('Trusted', false)
            ->assertSee('across borders', false);
    }

    public function test_it_lists_active_markets_only(): void
    {
        $active = ExportMarket::factory()->create(['country_name' => ['en' => 'Nigeria']]);
        $inactive = ExportMarket::factory()->create(['country_name' => ['en' => 'Hidden Market'], 'is_active' => false]);

        $response = $this->get(route('export.index'))->assertOk();

        $response->assertSee('Nigeria');
        $response->assertDontSee('Hidden Market');
    }

    public function test_it_shows_an_honest_message_when_no_market_details_exist_yet(): void
    {
        $this->get(route('export.index'))
            ->assertOk()
            ->assertSee('Detailed notes on each market are on their way', false);
    }

    public function test_a_markets_blurb_is_shown_once_added(): void
    {
        ExportMarket::factory()->create([
            'country_name' => ['en' => 'Nigeria'],
            'blurb' => ['en' => 'A growing distributor network across West Africa.'],
        ]);

        $this->get(route('export.index'))
            ->assertOk()
            ->assertSee('A growing distributor network across West Africa.');
    }

    public function test_the_map_carries_data_iso_hooks_for_the_interactive_highlight(): void
    {
        $this->get(route('export.index'))
            ->assertOk()
            ->assertSee('data-iso="566"', false);
    }

    public function test_the_logistics_overview_renders(): void
    {
        $this->get(route('export.index'))
            ->assertOk()
            ->assertSee('Enquiry &amp; quotation', false);
    }

    public function test_the_partner_form_requires_name_email_and_message(): void
    {
        $response = $this->post(route('export.enquire'), []);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_a_valid_submission_creates_an_export_enquiry(): void
    {
        $response = $this->post(route('export.enquire'), [
            'name' => 'Femi Adeyemi',
            'company' => 'Adeyemi Distributors Ltd',
            'email' => 'femi@example.com',
            'phone' => '+2348012345678',
            'message' => 'Interested in inverter batteries, 500 units/month, Lagos.',
        ]);

        $response->assertRedirect(route('export.index').'#enquire');

        $enquiry = Enquiry::first();
        $this->assertNotNull($enquiry);
        $this->assertSame(Enquiry::TYPE_EXPORT, $enquiry->type);
        $this->assertSame('Femi Adeyemi', $enquiry->name);
        $this->assertSame('Adeyemi Distributors Ltd', $enquiry->company);
        $this->assertSame(Enquiry::STATUS_NEW, $enquiry->status);
    }

    public function test_the_confirmation_shows_after_a_successful_submission(): void
    {
        $this->followingRedirects()->post(route('export.enquire'), [
            'name' => 'Femi Adeyemi',
            'email' => 'femi@example.com',
            'message' => 'Interested in a partnership.',
        ])->assertSee('Enquiry received.');
    }
}
