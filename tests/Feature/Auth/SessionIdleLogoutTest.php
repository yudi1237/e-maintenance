<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionIdleLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_idle_logout_policy_is_not_registered(): void
    {
        $this->assertFileDoesNotExist(app_path('Http/Middleware/EnsureSessionIsActive.php'));
        $this->assertFileDoesNotExist(resource_path('js/session-idle.js'));
        $this->assertArrayNotHasKey('session_idle_minutes', config('maintenance'));
    }

    public function test_technician_stays_authenticated_after_long_inactivity(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_TECHNICIAN]);

        $this->actingAs($user);
        session(['last_activity' => time() - 3600]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('session-idle-seconds', false)
            ->assertDontSee('session-idle-logout-form', false)
            ->assertDontSee('Sesi berakhir karena tidak ada aktivitas', false);

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_stays_authenticated_after_long_inactivity(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);
        session(['last_activity' => time() - 3600]);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('session-idle-seconds', false)
            ->assertDontSee('session-idle-logout-form', false)
            ->assertDontSee('Sesi berakhir karena tidak ada aktivitas', false);

        $this->assertAuthenticatedAs($admin);
    }

    public function test_idle_does_not_change_technician_versus_admin_routing(): void
    {
        $technician = User::factory()->create(['role' => User::ROLE_TECHNICIAN]);
        $this->actingAs($technician);
        session(['last_activity' => time() - 3600]);

        $this->get(route('admin.dashboard'))->assertRedirect(route('home'));
        $this->get(route('home'))->assertOk();
        $this->assertAuthenticatedAs($technician);

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        session(['last_activity' => time() - 3600]);

        $this->get(route('home'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.dashboard'))->assertOk();
        $this->assertAuthenticatedAs($admin);
    }
}
