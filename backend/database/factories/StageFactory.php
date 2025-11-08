<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stage>
 */
class StageFactory extends Factory
{
    protected $model = Stage::class;

    public function definition(): array
    {
        return [
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'pipeline_id' => Pipeline::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->optional()->sentence(),
            'order' => $this->faker->numberBetween(1, 10),
            'is_final' => false,
        ];
    }

    public function final(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_final' => true,
        ]);
    }
}

