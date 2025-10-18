<?php

declare(strict_types=1);

namespace App\Domain\Crm\Entity;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

final readonly class Pipeline
{
    public function __construct(
        private string $id,
        private string $name,
        private ?string $description,
        private bool $isDefault,
        private string $createdBy,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public static function create(
        string $name,
        ?string $description,
        bool $isDefault,
        string $createdBy
    ): self {
        $now = Carbon::now();

        return new self(
            id: Uuid::uuid7()->toString(),
            name: $name,
            description: $description,
            isDefault: $isDefault,
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

    public function description(): ?string
    {
        return $this->description;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
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
