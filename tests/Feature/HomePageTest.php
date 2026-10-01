<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Product;
use App\Models\Testimonial;
use App\Support\Content\HomePageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_renders(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('The battery brand India', false);
    }

    public function test_it_shows_the_trust_figures(): void
    {
        $response = $this->get('/')->assertOk();

        foreach (HomePageContent::stats() as $stat) {
            $response->assertSee($stat['value']);
            $response->assertSee($stat['label']);
        }
    }

    /**
     * The product index is real Product data (CLAUDE.md §8) — empty until
     * the admin adds lines, same as the blog strip.
     */
    public function test_the_product_index_shows_active_products_and_links_to_their_pages(): void
    {
        $product = Product::factory()->create(['name' => ['en' => 'Inverter Batteries']]);
        $hidden = Product::factory()->inactive()->create(['name' => ['en' => 'Hidden Line']]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Inverter Batteries')
            ->assertSee(route('products.show', $product), false)
            ->assertDontSee('Hidden Line');
    }

    public function test_the_product_index_disappears_when_nothing_is_published(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Eight lines of power.');
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

    /**
     * The homepage blog strip is a view onto the real blog, not a copy of it:
     * whatever it shows must exist at /blog, or the homepage advertises articles
     * that 404 (CLAUDE.md §8).
     */
    public function test_the_blog_strip_shows_published_posts_and_links_to_them(): void
    {
        $post = Post::factory()->create(['title' => ['en' => 'A published post']]);

        $this->get('/')
            ->assertOk()
            ->assertSee($post->getTranslation('title', 'en'))
            ->assertSee(route('blog.show', $post), false);
    }

    public function test_the_blog_strip_hides_drafts_and_scheduled_posts(): void
    {
        $draft = Post::factory()->draft()->create(['title' => ['en' => 'A draft post']]);
        $scheduled = Post::factory()->scheduled()->create(['title' => ['en' => 'A scheduled post']]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee($draft->getTranslation('title', 'en'))
            ->assertDontSee($scheduled->getTranslation('title', 'en'));
    }

    public function test_the_blog_strip_shows_the_three_newest_posts(): void
    {
        $older = Post::factory()->create([
            'title' => ['en' => 'The fourth newest post'],
            'published_at' => now()->subYear(),
        ]);

        Post::factory()->count(3)->create(['published_at' => now()->subDay()]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee($older->getTranslation('title', 'en'));
    }

    /**
     * With nothing published the section drops out rather than rendering a
     * heading over an empty grid.
     */
    public function test_the_blog_strip_disappears_when_nothing_is_published(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('From the Radix blog');
    }

    /**
     * Testimonials are named quotes (CLAUDE.md §8) — the section must show a
     * real App\Models\Testimonial or nothing, never an invented one.
     */
    public function test_the_testimonials_section_disappears_when_none_are_published(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('<blockquote', $html);
    }

    public function test_an_active_testimonial_is_shown_with_its_real_name(): void
    {
        Testimonial::factory()->create([
            'quote' => ['en' => 'Radix batteries move fast off my shelf.'],
            'author_name' => 'Rakesh Verma',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Radix batteries move fast off my shelf.')
            ->assertSee('Rakesh Verma');
    }

    public function test_an_inactive_testimonial_is_not_shown(): void
    {
        Testimonial::factory()->create(['author_name' => 'Hidden Author', 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Hidden Author');
    }

    public function test_the_featured_testimonial_appears_in_the_large_quote_slot(): void
    {
        Testimonial::factory()->create(['author_name' => 'Regular Quote']);
        Testimonial::factory()->featured()->create(['author_name' => 'Featured Quote']);

        $html = $this->get('/')->getContent();

        // The featured author's name appears before the regular one's — the
        // large pull-quote is always the first one in the markup.
        $this->assertLessThan(strpos($html, 'Regular Quote'), strpos($html, 'Featured Quote'));
    }
}
