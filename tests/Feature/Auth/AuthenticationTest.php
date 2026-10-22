<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('images/login-background.jpg', false)
            ->assertSee('images/logo-dpp.png', false)
            ->assertSee('em-login-brand', false)
            ->assertSee('Portal Maintenance')
            ->assertSee('Masuk ke sistem');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'npk' => $user->npk,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'npk' => $user->npk,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_logout_does_not_change_the_account_password(): void
    {
        $user = User::factory()->create([
            'npk' => '20221914',
            'role' => User::ROLE_TECHNICIAN,
        ]);

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $user->fresh()->getRawOriginal('password')));

        $this->post('/login', [
            'npk' => '20221914',
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_pic_pin_as_password(): void
    {
        $user = User::factory()->create();

        $this->from('/login')->post('/login', [
            'npk' => $user->npk,
            'password' => '1234',
        ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('npk');

        $this->assertGuest();
    }
}
