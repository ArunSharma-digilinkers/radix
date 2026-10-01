<?php

namespace Tests\Feature;

use App\Support\Content\ContactPageContent;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
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
}
