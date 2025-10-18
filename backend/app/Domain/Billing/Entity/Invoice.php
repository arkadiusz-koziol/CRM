<?php

declare(strict_types=1);

namespace App\Domain\Billing\Entity;

use App\Enums\Billing\InvoiceStatus;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

final readonly class Invoice
{
    public function __construct(
        private string $id,
        private string $nr,
        private ?string $contractId,
        private string $companyId,
        private Carbon $issueDate,
        private Carbon $dueDate,
        private float $amount,
        private string $currency,
        private InvoiceStatus $status,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public static function create(
        string $nr,
        ?string $contractId,
        string $companyId,
        Carbon $issueDate,
        Carbon $dueDate,
        float $amount,
        string $currency,
        InvoiceStatus $status
    ): self {
        $now = Carbon::now();

        return new self(
            id: Uuid::uuid7()->toString(),
            nr: $nr,
            contractId: $contractId,
            companyId: $companyId,
            issueDate: $issueDate,
            dueDate: $dueDate,
            amount: $amount,
            currency: $currency,
            status: $status,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function nr(): string
    {
        return $this->nr;
    }

    public function contractId(): ?string
    {
        return $this->contractId;
    }

    public function companyId(): string
    {
        return $this->companyId;
    }

    public function issueDate(): Carbon
    {
        return $this->issueDate;
    }

    public function dueDate(): Carbon
    {
        return $this->dueDate;
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function status(): InvoiceStatus
    {
        return $this->status;
    }

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }

    public function updatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?Carbon
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::PAID;
    }

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::OVERDUE ||
               ($this->status === InvoiceStatus::ISSUED && $this->dueDate->isPast());
    }

    public function isCancelled(): bool
    {
        return $this->status === InvoiceStatus::CANCELLED;
    }

    public function isIssued(): bool
    {
        return $this->status === InvoiceStatus::ISSUED;
    }
}
