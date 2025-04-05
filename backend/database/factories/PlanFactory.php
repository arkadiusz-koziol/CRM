<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Estate;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'estate_id' => Estate::factory(),
            'file_path' => 'plans/pdf/' . $this->faker->word . '.pdf',
            'image_path' => 'plans/images/' . $this->faker->word . '.jpg',
        ];
    }
}
