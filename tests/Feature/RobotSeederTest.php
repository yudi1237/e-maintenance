<?php

namespace Tests\Feature;

use App\Models\Robot;
use App\Models\User;
use App\Support\InjectionMachine;
use Database\Seeders\RobotSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RobotSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_sixty_active_demo_robots(): void
    {
        $this->seed(RobotSeeder::class);

        $this->assertSame(60, InjectionMachine::count());
        $this->assertSame(60, Robot::query()->count());
        $this->assertSame(60, Robot::query()->active()->count());
        $this->assertSame('R01', Robot::query()->where('robot_number', 'R01')->value('robot_number'));
        $this->assertSame('Yushin YC-150', Robot::query()->where('robot_number', 'R01')->value('robot_type'));
        $this->assertSame('R60', Robot::query()->where('robot_number', 'R60')->value('robot_number'));
        $this->assertSame('Harmo EX-II', Robot::query()->where('robot_number', 'R60')->value('robot_type'));
        $this->assertTrue((bool) Robot::query()->where('robot_number', 'R60')->value('is_active'));
    }

    public function test_seeder_is_idempotent_and_does_not_overwrite_real_robots(): void
    {
        Robot::query()->create([
            'robot_number' => 'R07',
            'robot_type' => 'Yushin YC-150 Custom',
            'is_active' => true,
        ]);

        $this->seed(RobotSeeder::class);
        $this->seed(RobotSeeder::class);

        $this->assertSame(60, Robot::query()->count());

        $real = Robot::query()->where('robot_number', 'R07')->first();
        $this->assertSame('Yushin YC-150 Custom', $real->robot_type);
        $this->assertSame(1, Robot::query()->where('robot_number', 'R01')->count());
    }

    public function test_admin_robot_master_lists_demo_robots(): void
    {
        $this->seed(RobotSeeder::class);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.robots.index'))
            ->assertOk()
            ->assertSee('Master Data Equipment')
            ->assertSee('Input Master Data Robot')
            ->assertSee('R01')
            ->assertSee('Yushin YC-150');
    }
}
