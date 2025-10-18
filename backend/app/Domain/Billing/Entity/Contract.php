<?php

declare(strict_types=1);

namespace App\Domain\Billing\Entity;

use App\Enums\Billing\ContractStatus;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

final readonly class Contract
{
    public function __construct(
        private string $id,
        private string $nr,
        private string $companyId,
        private Carbon $startAt,
        private Carbon $endAt,
        private float $amount,
        private string $currency,
        private ContractStatus $status,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public static function create(
        string $nr,
        string $companyId,
        Carbon $startAt,
        Carbon $endAt,
        float $amount,
        string $currency,
        ContractStatus $status
    ): self {
        $now = Carbon::now();

        return new self(
            id: Uuid::uuid7()->toString(),
            nr: $nr,
            companyId: $companyId,
            startAt: $startAt,
            endAt: $endAt,
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

    public function status(): ContractStatus
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

    public function isActive(): bool
    {
        return $this->status === ContractStatus::ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this->status === ContractStatus::EXPIRED || $this->endAt->isPast();
    }

    public function isTerminated(): bool
    {
        return $this->status === ContractStatus::TERMINATED;
    }
}
