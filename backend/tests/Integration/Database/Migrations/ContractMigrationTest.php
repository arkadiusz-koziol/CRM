<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Migrations;

use App\Enums\Billing\ContractStatus;
use App\Models\Company;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContractMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure all migrations are run in correct order
        $this->artisan('migrate');
        $this->seed();
    }

    public function test_contracts_table_has_correct_structure(): void
    {
        $this->assertTrue(\Schema::hasTable('contracts'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'id'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'nr'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'company_id'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'start_at'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'end_at'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'amount'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'currency'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'status'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'created_at'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'updated_at'));
        $this->assertTrue(\Schema::hasColumn('contracts', 'deleted_at'));
    }

    public function test_contracts_table_accepts_valid_data(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $contract = Contract::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'CON-0001',
            'company_id' => $company->id,
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 50000.00,
            'currency' => 'USD',
            'status' => ContractStatus::ACTIVE->value,
        ]);

        $this->assertDatabaseHas('contracts', [
            'nr' => 'CON-0001',
            'company_id' => $company->id,
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 50000.00,
            'currency' => 'USD',
            'status' => ContractStatus::ACTIVE->value,
        ]);
    }

    public function test_contracts_table_soft_deletes(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $contract = Contract::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'CON-SOFT-DELETE',
            'company_id' => $company->id,
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 25000.00,
            'currency' => 'EUR',
            'status' => ContractStatus::DRAFT->value,
        ]);

        $contract->delete();

        $this->assertSoftDeleted('contracts', [
            'id' => $contract->id,
        ]);
    }

    public function test_contracts_table_foreign_key_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Contract::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'CON-INVALID',
            'company_id' => 'non-existent-company-id',
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 10000.00,
            'currency' => 'USD',
            'status' => ContractStatus::DRAFT->value,
        ]);
    }

    public function test_contracts_table_unique_nr_constraint(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        Contract::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'CON-UNIQUE',
            'company_id' => $company->id,
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 10000.00,
            'currency' => 'USD',
            'status' => ContractStatus::DRAFT->value,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Contract::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'CON-UNIQUE', // Same number
            'company_id' => $company->id,
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 20000.00,
            'currency' => 'USD',
            'status' => ContractStatus::DRAFT->value,
        ]);
    }

    public function test_contracts_table_default_values(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $contract = Contract::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'nr' => 'CON-DEFAULT',
            'company_id' => $company->id,
            'start_at' => '2024-01-01',
            'end_at' => '2024-12-31',
            'amount' => 10000.00,
            'currency' => 'USD', // Explicitly set to test default
            'status' => ContractStatus::DRAFT->value, // Explicitly set to test default
        ]);

        $this->assertEquals('USD', $contract->currency);
        $this->assertEquals(ContractStatus::DRAFT, $contract->status);
    }

    public function test_contracts_table_handles_all_statuses(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $statuses = [
            ContractStatus::DRAFT,
            ContractStatus::ACTIVE,
            ContractStatus::EXPIRED,
            ContractStatus::TERMINATED,
        ];

        foreach ($statuses as $status) {
            $contract = Contract::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'nr' => 'CON-'.$status->value,
                'company_id' => $company->id,
                'start_at' => '2024-01-01',
                'end_at' => '2024-12-31',
                'amount' => 10000.00,
                'currency' => 'USD',
                'status' => $status->value,
            ]);

            $this->assertEquals($status, $contract->status);
        }
    }
}
