<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pipeline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pipeline>
 */
class PipelineFactory extends Factory
{
    protected $model = Pipeline::class;

    public function definition(): array
    {
        return [
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => $this->faker->words(3, true) . ' Pipeline',
            'description' => $this->faker->optional()->sentence(),
            'is_default' => $this->faker->boolean(20), // 20% chance of being default
            'created_by' => User::factory(),
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function nonDefault(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => false,
        ]);
    }
}