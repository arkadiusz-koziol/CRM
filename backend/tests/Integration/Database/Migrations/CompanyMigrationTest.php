<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Migrations;

use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_companies_table_has_correct_structure(): void
    {
        $this->assertTrue(\Schema::hasTable('companies'));
        $this->assertTrue(\Schema::hasColumn('companies', 'id'));
        $this->assertTrue(\Schema::hasColumn('companies', 'name'));
        $this->assertTrue(\Schema::hasColumn('companies', 'industry'));
        $this->assertTrue(\Schema::hasColumn('companies', 'source'));
        $this->assertTrue(\Schema::hasColumn('companies', 'status'));
        $this->assertTrue(\Schema::hasColumn('companies', 'region'));
        $this->assertTrue(\Schema::hasColumn('companies', 'vat_id'));
        $this->assertTrue(\Schema::hasColumn('companies', 'created_by'));
        $this->assertTrue(\Schema::hasColumn('companies', 'created_at'));
        $this->assertTrue(\Schema::hasColumn('companies', 'updated_at'));
        $this->assertTrue(\Schema::hasColumn('companies', 'deleted_at'));
    }

    public function test_companies_table_accepts_valid_data(): void
    {
        $user = User::factory()->create();

        $company = Company::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Test Company',
            'industry' => 'Technology',
            'source' => CompanySource::WEBSITE->value,
            'status' => CompanyStatus::ACTIVE->value,
            'region' => 'North America',
            'vat_id' => 'US123456789',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'industry' => 'Technology',
            'source' => CompanySource::WEBSITE->value,
            'status' => CompanyStatus::ACTIVE->value,
            'region' => 'North America',
            'vat_id' => 'US123456789',
            'created_by' => $user->id,
        ]);
    }

    public function test_companies_table_enforces_vat_id_uniqueness(): void
    {
        $user = User::factory()->create();

        Company::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'First Company',
            'source' => CompanySource::WEBSITE->value,
            'status' => CompanyStatus::ACTIVE->value,
            'vat_id' => 'US123456789',
            'created_by' => $user->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Company::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Second Company',
            'source' => CompanySource::WEBSITE->value,
            'status' => CompanyStatus::ACTIVE->value,
            'vat_id' => 'US123456789', // Duplicate VAT ID
            'created_by' => $user->id,
        ]);
    }

    public function test_companies_table_accepts_nullable_fields(): void
    {
        $user = User::factory()->create();

        $company = Company::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Minimal Company',
            'source' => CompanySource::WEBSITE->value,
            'status' => CompanyStatus::PROSPECT->value,
            'industry' => null,
            'region' => null,
            'vat_id' => null,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Minimal Company',
            'industry' => null,
            'region' => null,
            'vat_id' => null,
        ]);
    }

    public function test_companies_table_soft_deletes(): void
    {
        $user = User::factory()->create();

        $company = Company::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Soft Delete Test',
            'source' => CompanySource::WEBSITE->value,
            'status' => CompanyStatus::ACTIVE->value,
            'created_by' => $user->id,
        ]);

        $company->delete();

        $this->assertSoftDeleted('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_companies_table_foreign_key_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Company::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Invalid Company',
            'source' => CompanySource::WEBSITE->value,
            'status' => CompanyStatus::ACTIVE->value,
            'created_by' => 'non-existent-user-id',
        ]);
    }
}
