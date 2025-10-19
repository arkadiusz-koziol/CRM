<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        $commentableTypes = [
            'App\\Models\\Task',
            'App\\Models\\Company',
            'App\\Models\\Contact',
            'App\\Models\\Opportunity',
            'App\\Models\\Estate',
        ];

        return [
            'id' => Str::uuid()->toString(),
            'content' => $this->faker->paragraph(),
            'content_html' => '<p>'.$this->faker->paragraph().'</p>',
            'commentable_type' => $this->faker->randomElement($commentableTypes),
            'commentable_id' => Str::uuid()->toString(),
            'author_id' => User::factory(),
            'parent_id' => null,
            'is_private' => $this->faker->boolean(20), // 20% chance of being private
            'mentions' => $this->faker->optional(0.3)->randomElements(['@user1', '@user2'], $this->faker->numberBetween(1, 3)),
        ];
    }

    public function reply(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Str::uuid()->toString(),
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }
}
