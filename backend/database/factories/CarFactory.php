<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company.' '.$this->faker->word,
            'description' => $this->faker->sentence,
            'registration_number' => strtoupper($this->faker->bothify('??####')),
            'technical_details' => $this->faker->optional()->sentence,
        ];
    }
}

