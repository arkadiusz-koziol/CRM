<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Billing\ContractStatus;
use App\Models\Company;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
final class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 month');
        $endDate = Carbon::parse($startDate)->addMonths(rand(6, 24));

        return [
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'CON-'.$this->faker->unique()->numberBetween(1000, 9999),
            'company_id' => Company::factory(),
            'start_at' => $startDate,
            'end_at' => $endDate,
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'status' => $this->faker->randomElement([
                ContractStatus::DRAFT,
                ContractStatus::ACTIVE,
                ContractStatus::EXPIRED,
                ContractStatus::TERMINATED,
            ]),
        ];
    }
}
