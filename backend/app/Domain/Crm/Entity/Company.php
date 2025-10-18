<?php

declare(strict_types=1);

namespace App\Domain\Crm\Entity;

use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

final readonly class Company
{
    public function __construct(
        private string $id,
        private string $name,
        private ?string $industry,
        private CompanySource $source,
        private CompanyStatus $status,
        private ?string $region,
        private ?string $vatId,
        private string $createdBy,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public static function create(
        string $name,
        ?string $industry,
        CompanySource $source,
        CompanyStatus $status,
        ?string $region,
        ?string $vatId,
        string $createdBy
    ): self {
        $now = Carbon::now();

        return new self(
            id: Uuid::uuid7()->toString(),
            name: $name,
            industry: $industry,
            source: $source,
            status: $status,
            region: $region,
            vatId: $vatId,
            createdBy: $createdBy,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function industry(): ?string
    {
        return $this->industry;
    }

    public function source(): CompanySource
    {
        return $this->source;
    }

    public function status(): CompanyStatus
    {
        return $this->status;
    }

    public function region(): ?string
    {
        return $this->region;
    }

    public function vatId(): ?string
    {
        return $this->vatId;
    }

    public function createdBy(): string
    {
        return $this->createdBy;
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
