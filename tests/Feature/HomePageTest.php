<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_the_home_page_renders(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Radix');
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
}
