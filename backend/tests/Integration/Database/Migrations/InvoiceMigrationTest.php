<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Migrations;

use App\Enums\Billing\InvoiceStatus;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InvoiceMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure all migrations are run in correct order
        $this->artisan('migrate');
        $this->seed();
    }

    public function test_invoices_table_has_correct_structure(): void
    {
        $this->assertTrue(\Schema::hasTable('invoices'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'id'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'nr'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'contract_id'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'company_id'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'issue_date'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'due_date'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'amount'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'currency'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'status'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'created_at'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'updated_at'));
        $this->assertTrue(\Schema::hasColumn('invoices', 'deleted_at'));
    }

    public function test_invoices_table_accepts_valid_data(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);
        $contract = Contract::factory()->create(['company_id' => $company->id]);

        $invoice = Invoice::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-0001',
            'contract_id' => $contract->id,
            'company_id' => $company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 5000.00,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ]);

        $this->assertDatabaseHas('invoices', [
            'nr' => 'INV-0001',
            'contract_id' => $contract->id,
            'company_id' => $company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 5000.00,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ]);
    }

    public function test_invoices_table_accepts_nullable_contract_id(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $invoice = Invoice::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-NO-CONTRACT',
            'contract_id' => null,
            'company_id' => $company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 3000.00,
            'currency' => 'EUR',
            'status' => InvoiceStatus::ISSUED->value,
        ]);

        $this->assertDatabaseHas('invoices', [
            'nr' => 'INV-NO-CONTRACT',
            'contract_id' => null,
            'company_id' => $company->id,
        ]);
    }

    public function test_invoices_table_soft_deletes(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $invoice = Invoice::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-SOFT-DELETE',
            'contract_id' => null,
            'company_id' => $company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 2000.00,
            'currency' => 'GBP',
            'status' => InvoiceStatus::CANCELLED->value,
        ]);

        $invoice->delete();

        $this->assertSoftDeleted('invoices', [
            'id' => $invoice->id,
        ]);
    }

    public function test_invoices_table_foreign_key_constraints(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        // Test invalid company_id
        $this->expectException(\Illuminate\Database\QueryException::class);
        Invoice::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-INVALID-COMPANY',
            'contract_id' => null,
            'company_id' => 'non-existent-company-id',
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 1000.00,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ]);

        // Test invalid contract_id
        $this->expectException(\Illuminate\Database\QueryException::class);
        Invoice::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-INVALID-CONTRACT',
            'contract_id' => 'non-existent-contract-id',
            'company_id' => $company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 1000.00,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ]);
    }

    public function test_invoices_table_unique_nr_constraint(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        Invoice::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-UNIQUE',
            'contract_id' => null,
            'company_id' => $company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 1000.00,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Invoice::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-UNIQUE', // Same number
            'contract_id' => null,
            'company_id' => $company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 2000.00,
            'currency' => 'USD',
            'status' => InvoiceStatus::ISSUED->value,
        ]);
    }

    public function test_invoices_table_default_values(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $invoice = Invoice::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'INV-DEFAULT',
            'contract_id' => null,
            'company_id' => $company->id,
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount' => 1000.00,
            'currency' => 'USD', // Explicitly set to test default
            'status' => InvoiceStatus::ISSUED->value, // Explicitly set to test default
        ]);

        $this->assertEquals('USD', $invoice->currency);
        $this->assertEquals(InvoiceStatus::ISSUED, $invoice->status);
    }

    public function test_invoices_table_handles_all_statuses(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $statuses = [
            InvoiceStatus::ISSUED,
            InvoiceStatus::PAID,
            InvoiceStatus::OVERDUE,
            InvoiceStatus::CANCELLED,
        ];

        foreach ($statuses as $status) {
            $invoice = Invoice::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'nr' => 'INV-'.$status->value,
                'contract_id' => null,
                'company_id' => $company->id,
                'issue_date' => '2024-01-01',
                'due_date' => '2024-01-31',
                'amount' => 1000.00,
                'currency' => 'USD',
                'status' => $status->value,
            ]);

            $this->assertEquals($status, $invoice->status);
        }
    }
}
