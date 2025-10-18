<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm\Entity;

use App\Domain\Crm\Entity\Company;
use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use Carbon\Carbon;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    public function test_it_creates_company_with_required_fields(): void
    {
        $company = Company::create(
            name: 'TechCorp Solutions',
            industry: 'Technology',
            source: CompanySource::WEBSITE,
            status: CompanyStatus::ACTIVE,
            region: 'North America',
            vatId: 'US123456789',
            createdBy: 'user-123'
        );

        $this->assertInstanceOf(Company::class, $company);
        $this->assertNotEmpty($company->id());
        $this->assertEquals('TechCorp Solutions', $company->name());
        $this->assertEquals('Technology', $company->industry());
        $this->assertEquals(CompanySource::WEBSITE, $company->source());
        $this->assertEquals(CompanyStatus::ACTIVE, $company->status());
        $this->assertEquals('North America', $company->region());
        $this->assertEquals('US123456789', $company->vatId());
        $this->assertEquals('user-123', $company->createdBy());
        $this->assertInstanceOf(Carbon::class, $company->createdAt());
        $this->assertInstanceOf(Carbon::class, $company->updatedAt());
        $this->assertNull($company->deletedAt());
        $this->assertFalse($company->isDeleted());
    }

    public function test_it_creates_company_with_minimal_fields(): void
    {
        $company = Company::create(
            name: 'Minimal Corp',
            industry: null,
            source: CompanySource::REFERRAL,
            status: CompanyStatus::PROSPECT,
            region: null,
            vatId: null,
            createdBy: 'user-456'
        );

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals('Minimal Corp', $company->name());
        $this->assertNull($company->industry());
        $this->assertEquals(CompanySource::REFERRAL, $company->source());
        $this->assertEquals(CompanyStatus::PROSPECT, $company->status());
        $this->assertNull($company->region());
        $this->assertNull($company->vatId());
        $this->assertEquals('user-456', $company->createdBy());
    }

    public function test_it_uses_uuid7_for_id(): void
    {
        $company = Company::create(
            name: 'Test Corp',
            industry: null,
            source: CompanySource::WEBSITE,
            status: CompanyStatus::ACTIVE,
            region: null,
            vatId: null,
            createdBy: 'user-789'
        );

        $id = $company->id();
        $this->assertIsString($id);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id);
    }

    public function test_it_sets_created_and_updated_at_to_current_time(): void
    {
        $before = Carbon::now();

        $company = Company::create(
            name: 'Time Test Corp',
            industry: null,
            source: CompanySource::WEBSITE,
            status: CompanyStatus::ACTIVE,
            region: null,
            vatId: null,
            createdBy: 'user-time'
        );

        $after = Carbon::now();

        $this->assertTrue($company->createdAt()->between($before, $after));
        $this->assertTrue($company->updatedAt()->between($before, $after));
    }

    public function test_it_handles_all_company_sources(): void
    {
        $sources = [
            CompanySource::WEBSITE,
            CompanySource::REFERRAL,
            CompanySource::SOCIAL_MEDIA,
            CompanySource::EMAIL_CAMPAIGN,
            CompanySource::COLD_CALL,
            CompanySource::TRADE_SHOW,
            CompanySource::PARTNER,
            CompanySource::OTHER,
        ];

        foreach ($sources as $source) {
            $company = Company::create(
                name: 'Test Corp',
                industry: null,
                source: $source,
                status: CompanyStatus::ACTIVE,
                region: null,
                vatId: null,
                createdBy: 'user-test'
            );

            $this->assertEquals($source, $company->source());
        }
    }

    public function test_it_handles_all_company_statuses(): void
    {
        $statuses = [
            CompanyStatus::ACTIVE,
            CompanyStatus::INACTIVE,
            CompanyStatus::PROSPECT,
        ];

        foreach ($statuses as $status) {
            $company = Company::create(
                name: 'Test Corp',
                industry: null,
                source: CompanySource::WEBSITE,
                status: $status,
                region: null,
                vatId: null,
                createdBy: 'user-test'
            );

            $this->assertEquals($status, $company->status());
        }
    }
}
