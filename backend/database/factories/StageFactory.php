<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stage>
 */
class StageFactory extends Factory
{
    protected $model = Stage::class;

    public function definition(): array
    {
        return [
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'pipeline_id' => Pipeline::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->optional()->sentence(),
            'order' => $this->faker->numberBetween(0, 10),
            'is_final' => $this->faker->boolean(20), // 20% chance of being final
        ];
    }

    public function final(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_final' => true,
        ]);
    }

    public function nonFinal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_final' => false,
        ]);
    }

    public function withOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
        ]);
    }
}
