<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Billing\Entity\Invoice;
use App\Dto\CreateInvoiceDto;
use App\Dto\UpdateInvoiceDto;
use App\Enums\Billing\InvoiceStatus;
use App\Events\InvoiceOverdue;
use App\Events\InvoicePaid;
use App\Interfaces\Repositories\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;

final class InvoiceService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private LoggerInterface $logger
    ) {}

    public function create(CreateInvoiceDto $dto): string
    {
        $invoice = Invoice::create(
            nr: $dto->number(),
            companyId: $dto->companyId(),
            issueDate: $dto->issueDate(),
            dueDate: $dto->dueDate(),
            amount: $dto->amount(),
            currency: $dto->currency(),
            status: InvoiceStatus::from($dto->status()),
            contractId: $dto->contractId()
        );

        $this->invoiceRepository->save($invoice);

        if ($invoice->status() === InvoiceStatus::PAID) {
            event(new InvoicePaid($invoice->id()));
        }

        return $invoice->id();
    }

    public function update(UpdateInvoiceDto $dto): string
    {
        $invoice = $this->invoiceRepository->findById($dto->id());

        if (! $invoice) {
            throw new \RuntimeException("Invoice with ID {$dto->id()} not found");
        }

        $updatedInvoice = new Invoice(
            id: $invoice->id(),
            nr: $dto->number() ?? $invoice->nr(),
            companyId: $dto->companyId() ?? $invoice->companyId(),
            issueDate: $dto->issueDate() ?? $invoice->issueDate(),
            dueDate: $dto->dueDate() ?? $invoice->dueDate(),
            amount: $dto->amount() ?? $invoice->amount(),
            currency: $dto->currency() ?? $invoice->currency(),
            status: $dto->status() ? InvoiceStatus::from($dto->status()) : $invoice->status(),
            createdAt: $invoice->createdAt(),
            updatedAt: Carbon::now(),
            contractId: $dto->contractId() ?? $invoice->contractId(),
            deletedAt: $invoice->deletedAt()
        );

        $this->invoiceRepository->update($updatedInvoice);

        if ($updatedInvoice->status() === InvoiceStatus::PAID && $invoice->status() !== InvoiceStatus::PAID) {
            event(new InvoicePaid($updatedInvoice->id()));
        }

        if ($updatedInvoice->status() === InvoiceStatus::OVERDUE && $invoice->status() !== InvoiceStatus::OVERDUE) {
            event(new InvoiceOverdue($updatedInvoice->id()));
        }

        return $updatedInvoice->id();
    }

    public function findById(string $id): ?Invoice
    {
        return $this->invoiceRepository->findById($id);
    }

    public function findByNumber(string $number): ?Invoice
    {
        return $this->invoiceRepository->findByNumber($number);
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->invoiceRepository->findAll($filters, $limit, $offset);
    }

    public function delete(string $id): void
    {
        $invoice = $this->invoiceRepository->findById($id);

        if (! $invoice) {
            throw new \RuntimeException("Invoice with ID {$id} not found");
        }

        $this->invoiceRepository->delete($id);
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        $validStatuses = array_column(InvoiceStatus::cases(), 'value');

        if (! in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        return $this->invoiceRepository->bulkUpdateStatus($ids, $status);
    }

    public function findByCompanyId(string $companyId): array
    {
        return $this->invoiceRepository->findByCompanyId($companyId);
    }

    public function findByContractId(string $contractId): array
    {
        return $this->invoiceRepository->findByContractId($contractId);
    }

    public function findOverdue(): array
    {
        return $this->invoiceRepository->findOverdue();
    }

    public function findDueSoon(int $days = 7): array
    {
        return $this->invoiceRepository->findDueSoon($days);
    }
}
