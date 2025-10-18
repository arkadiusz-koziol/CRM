<?php

declare(strict_types=1);

namespace App\Dto;

use Carbon\Carbon;

final readonly class UpdateContractDto
{
    public function __construct(
        private string $id,
        private ?string $number = null,
        private ?string $companyId = null,
        private ?Carbon $startAt = null,
        private ?Carbon $endAt = null,
        private ?float $amount = null,
        private ?string $currency = null,
        private ?string $status = null
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

    public function startAt(): ?Carbon
    {
        return $this->startAt;
    }

    public function endAt(): ?Carbon
    {
        return $this->endAt;
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
}
