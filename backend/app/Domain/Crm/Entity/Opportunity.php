<?php

declare(strict_types=1);

namespace App\Domain\Crm\Entity;

use App\Enums\Crm\OpportunityStatus;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

final readonly class Opportunity
{
    public function __construct(
        private string $id,
        private string $title,
        private string $companyId,
        private ?string $contactId,
        private float $value,
        private string $currency,
        private int $probability,
        private string $stageId,
        private string $ownerUserId,
        private ?Carbon $closeDate,
        private OpportunityStatus $status,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public static function create(
        string $title,
        string $companyId,
        ?string $contactId,
        float $value,
        string $currency,
        int $probability,
        string $stageId,
        string $ownerUserId,
        ?Carbon $closeDate = null
    ): self {
        $now = Carbon::now();

        return new self(
            id: Uuid::uuid7()->toString(),
            title: $title,
            companyId: $companyId,
            contactId: $contactId,
            value: $value,
            currency: $currency,
            probability: $probability,
            stageId: $stageId,
            ownerUserId: $ownerUserId,
            closeDate: $closeDate,
            status: OpportunityStatus::OPEN,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function companyId(): string
    {
        return $this->companyId;
    }

    public function contactId(): ?string
    {
        return $this->contactId;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function probability(): int
    {
        return $this->probability;
    }

    public function stageId(): string
    {
        return $this->stageId;
    }

    public function ownerUserId(): string
    {
        return $this->ownerUserId;
    }

    public function closeDate(): ?Carbon
    {
        return $this->closeDate;
    }

    public function status(): OpportunityStatus
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
}
