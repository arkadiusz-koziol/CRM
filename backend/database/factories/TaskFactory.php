<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3, 6),
            'description' => $this->faker->paragraph(2, 4),
            'status' => $this->faker->randomElement(TaskStatus::cases()),
            'priority' => $this->faker->randomElement(TaskPriority::cases()),
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
            'due_date' => $this->faker->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'completed_at' => $this->faker->optional(0.3)->dateTimeBetween('-30 days', 'now'),
            'estimated_hours' => $this->faker->optional(0.8)->randomFloat(2, 0.5, 40.0),
            'actual_hours' => $this->faker->optional(0.2)->randomFloat(2, 0.5, 50.0),
        ];
    }

    /**
     * Indicate that the task is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::PENDING,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::IN_PROGRESS,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::COMPLETED,
            'completed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Indicate that the task is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::HIGH,
        ]);
    }

    /**
     * Indicate that the task is urgent priority.
     */
    public function urgentPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::URGENT,
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'status' => $this->faker->randomElement([TaskStatus::PENDING, TaskStatus::IN_PROGRESS]),
        ]);
    }

    /**
     * Indicate that the task is due today.
     */
    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->endOfDay(),
            'status' => $this->faker->randomElement([TaskStatus::PENDING, TaskStatus::IN_PROGRESS]),
        ]);
    }
}
