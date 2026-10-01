<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_is_valid_xml_listing_the_static_pages(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);

        $locs = collect();
        foreach ($xml->url as $url) {
            $locs->push((string) $url->loc);
        }
        $this->assertTrue($locs->contains(route('home')));
        $this->assertTrue($locs->contains(route('products.index')));
        $this->assertTrue($locs->contains(route('dealers.index')));
    }

    public function test_it_lists_active_products_but_not_hidden_ones(): void
    {
        $shown = Product::factory()->create(['is_active' => true]);
        $hidden = Product::factory()->create(['is_active' => false]);

        $body = $this->get('/sitemap.xml')->getContent();

        $this->assertStringContainsString(route('products.show', $shown), $body);
        $this->assertStringNotContainsString(route('products.show', $hidden), $body);
    }

    public function test_it_lists_published_posts_but_not_drafts(): void
    {
        $live = Post::factory()->create(['published_at' => now()->subDay()]);
        $draft = Post::factory()->create(['published_at' => null]);

        $body = $this->get('/sitemap.xml')->getContent();

        $this->assertStringContainsString(route('blog.show', $live), $body);
        $this->assertStringNotContainsString(route('blog.show', $draft), $body);
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /', false);
    }

    public function test_robots_points_production_crawlers_at_the_sitemap(): void
    {
        $this->app['env'] = 'production';

        $this->get('/robots.txt')
            ->assertSee('Sitemap: '.route('sitemap'), false)
            ->assertSee('Disallow: /admin', false);
    }
}
