<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;

readonly class UpdateTaskDto
{
    public function __construct(
        private ?string $title = null,
        private ?string $description = null,
        private ?TaskStatus $status = null,
        private ?TaskPriority $priority = null,
        private ?int $assignedTo = null,
        private ?string $dueDate = null,
        private ?float $estimatedHours = null,
        private ?float $actualHours = null
    ) {}

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getStatus(): ?TaskStatus
    {
        return $this->status;
    }

    public function getPriority(): ?TaskPriority
    {
        return $this->priority;
    }

    public function getAssignedTo(): ?int
    {
        return $this->assignedTo;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    public function getEstimatedHours(): ?float
    {
        return $this->estimatedHours;
    }

    public function getActualHours(): ?float
    {
        return $this->actualHours;
    }

    public function hasChanges(): bool
    {
        return $this->title !== null ||
            $this->description !== null ||
            $this->status !== null ||
            $this->priority !== null ||
            $this->assignedTo !== null ||
            $this->dueDate !== null ||
            $this->estimatedHours !== null ||
            $this->actualHours !== null;
    }
}
