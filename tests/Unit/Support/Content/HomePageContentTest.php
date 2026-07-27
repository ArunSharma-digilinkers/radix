<?php

namespace Tests\Unit\Support\Content;

use App\Support\Content\HomePageContent;
use PHPUnit\Framework\TestCase;

/**
 * These assertions pin the shape of the Phase 1 content scaffold.
 *
 * Phase 4 replaces the class body with database queries. The templates only ever
 * see these array keys, so as long as these tests still pass after the swap, the
 * homepage markup does not need to change — which is the reason for building
 * against this seam in the first place.
 */
class HomePageContentTest extends TestCase
{
    public function test_it_lists_the_eight_product_lines_from_the_brief(): void
    {
        $products = HomePageContent::products();

        $this->assertCount(8, $products, 'The brief specifies exactly eight product lines.');

        foreach ($products as $product) {
            $this->assertSame(['number', 'name', 'pitch', 'image'], array_keys($product));
        }

        $this->assertContains(
            'Solar Power Systems',
            array_column($products, 'name'),
            'The bundled solar system is a priority line and must appear on the homepage.'
        );
    }

    public function test_the_trust_stats_match_the_figures_stated_in_the_brief(): void
    {
        $stats = collect(HomePageContent::stats())->pluck('value', 'label')->all();

        $this->assertSame('25+', $stats['Years manufacturing']);
        $this->assertSame('650+', $stats['Distributors']);
        $this->assertSame('10L+', $stats['Customers served']);
        $this->assertSame('5+', $stats['Export countries']);
    }

    public function test_the_solar_system_is_broken_into_its_four_components(): void
    {
        $components = array_column(HomePageContent::solarComponents(), 'title');

        $this->assertSame(
            ['Solar Panel', 'Solar Battery', 'Solar Inverter', 'Charge Controller'],
            $components
        );
    }

    /**
     * The world map highlights a fixed list of countries baked in at build time by
     * scripts/build-maps.mjs. If the copy and the map disagree, one of them is
     * wrong — and the map is the one nobody thinks to update.
     */
    public function test_export_markets_match_the_countries_highlighted_on_the_map(): void
    {
        $script = file_get_contents(__DIR__.'/../../../../scripts/build-maps.mjs');

        preg_match_all("/\{ name: '([^']+)'/", $script, $matches);

        $onMap = array_values(array_diff($matches[1], ['India']));

        $this->assertSame(HomePageContent::exportMarkets(), $onMap);
    }

    public function test_every_post_has_the_metadata_a_card_needs(): void
    {
        foreach (HomePageContent::posts() as $post) {
            $this->assertNotEmpty($post['category']);
            $this->assertNotEmpty($post['title']);
            $this->assertNotEmpty($post['meta']);
            $this->assertNotEmpty($post['image']);
        }
    }
}
