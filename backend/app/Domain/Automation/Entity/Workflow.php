<?php

declare(strict_types=1);

namespace App\Domain\Automation\Entity;

use Carbon\Carbon;

final class Workflow
{
    public function __construct(
        private string $id,
        private string $name,
        private string $description,
        private bool $isActive,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isActive(): bool
    {
        return $this->isActive;
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

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function updateDetails(string $name, string $description): void
    {
        $this->name = $name;
        $this->description = $description;
    }

    public static function create(string $name, string $description): self
    {
        return new self(
            id: \Ramsey\Uuid\Uuid::uuid7()->toString(),
            name: $name,
            description: $description,
            isActive: true,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );
    }
}
