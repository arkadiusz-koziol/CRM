<?php

declare(strict_types=1);

namespace App\Domain\Automation\Entity;

use Carbon\Carbon;

final class WorkflowAction
{
    public function __construct(
        private string $id,
        private string $workflowId,
        private string $name,
        private string $type,
        private array $config,
        private bool $isActive,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function workflowId(): string
    {
        return $this->workflowId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function config(): array
    {
        return $this->config;
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

    public function updateConfig(array $config): void
    {
        $this->config = $config;
    }

    public static function create(
        string $workflowId,
        string $name,
        string $type,
        array $config
    ): self {
        return new self(
            id: \Ramsey\Uuid\Uuid::uuid7()->toString(),
            workflowId: $workflowId,
            name: $name,
            type: $type,
            config: $config,
            isActive: true,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );
    }
}
