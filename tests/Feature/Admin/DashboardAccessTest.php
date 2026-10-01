<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The dashboard itself only requires being logged in — these tests are
 * really exercising the sidebar's @can gating (CLAUDE.md §5: gate by
 * permission, not role), the actual mechanism every future admin route
 * will rely on.
 */
class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_any_authenticated_staff_member_can_view_the_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('sales');

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_super_admin_sees_every_sidebar_link(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        foreach (['Products', 'Blog', 'Careers', 'Pages', 'Media', 'Enquiries', 'Dealers', 'Users'] as $label) {
            $response->assertSee($label);
        }
    }

    public function test_content_editor_only_sees_content_links(): void
    {
        $user = User::factory()->create();
        $user->assignRole('content-editor');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        foreach (['Products', 'Blog', 'Careers', 'Pages', 'Media'] as $label) {
            $response->assertSee($label);
        }

        foreach (['Enquiries', 'Dealers', 'Users'] as $label) {
            $response->assertDontSee($label);
        }
    }

    public function test_sales_only_sees_lead_links(): void
    {
        $user = User::factory()->create();
        $user->assignRole('sales');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        foreach (['Enquiries', 'Dealers'] as $label) {
            $response->assertSee($label);
        }

        foreach (['Products', 'Blog', 'Careers', 'Pages', 'Media', 'Users'] as $label) {
            $response->assertDontSee($label);
        }
    }
}
