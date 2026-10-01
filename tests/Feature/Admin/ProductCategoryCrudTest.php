<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Products\Categories\Form as CategoryForm;
use App\Models\ProductCategory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_a_content_editor_can_create_a_category(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('content-editor');

        Livewire::actingAs($editor)
            ->test(CategoryForm::class)
            ->set('name', 'Lithium')
            ->call('save')
            ->assertHasNoErrors();

        $category = ProductCategory::first();
        $this->assertNotNull($category);
        $this->assertSame('Lithium', $category->getTranslation('name', 'en'));
        $this->assertSame('lithium', $category->slug);
    }

    public function test_slug_must_be_unique(): void
    {
        ProductCategory::create(['slug' => 'lithium', 'name' => ['en' => 'Lithium'], 'is_active' => true, 'sort_order' => 0]);

        $editor = User::factory()->create();
        $editor->assignRole('content-editor');

        Livewire::actingAs($editor)
            ->test(CategoryForm::class)
            ->set('name', 'Lithium Two')
            ->set('slug', 'lithium')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_sales_cannot_create_categories(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.products.categories.index'))->assertForbidden();
    }
}
