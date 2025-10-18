<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Billing\Entity;

use App\Domain\Billing\Entity\Contract;
use App\Enums\Billing\ContractStatus;
use Carbon\Carbon;
use Tests\TestCase;

final class ContractTest extends TestCase
{
    public function test_it_creates_contract_with_required_fields(): void
    {
        $contract = Contract::create(
            nr: 'CON-0001',
            companyId: 'company-123',
            startAt: Carbon::today(),
            endAt: Carbon::today()->addYear(),
            amount: 50000.00,
            currency: 'USD',
            status: ContractStatus::ACTIVE
        );

        $this->assertInstanceOf(Contract::class, $contract);
        $this->assertNotEmpty($contract->id());
        $this->assertEquals('CON-0001', $contract->nr());
        $this->assertEquals('company-123', $contract->companyId());
        $this->assertEquals(Carbon::today()->toDateString(), $contract->startAt()->toDateString());
        $this->assertEquals(Carbon::today()->addYear()->toDateString(), $contract->endAt()->toDateString());
        $this->assertEquals(50000.00, $contract->amount());
        $this->assertEquals('USD', $contract->currency());
        $this->assertEquals(ContractStatus::ACTIVE, $contract->status());
        $this->assertInstanceOf(Carbon::class, $contract->createdAt());
        $this->assertInstanceOf(Carbon::class, $contract->updatedAt());
        $this->assertNull($contract->deletedAt());
        $this->assertFalse($contract->isDeleted());
    }

    public function test_it_uses_uuid7_for_id(): void
    {
        $contract = Contract::create(
            nr: 'CON-0002',
            companyId: 'company-456',
            startAt: Carbon::today(),
            endAt: Carbon::today()->addYear(),
            amount: 25000.00,
            currency: 'EUR',
            status: ContractStatus::DRAFT
        );

        $id = $contract->id();
        $this->assertIsString($id);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id);
    }

    public function test_it_sets_created_and_updated_at_to_current_time(): void
    {
        $before = Carbon::now();

        $contract = Contract::create(
            nr: 'CON-0003',
            companyId: 'company-789',
            startAt: Carbon::today(),
            endAt: Carbon::today()->addYear(),
            amount: 75000.00,
            currency: 'GBP',
            status: ContractStatus::ACTIVE
        );

        $after = Carbon::now();

        $this->assertTrue($contract->createdAt()->between($before, $after));
        $this->assertTrue($contract->updatedAt()->between($before, $after));
    }

    public function test_it_handles_all_contract_statuses(): void
    {
        $statuses = [
            ContractStatus::DRAFT,
            ContractStatus::ACTIVE,
            ContractStatus::EXPIRED,
            ContractStatus::TERMINATED,
        ];

        foreach ($statuses as $status) {
            $contract = Contract::create(
                nr: 'CON-TEST',
                companyId: 'company-test',
                startAt: Carbon::today(),
                endAt: Carbon::today()->addYear(),
                amount: 10000.00,
                currency: 'USD',
                status: $status
            );
            $this->assertEquals($status, $contract->status());
        }
    }

    public function test_it_handles_different_currencies(): void
    {
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CAD'];

        foreach ($currencies as $currency) {
            $contract = Contract::create(
                nr: 'CON-CURRENCY',
                companyId: 'company-currency',
                startAt: Carbon::today(),
                endAt: Carbon::today()->addYear(),
                amount: 10000.00,
                currency: $currency,
                status: ContractStatus::ACTIVE
            );
            $this->assertEquals($currency, $contract->currency());
        }
    }

    public function test_it_determines_contract_state_correctly(): void
    {
        $activeContract = Contract::create(
            nr: 'CON-ACTIVE',
            companyId: 'company-active',
            startAt: Carbon::today()->subMonth(),
            endAt: Carbon::today()->addYear(),
            amount: 50000.00,
            currency: 'USD',
            status: ContractStatus::ACTIVE
        );
        $this->assertTrue($activeContract->isActive());
        $this->assertFalse($activeContract->isExpired());
        $this->assertFalse($activeContract->isTerminated());

        $expiredContract = Contract::create(
            nr: 'CON-EXPIRED',
            companyId: 'company-expired',
            startAt: Carbon::today()->subYear(),
            endAt: Carbon::today()->subMonth(),
            amount: 50000.00,
            currency: 'USD',
            status: ContractStatus::EXPIRED
        );
        $this->assertFalse($expiredContract->isActive());
        $this->assertTrue($expiredContract->isExpired());
        $this->assertFalse($expiredContract->isTerminated());

        $terminatedContract = Contract::create(
            nr: 'CON-TERMINATED',
            companyId: 'company-terminated',
            startAt: Carbon::today()->subMonth(),
            endAt: Carbon::today()->addYear(),
            amount: 50000.00,
            currency: 'USD',
            status: ContractStatus::TERMINATED
        );
        $this->assertFalse($terminatedContract->isActive());
        $this->assertFalse($terminatedContract->isExpired());
        $this->assertTrue($terminatedContract->isTerminated());
    }

    public function test_it_handles_amount_values(): void
    {
        $contractZero = Contract::create(
            nr: 'CON-ZERO',
            companyId: 'company-zero',
            startAt: Carbon::today(),
            endAt: Carbon::today()->addYear(),
            amount: 0.00,
            currency: 'USD',
            status: ContractStatus::DRAFT
        );
        $this->assertEquals(0.00, $contractZero->amount());

        $contractLarge = Contract::create(
            nr: 'CON-LARGE',
            companyId: 'company-large',
            startAt: Carbon::today(),
            endAt: Carbon::today()->addYear(),
            amount: 999999.99,
            currency: 'USD',
            status: ContractStatus::ACTIVE
        );
        $this->assertEquals(999999.99, $contractLarge->amount());
    }
}
