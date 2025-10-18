<?php

declare(strict_types=1);

namespace App\Dto;

use Carbon\Carbon;

final readonly class CreateContractDto
{
    public function __construct(
        private string $number,
        private string $companyId,
        private Carbon $startAt,
        private Carbon $endAt,
        private float $amount,
        private string $currency,
        private string $status
    ) {}

    public function number(): string
    {
        return $this->number;
    }

    public function companyId(): string
    {
        return $this->companyId;
    }

    public function startAt(): Carbon
    {
        return $this->startAt;
    }

    public function endAt(): Carbon
    {
        return $this->endAt;
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
}
