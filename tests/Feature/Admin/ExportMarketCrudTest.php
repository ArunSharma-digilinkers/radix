<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Export\Form as ExportForm;
use App\Livewire\Admin\Export\Index as ExportIndex;
use App\Models\ExportMarket;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExportMarketCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function editor(): User
    {
        $user = User::factory()->create();
        $user->assignRole('content-editor');

        return $user;
    }

    public function test_a_content_editor_can_create_a_market(): void
    {
        Livewire::actingAs($this->editor())
            ->test(ExportForm::class)
            ->set('countryName', 'Nigeria')
            ->set('isoCode', 'ng')
            ->set('isoNumeric', '566')
            ->set('blurb', 'A growing distributor network across West Africa.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.export.index'));

        $market = ExportMarket::first();
        $this->assertNotNull($market);
        $this->assertSame('Nigeria', $market->getTranslation('country_name', 'en'));
        $this->assertSame('nigeria', $market->slug);
        $this->assertSame('NG', $market->iso_code);
        $this->assertSame(566, $market->iso_numeric);
    }

    public function test_slug_must_be_unique(): void
    {
        ExportMarket::factory()->create(['slug' => 'nigeria']);

        Livewire::actingAs($this->editor())
            ->test(ExportForm::class)
            ->set('countryName', 'Nigeria Two')
            ->set('slug', 'nigeria')
            ->set('isoCode', 'NG')
            ->call('save')
            ->assertHasErrors(['slug' => 'unique']);
    }

    public function test_iso_code_must_be_unique(): void
    {
        ExportMarket::factory()->create(['iso_code' => 'NG']);

        Livewire::actingAs($this->editor())
            ->test(ExportForm::class)
            ->set('countryName', 'Duplicate ISO')
            ->set('isoCode', 'NG')
            ->call('save')
            ->assertHasErrors(['isoCode' => 'unique']);
    }

    public function test_the_iso_numeric_code_is_optional(): void
    {
        Livewire::actingAs($this->editor())
            ->test(ExportForm::class)
            ->set('countryName', 'No Map Highlight Yet')
            ->set('isoCode', 'ZZ')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull(ExportMarket::first()->iso_numeric);
    }

    public function test_a_market_can_be_deactivated(): void
    {
        $market = ExportMarket::factory()->create(['is_active' => true]);

        Livewire::actingAs($this->editor())->test(ExportIndex::class)->call('toggleActive', $market->id);

        $this->assertFalse($market->fresh()->is_active);
    }

    public function test_a_market_can_be_deleted(): void
    {
        $market = ExportMarket::factory()->create();

        Livewire::actingAs($this->editor())->test(ExportIndex::class)->call('delete', $market->id);

        $this->assertNull(ExportMarket::find($market->id));
    }

    public function test_sales_cannot_manage_export_markets(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.export.index'))->assertForbidden();
    }
}
