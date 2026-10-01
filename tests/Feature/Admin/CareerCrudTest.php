<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Careers\Form as CareerForm;
use App\Livewire\Admin\Careers\Index as CareersIndex;
use App\Models\JobOpening;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CareerCrudTest extends TestCase
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

    public function test_a_content_editor_can_create_a_draft_opening(): void
    {
        Livewire::actingAs($this->editor())
            ->test(CareerForm::class)
            ->set('title', 'Production Supervisor')
            ->set('department', 'Production')
            ->set('location', 'Kanpur, Uttar Pradesh')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.careers.index'));

        $opening = JobOpening::first();
        $this->assertNotNull($opening);
        $this->assertSame('Production Supervisor', $opening->getTranslation('title', 'en'));
        $this->assertSame('production-supervisor', $opening->slug);
        $this->assertNull($opening->published_at);
    }

    public function test_setting_a_future_publish_date_schedules_the_opening(): void
    {
        Livewire::actingAs($this->editor())
            ->test(CareerForm::class)
            ->set('title', 'Scheduled Role')
            ->set('publishedAt', now()->addWeek()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $opening = JobOpening::first();
        $this->assertTrue($opening->published_at->isFuture());
        $this->assertFalse(JobOpening::open()->whereKey($opening->id)->exists());
    }

    public function test_publish_now_makes_the_opening_immediately_visible(): void
    {
        Livewire::actingAs($this->editor())
            ->test(CareerForm::class)
            ->set('title', 'Live Right Away')
            ->call('publishNow')
            ->call('save')
            ->assertHasNoErrors();

        $opening = JobOpening::first();

        $this->assertTrue(JobOpening::open()->whereKey($opening->id)->exists());
        $this->get(route('careers.index'))->assertSee('Live Right Away');
    }

    public function test_a_closing_date_in_the_past_takes_the_opening_off_the_public_page(): void
    {
        Livewire::actingAs($this->editor())
            ->test(CareerForm::class)
            ->set('title', 'Already Closed Role')
            ->call('publishNow')
            ->set('closesOn', now()->subDay()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->get(route('careers.index'))->assertDontSee('Already Closed Role');
    }

    public function test_a_duplicate_slug_is_rejected(): void
    {
        JobOpening::factory()->create(['slug' => 'taken-slug']);

        Livewire::actingAs($this->editor())
            ->test(CareerForm::class)
            ->set('title', 'A new role')
            ->set('slug', 'taken-slug')
            ->call('save')
            ->assertHasErrors(['slug' => 'unique']);
    }

    public function test_a_rejected_save_tells_the_editor_why(): void
    {
        Livewire::actingAs($this->editor())
            ->test(CareerForm::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title' => 'required'])
            ->assertSee('The title field is required.')
            ->assertSee('Not saved.');
    }

    public function test_sales_cannot_reach_career_management(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.careers.index'))->assertForbidden();
    }

    public function test_an_opening_can_be_soft_deleted_and_restored(): void
    {
        $opening = JobOpening::factory()->create();

        Livewire::actingAs($this->editor())->test(CareersIndex::class)->call('delete', $opening->id);
        $this->assertSoftDeleted($opening);

        Livewire::actingAs($this->editor())->test(CareersIndex::class)->set('showTrashed', true)->call('restore', $opening->id);
        $this->assertNotSoftDeleted($opening->fresh());
    }
}
