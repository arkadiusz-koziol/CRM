<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have at least one user
        $user = \App\Models\User::first();
        if (! $user) {
            $user = \App\Models\User::factory()->create();
        }

        $companies = [
            [
                'name' => 'TechCorp Solutions',
                'industry' => 'Technology',
                'source' => CompanySource::WEBSITE->value,
                'status' => CompanyStatus::ACTIVE->value,
                'region' => 'North America',
                'vat_id' => 'US123456789',
            ],
            [
                'name' => 'Global Manufacturing Ltd',
                'industry' => 'Manufacturing',
                'source' => CompanySource::REFERRAL->value,
                'status' => CompanyStatus::ACTIVE->value,
                'region' => 'Europe',
                'vat_id' => 'EU987654321',
            ],
            [
                'name' => 'Digital Marketing Agency',
                'industry' => 'Marketing',
                'source' => CompanySource::SOCIAL_MEDIA->value,
                'status' => CompanyStatus::PROSPECT->value,
                'region' => 'North America',
                'vat_id' => null,
            ],
            [
                'name' => 'Healthcare Partners',
                'industry' => 'Healthcare',
                'source' => CompanySource::EMAIL_CAMPAIGN->value,
                'status' => CompanyStatus::ACTIVE->value,
                'region' => 'Europe',
                'vat_id' => 'EU555666777',
            ],
            [
                'name' => 'Financial Services Group',
                'industry' => 'Finance',
                'source' => CompanySource::COLD_CALL->value,
                'status' => CompanyStatus::PROSPECT->value,
                'region' => 'Asia',
                'vat_id' => 'AS111222333',
            ],
            [
                'name' => 'Retail Chain Corp',
                'industry' => 'Retail',
                'source' => CompanySource::TRADE_SHOW->value,
                'status' => CompanyStatus::ACTIVE->value,
                'region' => 'North America',
                'vat_id' => 'US444555666',
            ],
            [
                'name' => 'Energy Solutions Inc',
                'industry' => 'Energy',
                'source' => CompanySource::PARTNER->value,
                'status' => CompanyStatus::INACTIVE->value,
                'region' => 'Europe',
                'vat_id' => 'EU777888999',
            ],
            [
                'name' => 'Education Foundation',
                'industry' => 'Education',
                'source' => CompanySource::WEBSITE->value,
                'status' => CompanyStatus::PROSPECT->value,
                'region' => 'North America',
                'vat_id' => null,
            ],
            [
                'name' => 'Logistics Network',
                'industry' => 'Logistics',
                'source' => CompanySource::REFERRAL->value,
                'status' => CompanyStatus::ACTIVE->value,
                'region' => 'Asia',
                'vat_id' => 'AS999000111',
            ],
            [
                'name' => 'Consulting Services',
                'industry' => 'Consulting',
                'source' => CompanySource::OTHER->value,
                'status' => CompanyStatus::PROSPECT->value,
                'region' => 'Europe',
                'vat_id' => null,
            ],
        ];

        foreach ($companies as $companyData) {
            Company::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                ...$companyData,
                'created_by' => $user->id,
            ]);
        }
    }
}
