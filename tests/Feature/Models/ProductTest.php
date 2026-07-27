<?php

namespace Tests\Feature\Models;

use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductDocument;
use App\Models\ProductFaq;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_is_addressed_by_slug_not_id(): void
    {
        $product = Product::factory()->create(['slug' => 'solar-batteries']);

        $this->assertSame('slug', $product->getRouteKeyName());
        $this->assertSame('solar-batteries', $product->getRouteKey());
    }

    /**
     * Content columns hold a locale map. English ships now; Hindi is added later
     * without a migration, which is the whole reason for the JSON column.
     */
    public function test_translatable_fields_resolve_per_locale(): void
    {
        $product = Product::factory()->create(['name' => ['en' => 'Inverter Batteries']]);

        $product->setTranslation('name', 'hi', 'इन्वर्टर बैटरी')->save();

        $this->assertSame('Inverter Batteries', $product->fresh()->name);

        app()->setLocale('hi');
        $this->assertSame('इन्वर्टर बैटरी', $product->fresh()->name);
    }

    public function test_it_falls_back_to_english_when_a_translation_is_missing(): void
    {
        $product = Product::factory()->create(['name' => ['en' => 'Bike Batteries']]);

        app()->setLocale('hi');

        $this->assertSame('Bike Batteries', $product->fresh()->name);
    }

    public function test_for_display_returns_only_active_products_in_order(): void
    {
        Product::factory()->create(['sort_order' => 2, 'slug' => 'second']);
        Product::factory()->create(['sort_order' => 1, 'slug' => 'first']);
        Product::factory()->inactive()->create(['sort_order' => 0, 'slug' => 'hidden']);

        $slugs = Product::forDisplay()->pluck('slug')->all();

        $this->assertSame(['first', 'second'], $slugs);
    }

    public function test_a_solar_system_carries_its_bundled_components(): void
    {
        $system = Product::factory()->solarSystem()->create();

        foreach (ProductComponent::TYPES as $index => $type) {
            $system->components()->create([
                'type' => $type,
                'name' => ['en' => ucfirst(str_replace('_', ' ', $type))],
                'sort_order' => $index,
            ]);
        }

        $this->assertTrue($system->isSolarSystem());
        $this->assertCount(4, $system->components);
        $this->assertSame(ProductComponent::TYPES, $system->components->pluck('type')->all());
    }

    public function test_a_battery_line_is_not_a_solar_system(): void
    {
        $this->assertFalse(Product::factory()->create()->isSolarSystem());
    }

    public function test_datasheets_are_filtered_out_of_the_wider_document_list(): void
    {
        $product = Product::factory()->create();

        $product->documents()->create([
            'title' => ['en' => 'Datasheet'],
            'type' => ProductDocument::TYPE_DATASHEET,
            'path' => 'docs/datasheet.pdf',
        ]);
        $product->documents()->create([
            'title' => ['en' => 'Warranty card'],
            'type' => ProductDocument::TYPE_WARRANTY,
            'path' => 'docs/warranty.pdf',
        ]);

        $this->assertCount(2, $product->documents);
        $this->assertCount(1, $product->datasheets);
    }

    public function test_variants_and_faqs_are_removed_with_their_product(): void
    {
        $product = Product::factory()->create();

        $product->variants()->create(['model_code' => 'RX-150']);
        $product->faqs()->create(['question' => ['en' => 'Q?'], 'answer' => ['en' => 'A.']]);

        // A force delete cascades; an ordinary soft delete must not.
        $product->delete();
        $this->assertSame(1, ProductVariant::count());

        $product->forceDelete();
        $this->assertSame(0, ProductVariant::count());
        $this->assertSame(0, ProductFaq::count());
    }

    public function test_warranty_is_stored_in_months_and_read_in_years(): void
    {
        $variant = Product::factory()
            ->create()
            ->variants()
            ->create(['model_code' => 'RX-200', 'warranty_months' => 18]);

        $this->assertSame(1.5, $variant->warrantyInYears());
    }
}
