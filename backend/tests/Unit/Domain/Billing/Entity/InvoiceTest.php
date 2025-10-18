<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Billing\Entity;

use App\Domain\Billing\Entity\Invoice;
use App\Enums\Billing\InvoiceStatus;
use Carbon\Carbon;
use Tests\TestCase;

final class InvoiceTest extends TestCase
{
    public function test_it_creates_invoice_with_required_fields(): void
    {
        $invoice = Invoice::create(
            nr: 'INV-0001',
            contractId: 'contract-123',
            companyId: 'company-456',
            issueDate: Carbon::today(),
            dueDate: Carbon::today()->addDays(30),
            amount: 5000.00,
            currency: 'USD',
            status: InvoiceStatus::ISSUED
        );

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertNotEmpty($invoice->id());
        $this->assertEquals('INV-0001', $invoice->nr());
        $this->assertEquals('contract-123', $invoice->contractId());
        $this->assertEquals('company-456', $invoice->companyId());
        $this->assertEquals(Carbon::today()->toDateString(), $invoice->issueDate()->toDateString());
        $this->assertEquals(Carbon::today()->addDays(30)->toDateString(), $invoice->dueDate()->toDateString());
        $this->assertEquals(5000.00, $invoice->amount());
        $this->assertEquals('USD', $invoice->currency());
        $this->assertEquals(InvoiceStatus::ISSUED, $invoice->status());
        $this->assertInstanceOf(Carbon::class, $invoice->createdAt());
        $this->assertInstanceOf(Carbon::class, $invoice->updatedAt());
        $this->assertNull($invoice->deletedAt());
        $this->assertFalse($invoice->isDeleted());
    }

    public function test_it_creates_invoice_without_contract(): void
    {
        $invoice = Invoice::create(
            nr: 'INV-0002',
            contractId: null,
            companyId: 'company-789',
            issueDate: Carbon::today(),
            dueDate: Carbon::today()->addDays(30),
            amount: 2500.00,
            currency: 'EUR',
            status: InvoiceStatus::ISSUED
        );

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertNull($invoice->contractId());
    }

    public function test_it_uses_uuid7_for_id(): void
    {
        $invoice = Invoice::create(
            nr: 'INV-0003',
            contractId: 'contract-abc',
            companyId: 'company-def',
            issueDate: Carbon::today(),
            dueDate: Carbon::today()->addDays(30),
            amount: 1000.00,
            currency: 'GBP',
            status: InvoiceStatus::PAID
        );

        $id = $invoice->id();
        $this->assertIsString($id);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id);
    }

    public function test_it_sets_created_and_updated_at_to_current_time(): void
    {
        $before = Carbon::now();

        $invoice = Invoice::create(
            nr: 'INV-0004',
            contractId: 'contract-ghi',
            companyId: 'company-jkl',
            issueDate: Carbon::today(),
            dueDate: Carbon::today()->addDays(30),
            amount: 3000.00,
            currency: 'USD',
            status: InvoiceStatus::ISSUED
        );

        $after = Carbon::now();

        $this->assertTrue($invoice->createdAt()->between($before, $after));
        $this->assertTrue($invoice->updatedAt()->between($before, $after));
    }

    public function test_it_handles_all_invoice_statuses(): void
    {
        $statuses = [
            InvoiceStatus::ISSUED,
            InvoiceStatus::PAID,
            InvoiceStatus::OVERDUE,
            InvoiceStatus::CANCELLED,
        ];

        foreach ($statuses as $status) {
            $invoice = Invoice::create(
                nr: 'INV-TEST',
                contractId: 'contract-test',
                companyId: 'company-test',
                issueDate: Carbon::today(),
                dueDate: Carbon::today()->addDays(30),
                amount: 1000.00,
                currency: 'USD',
                status: $status
            );
            $this->assertEquals($status, $invoice->status());
        }
    }

    public function test_it_handles_different_currencies(): void
    {
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CAD'];

        foreach ($currencies as $currency) {
            $invoice = Invoice::create(
                nr: 'INV-CURRENCY',
                contractId: 'contract-currency',
                companyId: 'company-currency',
                issueDate: Carbon::today(),
                dueDate: Carbon::today()->addDays(30),
                amount: 1000.00,
                currency: $currency,
                status: InvoiceStatus::ISSUED
            );
            $this->assertEquals($currency, $invoice->currency());
        }
    }

    public function test_it_determines_invoice_state_correctly(): void
    {
        $paidInvoice = Invoice::create(
            nr: 'INV-PAID',
            contractId: 'contract-paid',
            companyId: 'company-paid',
            issueDate: Carbon::today()->subDays(10),
            dueDate: Carbon::today()->addDays(20),
            amount: 5000.00,
            currency: 'USD',
            status: InvoiceStatus::PAID
        );
        $this->assertTrue($paidInvoice->isPaid());
        $this->assertFalse($paidInvoice->isOverdue());
        $this->assertFalse($paidInvoice->isCancelled());
        $this->assertFalse($paidInvoice->isIssued());

        $overdueInvoice = Invoice::create(
            nr: 'INV-OVERDUE',
            contractId: 'contract-overdue',
            companyId: 'company-overdue',
            issueDate: Carbon::today()->subDays(60),
            dueDate: Carbon::today()->subDays(10),
            amount: 3000.00,
            currency: 'USD',
            status: InvoiceStatus::OVERDUE
        );
        $this->assertFalse($overdueInvoice->isPaid());
        $this->assertTrue($overdueInvoice->isOverdue());
        $this->assertFalse($overdueInvoice->isCancelled());
        $this->assertFalse($overdueInvoice->isIssued());

        $cancelledInvoice = Invoice::create(
            nr: 'INV-CANCELLED',
            contractId: 'contract-cancelled',
            companyId: 'company-cancelled',
            issueDate: Carbon::today()->subDays(5),
            dueDate: Carbon::today()->addDays(25),
            amount: 2000.00,
            currency: 'USD',
            status: InvoiceStatus::CANCELLED
        );
        $this->assertFalse($cancelledInvoice->isPaid());
        $this->assertFalse($cancelledInvoice->isOverdue());
        $this->assertTrue($cancelledInvoice->isCancelled());
        $this->assertFalse($cancelledInvoice->isIssued());

        $issuedInvoice = Invoice::create(
            nr: 'INV-ISSUED',
            contractId: 'contract-issued',
            companyId: 'company-issued',
            issueDate: Carbon::today()->subDays(5),
            dueDate: Carbon::today()->addDays(25),
            amount: 4000.00,
            currency: 'USD',
            status: InvoiceStatus::ISSUED
        );
        $this->assertFalse($issuedInvoice->isPaid());
        $this->assertFalse($issuedInvoice->isOverdue());
        $this->assertFalse($issuedInvoice->isCancelled());
        $this->assertTrue($issuedInvoice->isIssued());
    }

    public function test_it_handles_amount_values(): void
    {
        $invoiceZero = Invoice::create(
            nr: 'INV-ZERO',
            contractId: 'contract-zero',
            companyId: 'company-zero',
            issueDate: Carbon::today(),
            dueDate: Carbon::today()->addDays(30),
            amount: 0.00,
            currency: 'USD',
            status: InvoiceStatus::ISSUED
        );
        $this->assertEquals(0.00, $invoiceZero->amount());

        $invoiceLarge = Invoice::create(
            nr: 'INV-LARGE',
            contractId: 'contract-large',
            companyId: 'company-large',
            issueDate: Carbon::today(),
            dueDate: Carbon::today()->addDays(30),
            amount: 999999.99,
            currency: 'USD',
            status: InvoiceStatus::ISSUED
        );
        $this->assertEquals(999999.99, $invoiceLarge->amount());
    }
}
