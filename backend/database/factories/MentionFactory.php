<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Mention;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

final class MentionFactory extends Factory
{
    protected $model = Mention::class;

    public function definition(): array
    {
        return [
            'id' => Uuid::uuid7()->toString(),
            'comment_id' => Comment::factory(),
            'mentioned_user_id' => User::factory(),
            'mentioner_user_id' => User::factory(),
            'entity_type' => $this->faker->randomElement(['company', 'contact', 'opportunity', 'task']),
            'entity_id' => Uuid::uuid7()->toString(),
            'notified_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 week', 'now'),
            'read_at' => $this->faker->optional(0.6)->dateTimeBetween('-1 week', 'now'),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    public function unnotified(): static
    {
        return $this->state(fn (array $attributes) => [
            'notified_at' => null,
        ]);
    }
}
