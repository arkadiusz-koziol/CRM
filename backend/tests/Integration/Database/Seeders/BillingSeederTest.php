<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Seeders;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\BillingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BillingSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure all migrations are run in correct order
        $this->artisan('migrate');
    }

    public function test_billing_seeder_creates_contracts(): void
    {
        $this->seed(BillingSeeder::class);

        $this->assertDatabaseCount('contracts', 5);
    }

    public function test_billing_seeder_creates_invoices(): void
    {
        $this->seed(BillingSeeder::class);

        $this->assertDatabaseCount('invoices', 20);
    }

    public function test_billing_seeder_creates_user_if_none_exists(): void
    {
        User::query()->delete(); // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $this->seed(BillingSeeder::class);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_billing_seeder_uses_existing_user(): void
    {
        $existingUser = User::factory()->create();
        $this->assertDatabaseCount('users', 1);

        $this->seed(BillingSeeder::class);

        $this->assertDatabaseCount('users', 1); // No new user should be created
    }

    public function test_billing_seeder_creates_companies_for_contracts(): void
    {
        $this->seed(BillingSeeder::class);

        $contracts = Contract::all();
        $this->assertCount(5, $contracts);

        foreach ($contracts as $contract) {
            $this->assertNotNull($contract->company);
            $this->assertDatabaseHas('companies', [
                'id' => $contract->company_id,
            ]);
        }
    }

    public function test_billing_seeder_creates_companies_for_invoices(): void
    {
        $this->seed(BillingSeeder::class);

        $invoices = Invoice::all();
        $this->assertCount(20, $invoices);

        foreach ($invoices as $invoice) {
            $this->assertNotNull($invoice->company);
            $this->assertDatabaseHas('companies', [
                'id' => $invoice->company_id,
            ]);
        }
    }

    public function test_billing_seeder_creates_contracts_with_valid_data(): void
    {
        $this->seed(BillingSeeder::class);

        $contracts = Contract::all();

        foreach ($contracts as $contract) {
            $this->assertNotEmpty($contract->nr);
            $this->assertStringStartsWith('CON-', $contract->nr);
            $this->assertNotNull($contract->company_id);
            $this->assertNotNull($contract->start_at);
            $this->assertNotNull($contract->end_at);
            $this->assertGreaterThan(0, $contract->amount);
            $this->assertContains($contract->currency, ['USD', 'EUR', 'GBP']);
            $this->assertNotNull($contract->status);
        }
    }

    public function test_billing_seeder_creates_invoices_with_valid_data(): void
    {
        $this->seed(BillingSeeder::class);

        $invoices = Invoice::all();

        foreach ($invoices as $invoice) {
            $this->assertNotEmpty($invoice->nr);
            $this->assertStringStartsWith('INV-', $invoice->nr);
            $this->assertNotNull($invoice->company_id);
            $this->assertNotNull($invoice->issue_date);
            $this->assertNotNull($invoice->due_date);
            $this->assertGreaterThan(0, $invoice->amount);
            $this->assertContains($invoice->currency, ['USD', 'EUR', 'GBP']);
            $this->assertNotNull($invoice->status);
        }
    }

    public function test_billing_seeder_creates_invoices_with_contract_links(): void
    {
        $this->seed(BillingSeeder::class);

        $invoices = Invoice::all();
        $invoicesWithContracts = $invoices->whereNotNull('contract_id');
        $invoicesWithoutContracts = $invoices->whereNull('contract_id');

        // Should have some invoices linked to contracts and some not
        $this->assertGreaterThan(0, $invoicesWithContracts->count());
        $this->assertGreaterThan(0, $invoicesWithoutContracts->count());

        // Verify contract relationships
        foreach ($invoicesWithContracts as $invoice) {
            $this->assertDatabaseHas('contracts', [
                'id' => $invoice->contract_id,
            ]);
        }
    }

    public function test_billing_seeder_creates_contracts_with_realistic_dates(): void
    {
        $this->seed(BillingSeeder::class);

        $contracts = Contract::all();

        foreach ($contracts as $contract) {
            $this->assertTrue($contract->start_at->isPast() || $contract->start_at->isToday());
            $this->assertTrue($contract->end_at->isFuture() || $contract->end_at->isToday());
            $this->assertTrue($contract->end_at->greaterThan($contract->start_at));
        }
    }

    public function test_billing_seeder_creates_invoices_with_realistic_dates(): void
    {
        $this->seed(BillingSeeder::class);

        $invoices = Invoice::all();

        foreach ($invoices as $invoice) {
            $this->assertTrue($invoice->issue_date->isPast() || $invoice->issue_date->isToday());
            $this->assertTrue($invoice->due_date->isFuture() || $invoice->due_date->isToday());
            $this->assertTrue($invoice->due_date->greaterThan($invoice->issue_date));
        }
    }
}
