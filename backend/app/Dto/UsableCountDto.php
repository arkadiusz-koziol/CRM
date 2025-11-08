<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class UsableCountDto
{
    public function __construct(
        private string $entity,
        private int $count
    ) {}

    public function entity(): string
    {
        return $this->entity;
    }

    public function count(): int
    {
        return $this->count;
    }
}

