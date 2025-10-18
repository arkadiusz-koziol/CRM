<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Billing\InvoiceStatus;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
final class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $issueDate = $this->faker->dateTimeBetween('-3 months', 'now');
        $dueDate = Carbon::parse($issueDate)->addDays(rand(15, 45));

        return [
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-'.$this->faker->unique()->numberBetween(1000, 9999),
            'contract_id' => Contract::factory(),
            'company_id' => Company::factory(),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'status' => $this->faker->randomElement([
                InvoiceStatus::ISSUED,
                InvoiceStatus::PAID,
                InvoiceStatus::OVERDUE,
                InvoiceStatus::CANCELLED,
            ]),
        ];
    }

    public function withoutContract(): static
    {
        return $this->state(fn (array $attributes) => [
            'contract_id' => null,
        ]);
    }
}
