<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_page_renders(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Admin log in');
    }

    public function test_a_user_can_authenticate_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_user_cannot_authenticate_with_an_incorrect_password(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong']);
        }

        $response = $this->post(route('login'), ['email' => $user->email, 'password' => 'correct-password']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_user_can_log_out(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guests_are_redirected_to_login_when_visiting_the_admin_area(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }
}
