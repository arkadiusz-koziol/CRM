<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TrainingUserFactory extends Factory
{
    protected $model = TrainingUser::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'user_id' => User::factory(),
            'training_id' => Training::factory(),
            'status' => $this->faker->randomElement(['in_progress', 'completed', 'not_started']),
            'started_at' => $this->faker->optional(0.8)->dateTimeBetween('-30 days', 'now'),
            'completed_at' => $this->faker->optional(0.3)->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'started_at' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'completed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'started_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'completed_at' => null,
        ]);
    }
}
