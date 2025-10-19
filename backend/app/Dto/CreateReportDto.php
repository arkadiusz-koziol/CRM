<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class CreateReportDto
{
    public function __construct(
        private string $name,
        private ?string $description,
        private string $source,
        private array $columns,
        private ?array $filters,
        private ?array $sorting,
        private bool $isPublic,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function columns(): array
    {
        return $this->columns;
    }

    public function filters(): ?array
    {
        return $this->filters;
    }

    public function sorting(): ?array
    {
        return $this->sorting;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }
}
