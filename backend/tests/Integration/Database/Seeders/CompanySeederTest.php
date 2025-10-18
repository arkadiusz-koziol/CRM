<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_seeder_creates_companies(): void
    {
        // Create a user first as companies need created_by
        $user = User::factory()->create();

        $this->seed(CompanySeeder::class);

        $this->assertDatabaseCount('companies', 10);
    }

    public function test_company_seeder_creates_companies_with_diverse_data(): void
    {
        $user = User::factory()->create();

        $this->seed(CompanySeeder::class);

        $companies = Company::all();

        $this->assertCount(10, $companies);

        // Check that we have companies with different statuses
        $statuses = $companies->pluck('status')->unique()->toArray();
        $this->assertGreaterThan(1, count($statuses));

        // Check that we have companies with different sources
        $sources = $companies->pluck('source')->unique()->toArray();
        $this->assertGreaterThan(1, count($sources));

        // Check that we have companies with different regions
        $regions = $companies->pluck('region')->filter()->unique()->toArray();
        $this->assertGreaterThan(1, count($regions));
    }

    public function test_company_seeder_creates_companies_with_valid_vat_ids(): void
    {
        $user = User::factory()->create();

        $this->seed(CompanySeeder::class);

        $companies = Company::whereNotNull('vat_id')->get();

        $this->assertGreaterThan(0, $companies->count());

        foreach ($companies as $company) {
            $this->assertNotEmpty($company->vat_id);
            $this->assertIsString($company->vat_id);
        }
    }

    public function test_company_seeder_creates_companies_with_unique_vat_ids(): void
    {
        $user = User::factory()->create();

        $this->seed(CompanySeeder::class);

        $vatIds = Company::whereNotNull('vat_id')->pluck('vat_id')->toArray();
        $uniqueVatIds = array_unique($vatIds);

        $this->assertCount(count($vatIds), $uniqueVatIds);
    }

    public function test_company_seeder_creates_companies_with_industries(): void
    {
        $user = User::factory()->create();

        $this->seed(CompanySeeder::class);

        $companies = Company::whereNotNull('industry')->get();

        $this->assertGreaterThan(0, $companies->count());

        $industries = $companies->pluck('industry')->unique()->toArray();
        $this->assertGreaterThan(1, count($industries));
    }

    public function test_company_seeder_creates_companies_with_proper_relationships(): void
    {
        $user = User::factory()->create();

        $this->seed(CompanySeeder::class);

        $companies = Company::all();

        foreach ($companies as $company) {
            $this->assertEquals($user->id, $company->created_by);
            $this->assertInstanceOf(User::class, $company->creator);
        }
    }
}
