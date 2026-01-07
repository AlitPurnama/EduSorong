<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAndAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertViewIs('welcome');
    }

    public function test_campaigns_index_and_show_are_accessible(): void
    {
        $campaign = Campaign::factory()->create();

        $response = $this->get(route('campaigns.index'));
        $response->assertOk();
        $response->assertViewIs('campaigns.index');

        $response = $this->get(route('campaigns.show', $campaign));
        $response->assertOk();
        $response->assertViewIs('campaigns.show');
    }

    public function test_public_user_profile_is_accessible(): void
    {
        $user = User::factory()->create();
        $response = $this->get(route('users.show', $user));
        $response->assertOk();
        $response->assertViewIs('profile.show');
    }

    public function test_register_login_logout_flow(): void
    {
        // Register
        $register = $this->post(route('auth.register'), [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $register->assertRedirect('/');
        $this->assertAuthenticated();

        // Logout
        $logout = $this->post(route('logout'));
        $logout->assertRedirect('/');
        $this->assertGuest();

        // Login
        $user = User::where('email', 'tester@example.com')->first();
        $login = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $login->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}


