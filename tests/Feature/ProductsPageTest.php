<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductComponent;
use App\Models\ProductDocument;
use App\Models\ProductFaq;
use App\Models\ProductSpec;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_hub_lists_active_products_only(): void
    {
        $active = Product::factory()->create(['name' => ['en' => 'Inverter Batteries']]);
        $inactive = Product::factory()->inactive()->create(['name' => ['en' => 'Retired Line']]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Inverter Batteries')
            ->assertDontSee('Retired Line');
    }

    public function test_the_hub_renders_an_honest_empty_state(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Nothing published here yet.', false);
    }

    public function test_a_category_page_shows_only_that_categorys_products(): void
    {
        $lithium = ProductCategory::create(['slug' => 'lithium', 'name' => ['en' => 'Lithium'], 'is_active' => true, 'sort_order' => 0]);
        $leadAcid = ProductCategory::create(['slug' => 'lead-acid', 'name' => ['en' => 'Lead-Acid'], 'is_active' => true, 'sort_order' => 1]);

        $inCategory = Product::factory()->create(['product_category_id' => $lithium->id, 'name' => ['en' => 'Lithium Line']]);
        $other = Product::factory()->create(['product_category_id' => $leadAcid->id, 'name' => ['en' => 'Lead-Acid Line']]);

        $this->get(route('products.category', $lithium))
            ->assertOk()
            ->assertSee('Lithium Line')
            ->assertDontSee('Lead-Acid Line');
    }

    public function test_an_inactive_category_page_404s(): void
    {
        $category = ProductCategory::create(['slug' => 'discontinued', 'name' => ['en' => 'Discontinued'], 'is_active' => false, 'sort_order' => 0]);

        $this->get(route('products.category', $category))->assertNotFound();
    }

    public function test_the_detail_page_renders_specs_variants_faqs_and_documents(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create(['name' => ['en' => 'Inverter Batteries'], 'description' => ['en' => 'A long-backup battery.']]);

        ProductSpec::create(['product_id' => $product->id, 'label' => ['en' => 'Capacity'], 'value' => ['en' => '150 Ah'], 'sort_order' => 0]);
        ProductVariant::create(['product_id' => $product->id, 'model_code' => 'RX-150', 'capacity_ah' => 150, 'voltage' => 12, 'is_active' => true, 'sort_order' => 0]);
        ProductFaq::create(['product_id' => $product->id, 'question' => ['en' => 'How long is the warranty?'], 'answer' => ['en' => '24 months.'], 'is_active' => true, 'sort_order' => 0]);
        ProductDocument::create([
            'product_id' => $product->id,
            'title' => ['en' => 'Datasheet PDF'],
            'type' => ProductDocument::TYPE_DATASHEET,
            'disk' => 'public',
            'path' => 'product-documents/sample.pdf',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('products.show', $product))->assertOk();

        $response->assertSee('A long-backup battery.');
        $response->assertSee('150 Ah');
        $response->assertSee('RX-150');
        $response->assertSee('How long is the warranty?');
        $response->assertSee('Datasheet PDF');
    }

    public function test_an_inactive_product_404s(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_the_solar_system_kind_shows_its_bundled_components(): void
    {
        $system = Product::factory()->solarSystem()->create(['name' => ['en' => 'Solar Power System']]);

        ProductComponent::create([
            'product_id' => $system->id,
            'type' => 'panel',
            'name' => ['en' => 'Solar Panel'],
            'description' => ['en' => 'Mono PERC module.'],
            'sort_order' => 0,
        ]);

        $this->get(route('products.show', $system))
            ->assertOk()
            ->assertSee('Solar Panel')
            ->assertSee('Mono PERC module.');
    }

    public function test_a_plain_battery_line_does_not_show_the_bundled_system_section(): void
    {
        $product = Product::factory()->create();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertDontSee('The complete system');
    }

    public function test_downloading_a_document_increments_its_count_and_redirects(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('product-documents/sample.pdf', 'fake pdf');

        $product = Product::factory()->create();
        $document = ProductDocument::create([
            'product_id' => $product->id,
            'title' => ['en' => 'Datasheet'],
            'type' => ProductDocument::TYPE_DATASHEET,
            'disk' => 'public',
            'path' => 'product-documents/sample.pdf',
            'is_active' => true,
            'sort_order' => 0,
            'download_count' => 0,
        ]);

        $this->get(route('products.documents.download', [$product, $document]))->assertRedirect();

        $this->assertSame(1, $document->fresh()->download_count);
    }

    public function test_a_document_belonging_to_a_different_product_404s(): void
    {
        $product = Product::factory()->create();
        $otherProduct = Product::factory()->create();
        $document = ProductDocument::create([
            'product_id' => $otherProduct->id,
            'title' => ['en' => 'Datasheet'],
            'type' => ProductDocument::TYPE_DATASHEET,
            'disk' => 'public',
            'path' => 'product-documents/sample.pdf',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->get(route('products.documents.download', [$product, $document]))->assertNotFound();
    }
}
