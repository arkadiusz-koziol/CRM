<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'count' => $this->faker->numberBetween(1, 1000),
            'price' => $this->faker->numberBetween(10, 1000),
        ];
    }
}
