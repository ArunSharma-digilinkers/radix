<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Products\Form as ProductForm;
use App\Livewire\Admin\Products\Index as ProductsIndex;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Replicates Phase 3's stated exit criterion: "a content-editor can create a
 * product with specs, images, FAQs and a datasheet end-to-end without
 * touching code or the database."
 */
class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_a_content_editor_can_create_a_product_end_to_end(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('content-editor');

        $category = ProductCategory::create([
            'slug' => 'inverter-batteries',
            'name' => ['en' => 'Inverter Batteries'],
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($editor);

        Livewire::test(ProductForm::class)
            ->set('name', 'Test Inverter Battery')
            ->set('kind', Product::KIND_BATTERY)
            ->set('categoryId', (string) $category->id)
            ->set('newImage', UploadedFile::fake()->image('battery.jpg'))
            ->set('imageAlt', 'A Radix inverter battery')
            ->set('variants', [
                ['id' => null, 'model_code' => 'RX-100', 'name' => 'RX 100Ah', 'capacity_ah' => '100', 'voltage' => '12', 'warranty_months' => '24', 'dimensions_mm' => '500x200x220', 'weight_kg' => '28', 'is_active' => true],
            ])
            ->set('specs', [
                ['id' => null, 'label' => 'Chemistry', 'value' => 'Tubular', 'group' => ''],
            ])
            ->set('faqs', [
                ['id' => null, 'question' => 'How long does it last?', 'answer' => '5+ years with proper maintenance.', 'is_active' => true],
            ])
            ->set('documents', [
                ['id' => null, 'title' => 'Datasheet', 'type' => 'datasheet', 'existing_path' => null],
            ])
            ->set('documentFiles', [
                UploadedFile::fake()->create('datasheet.pdf', 200, 'application/pdf'),
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.products.index'));

        $product = Product::first();

        $this->assertNotNull($product);
        $this->assertSame('Test Inverter Battery', $product->getTranslation('name', 'en'));
        $this->assertSame('test-inverter-battery', $product->slug);
        $this->assertSame($category->id, $product->product_category_id);

        $this->assertCount(1, $product->variants);
        $this->assertSame('RX-100', $product->variants->first()->model_code);

        $this->assertCount(1, $product->specs);
        $this->assertCount(1, $product->faqs);
        $this->assertCount(1, $product->documents);

        $this->assertNotNull($product->image);
        $this->assertSame('A Radix inverter battery', $product->image->altText());

        Storage::disk('public')->assertExists($product->documents->first()->path);
    }

    public function test_sales_cannot_reach_product_crud(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.products.index'))->assertForbidden();
    }

    public function test_content_editor_cannot_reach_the_enquiry_inbox(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('content-editor');

        $this->actingAs($editor)->get(route('admin.enquiries.index'))->assertForbidden();
    }

    public function test_a_product_can_be_soft_deleted_and_restored(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('content-editor');
        $product = Product::factory()->create();

        $this->actingAs($editor);

        Livewire::test(ProductsIndex::class)->call('delete', $product->id);
        $this->assertSoftDeleted($product);

        Livewire::test(ProductsIndex::class)->set('showTrashed', true)->call('restore', $product->id);
        $this->assertNotSoftDeleted($product->fresh());
    }

    public function test_a_product_requires_alt_text_for_its_main_image(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('content-editor');
        $this->actingAs($editor);

        Livewire::test(ProductForm::class)
            ->set('name', 'No Alt Battery')
            ->set('newImage', UploadedFile::fake()->image('battery.jpg'))
            ->set('imageAlt', '')
            ->call('save')
            ->assertHasErrors(['imageAlt']);
    }
}
