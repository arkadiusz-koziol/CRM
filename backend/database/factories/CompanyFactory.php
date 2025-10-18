<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'id' => Uuid::uuid7()->toString(),
            'name' => fake()->company(),
            'industry' => fake()->randomElement([
                'Technology', 'Healthcare', 'Finance', 'Manufacturing', 'Retail',
                'Education', 'Energy', 'Logistics', 'Consulting', 'Other',
            ]),
            'source' => fake()->randomElement(CompanySource::cases())->value,
            'status' => fake()->randomElement(CompanyStatus::cases())->value,
            'region' => fake()->randomElement([
                'North America', 'Europe', 'Asia', 'South America', 'Africa', 'Oceania',
            ]),
            'vat_id' => fake()->optional(0.7)->numerify('US########'),
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CompanyStatus::ACTIVE->value,
        ]);
    }

    public function prospect(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CompanyStatus::PROSPECT->value,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CompanyStatus::INACTIVE->value,
        ]);
    }

    public function withVatId(): static
    {
        return $this->state(fn (array $attributes) => [
            'vat_id' => fake()->numerify('US########'),
        ]);
    }
}
