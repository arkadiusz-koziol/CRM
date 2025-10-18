<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class TrainingCategoryDto
{
    public function __construct(
        private string $name,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
