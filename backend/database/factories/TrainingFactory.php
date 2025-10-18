<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TrainingFactory extends Factory
{
    protected $model = Training::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph,
            'category' => $this->faker->randomElement(['Safety', 'Technical', 'Management', 'Compliance']),
            'category_id' => \App\Models\TrainingCategory::factory(),
            'file_path' => $this->faker->optional()->filePath(),
            'file_name' => $this->faker->optional()->word().'.pdf',
            'file_size' => $this->faker->optional()->numberBetween(1024, 10240000),
            'mime_type' => $this->faker->optional()->randomElement(['application/pdf', 'application/vnd.openxmlformats-officedocument.presentationml.presentation']),
        ];
    }
}
