<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;

readonly class CreateTaskDto
{
    public function __construct(
        private string $title,
        private string $description,
        private TaskStatus $status,
        private TaskPriority $priority,
        private int $assignedTo,
        private int $createdBy,
        private ?string $dueDate = null,
        private ?float $estimatedHours = null
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getPriority(): TaskPriority
    {
        return $this->priority;
    }

    public function getAssignedTo(): int
    {
        return $this->assignedTo;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    public function getEstimatedHours(): ?float
    {
        return $this->estimatedHours;
    }
}
