<?php

declare(strict_types=1);

namespace App\Domain\TrainingCategory\Entity;

use Carbon\Carbon;

final readonly class TrainingCategory
{
    public function __construct(
        public string $id,
        public string $name,
        public Carbon $createdAt,
        public Carbon $updatedAt,
        public ?Carbon $deletedAt = null,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
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
}
