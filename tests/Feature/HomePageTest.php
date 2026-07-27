<?php

namespace Tests\Feature;

use App\Support\Content\HomePageContent;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_the_home_page_renders(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('The battery brand India', false);
    }

    public function test_it_shows_every_product_line_and_trust_figure(): void
    {
        $response = $this->get('/')->assertOk();

        foreach (HomePageContent::products() as $product) {
            $response->assertSee($product['name']);
        }

        foreach (HomePageContent::stats() as $stat) {
            $response->assertSee($stat['value']);
            $response->assertSee($stat['label']);
        }
    }

    /**
     * A page with several <h1>s, or one with none, reads as structureless to a
     * screen reader and muddies the SEO signal the brief asks us to fix (§6).
     */
    public function test_it_has_exactly_one_top_level_heading(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
    }

    /**
     * Type is served from our own origin, never a third-party CDN — a
     * render-blocking cross-origin request works against the load target
     * in the brief. Guards against someone "simplifying" the font setup
     * back to a Google Fonts <link>.
     */
    public function test_fonts_are_not_loaded_from_a_third_party_cdn(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('fonts.googleapis.com', $html);
        $this->assertStringNotContainsString('fonts.gstatic.com', $html);
        $this->assertStringNotContainsString('fonts.bunny.net', $html);
    }

    /**
     * The concept fetched d3, topojson and a world atlas from unpkg/jsdelivr at
     * runtime, inside an iframe, twice. The maps are pre-rendered instead; this
     * keeps them that way.
     */
    public function test_maps_are_inline_svg_rather_than_iframes_hitting_a_cdn(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringNotContainsString('unpkg.com', $html);
        $this->assertStringNotContainsString('cdn.jsdelivr.net', $html);
        $this->assertStringContainsString('Radix export markets', $html);
    }

    /**
     * The brief bans the auto-rotating carousel that the current site uses as its
     * hero (§5.4). The replacement is a muted looping clip.
     */
    public function test_the_hero_is_a_looping_muted_video_not_a_carousel(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertMatchesRegularExpression('/<video[^>]*\bmuted\b/', $html);
        $this->assertMatchesRegularExpression('/<video[^>]*\bloop\b/', $html);
    }

    public function test_it_offers_a_skip_link_before_the_navigation(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('Skip to content', $html);
        $this->assertLessThan(
            strpos($html, '<header'),
            strpos($html, 'Skip to content'),
            'The skip link must come before the header to be reachable on first tab.'
        );
    }

    /**
     * A WhatsApp number has not been confirmed by the client yet. Rather than ship
     * a placeholder, the button omits itself — and starts appearing the moment the
     * number is configured.
     */
    public function test_the_whatsapp_button_appears_only_once_a_number_is_configured(): void
    {
        config(['radix.whatsapp' => null]);
        $this->assertStringNotContainsString('wa.me', $this->get('/')->getContent());

        config(['radix.whatsapp' => '+91 98765 43210']);
        $this->assertStringContainsString('wa.me/919876543210', $this->get('/')->getContent());
    }
}
