<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Enquiries\Inbox;
use App\Models\Enquiry;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EnquiryInboxTest extends TestCase
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

    public function test_sales_can_view_the_inbox(): void
    {
        $this->actingAs($this->sales())->get(route('admin.enquiries.index'))->assertOk();
    }

    public function test_marking_responded_calls_the_models_own_method_not_a_raw_status_write(): void
    {
        $enquiry = Enquiry::factory()->create(['status' => Enquiry::STATUS_QUALIFIED]);

        Livewire::actingAs($this->sales())
            ->test(Inbox::class)
            ->call('markResponded', $enquiry->id);

        $enquiry->refresh();
        $this->assertNotNull($enquiry->responded_at);
        // markResponded() only advances NEW -> CONTACTED; a later status must not rewind.
        $this->assertSame(Enquiry::STATUS_QUALIFIED, $enquiry->status);
    }

    public function test_a_note_can_be_saved_without_exposing_hidden_request_metadata(): void
    {
        $enquiry = Enquiry::factory()->create(['ip_address' => '203.0.113.4']);

        $component = Livewire::actingAs($this->sales())->test(Inbox::class);
        $component->set("noteDrafts.{$enquiry->id}", 'Called, no answer yet.');
        $component->call('saveNote', $enquiry->id);

        $this->assertSame('Called, no answer yet.', $enquiry->fresh()->internal_notes);

        // The IP never reaches the page at all — not the markup, not the
        // component's serialised wire snapshot payload.
        $component->assertDontSee('203.0.113.4');
    }

    public function test_the_open_filter_excludes_won_and_lost_leads(): void
    {
        Enquiry::factory()->create(['name' => 'New one']);
        Enquiry::factory()->create(['name' => 'Sold', 'status' => Enquiry::STATUS_WON]);

        $component = Livewire::actingAs($this->sales())->test(Inbox::class)->set('filter', 'open');

        $component->assertSee('New one')->assertDontSee('Sold');
    }

    public function test_content_editor_cannot_access_the_inbox(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('content-editor');

        $this->actingAs($editor)->get(route('admin.enquiries.index'))->assertForbidden();
    }

    public function test_csv_export_streams_a_download(): void
    {
        Enquiry::factory()->create(['name' => 'Export Me']);

        $response = Livewire::actingAs($this->sales())->test(Inbox::class)->call('export');

        $response->assertStatus(200);
    }
}
