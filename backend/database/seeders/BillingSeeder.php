<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Billing\ContractStatus;
use App\Enums\Billing\InvoiceStatus;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (! $user) {
            $user = User::factory()->create();
        }

        // Create 5 demo contracts
        $contracts = [];
        for ($i = 1; $i <= 5; $i++) {
            $company = Company::factory()->create(['created_by' => $user->id]);

            $startDate = Carbon::now()->subMonths(rand(1, 6));
            $endDate = Carbon::now()->addMonths(rand(6, 24));

            $contract = Contract::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'nr' => 'CON-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'company_id' => $company->id,
                'start_at' => $startDate,
                'end_at' => $endDate,
                'amount' => rand(10000, 100000),
                'currency' => ['USD', 'EUR', 'GBP'][array_rand(['USD', 'EUR', 'GBP'])],
                'status' => [
                    ContractStatus::DRAFT,
                    ContractStatus::ACTIVE,
                    ContractStatus::EXPIRED,
                    ContractStatus::TERMINATED,
                ][array_rand([
                    ContractStatus::DRAFT,
                    ContractStatus::ACTIVE,
                    ContractStatus::EXPIRED,
                    ContractStatus::TERMINATED,
                ])],
            ]);

            $contracts[] = $contract;
        }

        // Create 20 demo invoices
        for ($i = 1; $i <= 20; $i++) {
            $company = Company::inRandomOrder()->first();
            $contract = $contracts[array_rand($contracts)];

            $issueDate = Carbon::now()->subDays(rand(1, 30));
            $dueDate = Carbon::now()->addDays(rand(1, 30));

            Invoice::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'nr' => 'INV-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'contract_id' => rand(0, 1) ? $contract->id : null, // 50% chance of being linked to contract
                'company_id' => $company->id,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'amount' => rand(1000, 50000),
                'currency' => ['USD', 'EUR', 'GBP'][array_rand(['USD', 'EUR', 'GBP'])],
                'status' => [
                    InvoiceStatus::ISSUED,
                    InvoiceStatus::PAID,
                    InvoiceStatus::OVERDUE,
                    InvoiceStatus::CANCELLED,
                ][array_rand([
                    InvoiceStatus::ISSUED,
                    InvoiceStatus::PAID,
                    InvoiceStatus::OVERDUE,
                    InvoiceStatus::CANCELLED,
                ])],
            ]);
        }
    }
}
