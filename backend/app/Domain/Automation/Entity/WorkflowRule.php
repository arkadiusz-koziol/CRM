<?php

declare(strict_types=1);

namespace App\Domain\Automation\Entity;

use Carbon\Carbon;

final class WorkflowRule
{
    public function __construct(
        private string $id,
        private string $workflowId,
        private string $name,
        private string $description,
        private array $conditions,
        private array $actions,
        private int $priority,
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

    public function description(): string
    {
        return $this->description;
    }

    public function conditions(): array
    {
        return $this->conditions;
    }

    public function actions(): array
    {
        return $this->actions;
    }

    public function priority(): int
    {
        return $this->priority;
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

    public function updateDetails(string $name, string $description, array $conditions, array $actions, int $priority): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->conditions = $conditions;
        $this->actions = $actions;
        $this->priority = $priority;
    }

    public static function create(
        string $workflowId,
        string $name,
        string $description,
        array $conditions,
        array $actions,
        int $priority = 0
    ): self {
        return new self(
            id: \Ramsey\Uuid\Uuid::uuid7()->toString(),
            workflowId: $workflowId,
            name: $name,
            description: $description,
            conditions: $conditions,
            actions: $actions,
            priority: $priority,
            isActive: true,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );
    }
}
