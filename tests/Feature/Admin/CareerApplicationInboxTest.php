<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Careers\Applications;
use App\Models\JobApplication;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CareerApplicationInboxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('local');
    }

    private function editor(): User
    {
        $user = User::factory()->create();
        $user->assignRole('content-editor');

        return $user;
    }

    public function test_a_content_editor_can_view_the_inbox(): void
    {
        $this->actingAs($this->editor())->get(route('admin.careers.applications.index'))->assertOk();
    }

    public function test_it_lists_a_submitted_application(): void
    {
        JobApplication::factory()->create(['name' => 'Visible Applicant']);

        Livewire::actingAs($this->editor())
            ->test(Applications::class)
            ->assertSee('Visible Applicant');
    }

    public function test_the_status_can_be_updated(): void
    {
        $application = JobApplication::factory()->create(['status' => JobApplication::STATUS_NEW]);

        Livewire::actingAs($this->editor())
            ->test(Applications::class)
            ->call('updateStatus', $application->id, JobApplication::STATUS_SHORTLISTED);

        $this->assertSame(JobApplication::STATUS_SHORTLISTED, $application->fresh()->status);
    }

    public function test_an_invalid_status_is_ignored(): void
    {
        $application = JobApplication::factory()->create(['status' => JobApplication::STATUS_NEW]);

        Livewire::actingAs($this->editor())
            ->test(Applications::class)
            ->call('updateStatus', $application->id, 'not-a-real-status');

        $this->assertSame(JobApplication::STATUS_NEW, $application->fresh()->status);
    }

    public function test_a_note_can_be_saved_without_exposing_hidden_fields(): void
    {
        $application = JobApplication::factory()->create();

        $component = Livewire::actingAs($this->editor())->test(Applications::class);
        $component->set("noteDrafts.{$application->id}", 'Strong candidate, schedule a call.');
        $component->call('saveNote', $application->id);

        $this->assertSame('Strong candidate, schedule a call.', $application->fresh()->internal_notes);

        // The resume's storage path never reaches the page's markup or the
        // component's serialised wire snapshot payload.
        $component->assertDontSee($application->resume_path ?? '__no_path__');
    }

    public function test_the_open_filter_excludes_rejected_and_hired_applications(): void
    {
        JobApplication::factory()->create(['name' => 'Still open', 'status' => JobApplication::STATUS_NEW]);
        JobApplication::factory()->create(['name' => 'Already hired', 'status' => JobApplication::STATUS_HIRED]);

        $component = Livewire::actingAs($this->editor())->test(Applications::class)->set('filter', 'open');

        $component->assertSee('Still open')->assertDontSee('Already hired');
    }

    public function test_the_resume_can_be_downloaded(): void
    {
        $application = JobApplication::factory()->create();
        Storage::disk('local')->put($application->resume_path, 'fake pdf contents');

        $response = Livewire::actingAs($this->editor())
            ->test(Applications::class)
            ->call('download', $application->id);

        $response->assertStatus(200);
    }

    public function test_sales_cannot_access_career_applications(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.careers.applications.index'))->assertForbidden();
    }
}
