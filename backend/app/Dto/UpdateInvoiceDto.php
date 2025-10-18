<?php

declare(strict_types=1);

namespace App\Dto;

use Carbon\Carbon;

final readonly class UpdateInvoiceDto
{
    public function __construct(
        private string $id,
        private ?string $number = null,
        private ?string $companyId = null,
        private ?Carbon $issueDate = null,
        private ?Carbon $dueDate = null,
        private ?float $amount = null,
        private ?string $currency = null,
        private ?string $status = null,
        private ?string $contractId = null
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function number(): ?string
    {
        return $this->number;
    }

    public function companyId(): ?string
    {
        return $this->companyId;
    }

    public function issueDate(): ?Carbon
    {
        return $this->issueDate;
    }

    public function dueDate(): ?Carbon
    {
        return $this->dueDate;
    }

    public function amount(): ?float
    {
        return $this->amount;
    }

    public function currency(): ?string
    {
        return $this->currency;
    }

    public function status(): ?string
    {
        return $this->status;
    }

    public function contractId(): ?string
    {
        return $this->contractId;
    }
}
