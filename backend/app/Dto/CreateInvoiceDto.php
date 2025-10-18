<?php

declare(strict_types=1);

namespace App\Dto;

use Carbon\Carbon;

final readonly class CreateInvoiceDto
{
    public function __construct(
        private string $number,
        private string $companyId,
        private Carbon $issueDate,
        private Carbon $dueDate,
        private float $amount,
        private string $currency,
        private string $status,
        private ?string $contractId = null
    ) {}

    public function number(): string
    {
        return $this->number;
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

    public function status(): string
    {
        return $this->status;
    }

    public function contractId(): ?string
    {
        return $this->contractId;
    }
}
