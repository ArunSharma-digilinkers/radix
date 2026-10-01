<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Users\Form as UserForm;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        return $user;
    }

    public function test_a_super_admin_can_create_a_user_with_a_role(): void
    {
        Livewire::actingAs($this->superAdmin())
            ->test(UserForm::class)
            ->set('name', 'New Editor')
            ->set('email', 'new-editor@radix.test')
            ->set('role', 'content-editor')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'new-editor@radix.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('content-editor'));
        $this->assertTrue($user->is_active);
    }

    public function test_content_editor_cannot_reach_user_management(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('content-editor');

        $this->actingAs($editor)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_a_super_admin_cannot_remove_their_own_super_admin_role(): void
    {
        $admin = $this->superAdmin();

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['user' => $admin])
            ->set('role', 'content-editor')
            ->call('save')
            ->assertHasErrors(['role']);

        $this->assertTrue($admin->fresh()->hasRole('super-admin'));
    }

    public function test_a_super_admin_cannot_deactivate_their_own_account(): void
    {
        $admin = $this->superAdmin();

        Livewire::actingAs($admin)->test(UsersIndex::class)->call('toggleActive', $admin->id);

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_a_deactivated_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'password123', 'is_active' => false]);
        $user->assignRole('content-editor');

        $response = $this->post(route('login'), ['email' => $user->email, 'password' => 'password123']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
