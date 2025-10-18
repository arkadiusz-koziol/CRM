<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TrainingCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TrainingCategoryFactory extends Factory
{
    protected $model = TrainingCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true).' Training',
        ];
    }
}
