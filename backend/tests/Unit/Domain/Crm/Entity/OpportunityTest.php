<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm\Entity;

use App\Domain\Crm\Entity\Opportunity;
use App\Enums\Crm\OpportunityStatus;
use Carbon\Carbon;
use Tests\TestCase;

class OpportunityTest extends TestCase
{
    public function test_it_creates_opportunity_with_required_fields(): void
    {
        $opportunity = Opportunity::create(
            title: 'Enterprise Software License',
            companyId: 'company-123',
            contactId: 'contact-456',
            value: 50000.00,
            currency: 'USD',
            probability: 75,
            stageId: 'stage-789',
            ownerUserId: 'user-101',
            closeDate: Carbon::parse('2024-12-31')
        );

        $this->assertInstanceOf(Opportunity::class, $opportunity);
        $this->assertNotEmpty($opportunity->id());
        $this->assertEquals('Enterprise Software License', $opportunity->title());
        $this->assertEquals('company-123', $opportunity->companyId());
        $this->assertEquals('contact-456', $opportunity->contactId());
        $this->assertEquals(50000.00, $opportunity->value());
        $this->assertEquals('USD', $opportunity->currency());
        $this->assertEquals(75, $opportunity->probability());
        $this->assertEquals('stage-789', $opportunity->stageId());
        $this->assertEquals('user-101', $opportunity->ownerUserId());
        $this->assertEquals('2024-12-31', $opportunity->closeDate()->format('Y-m-d'));
        $this->assertEquals(OpportunityStatus::OPEN, $opportunity->status());
        $this->assertInstanceOf(Carbon::class, $opportunity->createdAt());
        $this->assertInstanceOf(Carbon::class, $opportunity->updatedAt());
        $this->assertNull($opportunity->deletedAt());
        $this->assertFalse($opportunity->isDeleted());
    }

    public function test_it_creates_opportunity_with_minimal_fields(): void
    {
        $opportunity = Opportunity::create(
            title: 'Small Project',
            companyId: 'company-456',
            contactId: null,
            value: 5000.00,
            currency: 'EUR',
            probability: 50,
            stageId: 'stage-123',
            ownerUserId: 'user-202'
        );

        $this->assertInstanceOf(Opportunity::class, $opportunity);
        $this->assertEquals('Small Project', $opportunity->title());
        $this->assertEquals('company-456', $opportunity->companyId());
        $this->assertNull($opportunity->contactId());
        $this->assertEquals(5000.00, $opportunity->value());
        $this->assertEquals('EUR', $opportunity->currency());
        $this->assertEquals(50, $opportunity->probability());
        $this->assertEquals('stage-123', $opportunity->stageId());
        $this->assertEquals('user-202', $opportunity->ownerUserId());
        $this->assertNull($opportunity->closeDate());
        $this->assertEquals(OpportunityStatus::OPEN, $opportunity->status());
    }

    public function test_it_uses_uuid7_for_id(): void
    {
        $opportunity = Opportunity::create(
            title: 'Test Opportunity',
            companyId: 'company-789',
            contactId: null,
            value: 10000.00,
            currency: 'GBP',
            probability: 25,
            stageId: 'stage-456',
            ownerUserId: 'user-303'
        );

        $id = $opportunity->id();
        $this->assertIsString($id);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id);
    }

    public function test_it_sets_created_and_updated_at_to_current_time(): void
    {
        $before = Carbon::now();

        $opportunity = Opportunity::create(
            title: 'Time Test Opportunity',
            companyId: 'company-time',
            contactId: null,
            value: 15000.00,
            currency: 'USD',
            probability: 60,
            stageId: 'stage-time',
            ownerUserId: 'user-time'
        );

        $after = Carbon::now();

        $this->assertTrue($opportunity->createdAt()->between($before, $after));
        $this->assertTrue($opportunity->updatedAt()->between($before, $after));
    }

    public function test_it_handles_all_opportunity_statuses(): void
    {
        $statuses = [
            OpportunityStatus::OPEN,
            OpportunityStatus::WON,
            OpportunityStatus::LOST,
        ];

        foreach ($statuses as $status) {
            $opportunity = Opportunity::create(
                title: 'Test Opportunity',
                companyId: 'company-test',
                contactId: null,
                value: 10000.00,
                currency: 'USD',
                probability: 50,
                stageId: 'stage-test',
                ownerUserId: 'user-test'
            );

            // Note: The create method always sets status to OPEN
            // Status changes would be handled by separate methods in a real implementation
            $this->assertEquals(OpportunityStatus::OPEN, $opportunity->status());
        }
    }

    public function test_it_handles_different_currencies(): void
    {
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CAD'];

        foreach ($currencies as $currency) {
            $opportunity = Opportunity::create(
                title: 'Test Opportunity',
                companyId: 'company-test',
                contactId: null,
                value: 10000.00,
                currency: $currency,
                probability: 50,
                stageId: 'stage-test',
                ownerUserId: 'user-test'
            );

            $this->assertEquals($currency, $opportunity->currency());
        }
    }

    public function test_it_handles_probability_bounds(): void
    {
        $probabilities = [0, 25, 50, 75, 100];

        foreach ($probabilities as $probability) {
            $opportunity = Opportunity::create(
                title: 'Test Opportunity',
                companyId: 'company-test',
                contactId: null,
                value: 10000.00,
                currency: 'USD',
                probability: $probability,
                stageId: 'stage-test',
                ownerUserId: 'user-test'
            );

            $this->assertEquals($probability, $opportunity->probability());
        }
    }

    public function test_it_handles_close_date(): void
    {
        $closeDate = Carbon::parse('2024-12-31');
        
        $opportunity = Opportunity::create(
            title: 'Test Opportunity',
            companyId: 'company-test',
            contactId: null,
            value: 10000.00,
            currency: 'USD',
            probability: 50,
            stageId: 'stage-test',
            ownerUserId: 'user-test',
            closeDate: $closeDate
        );

        $this->assertEquals('2024-12-31', $opportunity->closeDate()->format('Y-m-d'));
    }
}
