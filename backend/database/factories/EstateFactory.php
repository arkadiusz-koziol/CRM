<?php

namespace Database\Factories;

use App\Models\Estate;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstateFactory extends Factory
{
    protected $model = Estate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'custom_id' => strtoupper($this->faker->bothify('XYZ123')),
            'street' => $this->faker->streetName,
            'postal_code' => $this->faker->postcode,
            'city_id' => City::factory(),
            'house_number' => $this->faker->buildingNumber,
        ];
    }
}
