<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Product;
use App\Support\Content\ContactPageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_contact_page_renders(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Let', false);
    }

    public function test_it_has_exactly_one_top_level_heading(): void
    {
        $html = $this->get(route('contact'))->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
    }

    public function test_it_offers_every_enquiry_reason(): void
    {
        $response = $this->get(route('contact'))->assertOk();

        foreach (ContactPageContent::enquiryTypes() as $label) {
            $response->assertSee($label);
        }
    }

    /**
     * Same treatment as the homepage's WhatsApp button (CLAUDE.md §8 / brief
     * §11.5): an unconfirmed channel omits itself rather than render a dead
     * link, and the form becomes the primary path.
     */
    public function test_quick_contact_channels_appear_only_once_configured(): void
    {
        config(['radix.whatsapp' => null, 'radix.toll_free' => null]);
        $html = $this->get(route('contact'))->getContent();
        $this->assertStringNotContainsString('wa.me', $html);
        $this->assertStringContainsString('quickest way to reach us', $html);

        config(['radix.whatsapp' => '+91 98765 43210', 'radix.toll_free' => '1800 123 4567']);
        $html = $this->get(route('contact'))->getContent();
        $this->assertStringContainsString('wa.me/919876543210', $html);
        $this->assertStringContainsString('tel:1800123', $html);
    }

    public function test_fonts_are_not_loaded_from_a_third_party_cdn(): void
    {
        $html = $this->get(route('contact'))->getContent();

        $this->assertStringNotContainsString('fonts.googleapis.com', $html);
        $this->assertStringNotContainsString('fonts.gstatic.com', $html);
        $this->assertStringNotContainsString('fonts.bunny.net', $html);
    }

    public function test_the_form_requires_name_email_reason_and_message(): void
    {
        $this->post(route('contact.enquire'), [])
            ->assertSessionHasErrors(['name', 'email', 'type', 'message']);

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_a_valid_submission_is_stored_against_the_chosen_product(): void
    {
        $product = Product::factory()->create();

        $this->post(route('contact.enquire'), [
            'name' => 'Asha Verma',
            'email' => 'asha@example.com',
            'type' => Enquiry::TYPE_PRODUCT,
            'product' => $product->slug,
            'message' => 'Price for a 150Ah inverter battery?',
        ])->assertRedirect(route('contact').'#enquiry')->assertSessionHas('enquired');

        $enquiry = Enquiry::firstOrFail();
        $this->assertSame(Enquiry::TYPE_PRODUCT, $enquiry->type);
        $this->assertSame($product->id, $enquiry->product_id);
        $this->assertSame(Enquiry::STATUS_NEW, $enquiry->status);
    }

    public function test_an_unknown_product_or_reason_is_rejected(): void
    {
        $this->post(route('contact.enquire'), [
            'name' => 'A', 'email' => 'a@example.com', 'message' => 'Hi',
            'type' => 'bogus', 'product' => 'no-such-product',
        ])->assertSessionHasErrors(['type', 'product']);
    }

    public function test_the_product_query_param_preselects_the_dropdown(): void
    {
        $product = Product::factory()->create();

        $this->get(route('contact', ['product' => $product->slug]))
            ->assertOk()
            ->assertSee('value="'.$product->slug.'" selected', false);
    }
}
