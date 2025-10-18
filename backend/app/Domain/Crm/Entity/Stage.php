<?php

declare(strict_types=1);

namespace App\Domain\Crm\Entity;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

final readonly class Stage
{
    public function __construct(
        private string $id,
        private string $pipelineId,
        private string $name,
        private ?string $description,
        private int $order,
        private bool $isFinal,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public static function create(
        string $pipelineId,
        string $name,
        ?string $description,
        int $order,
        bool $isFinal = false
    ): self {
        $now = Carbon::now();

        return new self(
            id: Uuid::uuid7()->toString(),
            pipelineId: $pipelineId,
            name: $name,
            description: $description,
            order: $order,
            isFinal: $isFinal,
            createdAt: $now,
            updatedAt: $now
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function pipelineId(): string
    {
        return $this->pipelineId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
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
