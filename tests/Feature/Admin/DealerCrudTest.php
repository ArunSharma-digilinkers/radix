<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Dealers\Form as DealerForm;
use App\Livewire\Admin\Dealers\Index as DealersIndex;
use App\Models\Dealer;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Admin dealer management. `dealers.manage` belongs to `sales` and
 * `super-admin` (CLAUDE.md §5) — content-editor must not reach it, the same
 * shape as ProductCategoryCrudTest's sales-cannot-manage-products check in
 * reverse.
 */
class DealerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function sales(): User
    {
        $user = User::factory()->create();
        $user->assignRole('sales');

        return $user;
    }

    public function test_sales_can_create_a_dealer(): void
    {
        Livewire::actingAs($this->sales())
            ->test(DealerForm::class)
            ->set('name', 'Kanpur Battery House')
            ->set('type', Dealer::TYPE_DISTRIBUTOR)
            ->set('city', 'Kanpur')
            ->set('state', 'Uttar Pradesh')
            ->set('pincode', '208001')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dealers.index'));

        $dealer = Dealer::first();
        $this->assertNotNull($dealer);
        $this->assertSame('Kanpur Battery House', $dealer->name);
        $this->assertSame(Dealer::TYPE_DISTRIBUTOR, $dealer->type);
        $this->assertTrue($dealer->is_active);
    }

    public function test_city_and_state_are_required(): void
    {
        Livewire::actingAs($this->sales())
            ->test(DealerForm::class)
            ->set('name', 'No Address Store')
            ->set('city', '')
            ->set('state', '')
            ->call('save')
            ->assertHasErrors(['city', 'state']);
    }

    public function test_coordinates_must_be_within_range(): void
    {
        Livewire::actingAs($this->sales())
            ->test(DealerForm::class)
            ->set('name', 'Bad Coordinates Store')
            ->set('city', 'Kanpur')
            ->set('state', 'Uttar Pradesh')
            ->set('latitude', '200')
            ->call('save')
            ->assertHasErrors(['latitude']);
    }

    public function test_sales_can_edit_a_dealer(): void
    {
        $dealer = Dealer::factory()->create(['name' => 'Old Name']);

        Livewire::actingAs($this->sales())
            ->test(DealerForm::class, ['dealer' => $dealer])
            ->set('name', 'New Name')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('New Name', $dealer->fresh()->name);
    }

    public function test_sales_can_deactivate_and_reactivate_a_dealer(): void
    {
        $dealer = Dealer::factory()->create(['is_active' => true]);

        Livewire::actingAs($this->sales())->test(DealersIndex::class)->call('toggleActive', $dealer->id);
        $this->assertFalse($dealer->fresh()->is_active);

        Livewire::actingAs($this->sales())->test(DealersIndex::class)->call('toggleActive', $dealer->id);
        $this->assertTrue($dealer->fresh()->is_active);
    }

    public function test_sales_can_soft_delete_and_restore_a_dealer(): void
    {
        $dealer = Dealer::factory()->create();

        Livewire::actingAs($this->sales())->test(DealersIndex::class)->call('delete', $dealer->id);
        $this->assertSoftDeleted($dealer);

        Livewire::actingAs($this->sales())->test(DealersIndex::class)->call('restore', $dealer->id);
        $this->assertNull($dealer->fresh()->deleted_at);
    }

    public function test_content_editor_cannot_reach_dealer_management(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('content-editor');

        $this->actingAs($editor)->get(route('admin.dealers.index'))->assertForbidden();
    }
}
