<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Crm\OpportunityStatus;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opportunity>
 */
class OpportunityFactory extends Factory
{
    protected $model = Opportunity::class;

    public function definition(): array
    {
        return [
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'title' => $this->faker->words(3, true).' Deal',
            'company_id' => Company::factory(),
            'contact_id' => $this->faker->optional(0.7)->passthrough(Contact::factory()),
            'value' => $this->faker->randomFloat(2, 1000, 100000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'JPY']),
            'probability' => $this->faker->numberBetween(0, 100),
            'stage_id' => Stage::factory(),
            'owner_user_id' => User::factory(),
            'close_date' => $this->faker->optional(0.8)->dateTimeBetween('now', '+1 year'),
            'status' => $this->faker->randomElement(OpportunityStatus::cases()),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OpportunityStatus::OPEN,
        ]);
    }

    public function won(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OpportunityStatus::WON,
            'probability' => 100,
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OpportunityStatus::LOST,
            'probability' => 0,
        ]);
    }

    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $this->faker->randomFloat(2, 50000, 500000),
        ]);
    }

    public function lowValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $this->faker->randomFloat(2, 1000, 10000),
        ]);
    }

    public function highProbability(): static
    {
        return $this->state(fn (array $attributes) => [
            'probability' => $this->faker->numberBetween(70, 100),
        ]);
    }

    public function lowProbability(): static
    {
        return $this->state(fn (array $attributes) => [
            'probability' => $this->faker->numberBetween(0, 30),
        ]);
    }
}
