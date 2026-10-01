<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Support\Content\HomePageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_about_page_renders(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('25+ years of power', false);
    }

    public function test_it_shows_the_shared_trust_figures(): void
    {
        $response = $this->get(route('about'))->assertOk();

        foreach (HomePageContent::stats() as $stat) {
            $response->assertSee($stat['value']);
        }
    }

    /**
     * "What we make" is real Product data (CLAUDE.md §8) — empty until the
     * admin adds lines.
     */
    public function test_it_shows_active_product_lines_and_links_to_their_pages(): void
    {
        $product = Product::factory()->create(['name' => ['en' => 'Inverter Batteries']]);
        $hidden = Product::factory()->inactive()->create(['name' => ['en' => 'Hidden Line']]);

        $response = $this->get(route('about'))->assertOk();

        $response->assertSee('Inverter Batteries');
        $response->assertSee(route('products.show', $product), false);
        $response->assertDontSee('Hidden Line');
    }

    public function test_the_product_index_is_hidden_when_nothing_is_published(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertDontSee('What we make');
    }

    public function test_it_has_exactly_one_top_level_heading(): void
    {
        $html = $this->get(route('about'))->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
    }

    /**
     * CLAUDE.md §8: headcount, company history detail and leadership names/
     * bios/photos are unverified and must not reach a production template.
     * The sections exist in the markup but stay silent until their content
     * methods return data.
     */
    public function test_milestones_and_leadership_are_hidden_while_unverified(): void
    {
        $html = $this->get(route('about'))->getContent();

        $this->assertStringNotContainsString('Milestones.', $html);
        $this->assertStringNotContainsString('Who runs Radix.', $html);
    }
}
