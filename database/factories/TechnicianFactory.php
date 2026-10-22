<?php

namespace Database\Factories;

use App\Models\Technician;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Technician>
 */
class TechnicianFactory extends Factory
{
    protected $model = Technician::class;

    protected static ?string $pin;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'npk' => fake()->unique()->numerify('######'),
            'pin' => static::$pin ??= '1234',
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
