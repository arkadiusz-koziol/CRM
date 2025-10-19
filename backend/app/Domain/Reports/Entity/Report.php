<?php

declare(strict_types=1);

namespace App\Domain\Reports\Entity;

use Carbon\Carbon;

final class Report
{
    public function __construct(
        private string $id,
        private string $name,
        private ?string $description,
        private string $source,
        private array $columns,
        private ?array $filters,
        private ?array $sorting,
        private string $createdBy,
        private bool $isPublic,
        private Carbon $createdAt,
        private Carbon $updatedAt,
        private ?Carbon $deletedAt = null,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

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

    public function createdBy(): string
    {
        return $this->createdBy;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
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

    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    public function updateDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function updateColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    public function updateFilters(?array $filters): void
    {
        $this->filters = $filters;
    }

    public function updateSorting(?array $sorting): void
    {
        $this->sorting = $sorting;
    }

    public function updateVisibility(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    public function delete(): void
    {
        $this->deletedAt = Carbon::now();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }

    public static function create(
        string $id,
        string $name,
        ?string $description,
        string $source,
        array $columns,
        ?array $filters,
        ?array $sorting,
        string $createdBy,
        bool $isPublic = false,
    ): self {
        $now = Carbon::now();

        return new self(
            $id,
            $name,
            $description,
            $source,
            $columns,
            $filters,
            $sorting,
            $createdBy,
            $isPublic,
            $now,
            $now,
        );
    }

    public static function reconstitute(
        string $id,
        string $name,
        ?string $description,
        string $source,
        array $columns,
        ?array $filters,
        ?array $sorting,
        string $createdBy,
        bool $isPublic,
        Carbon $createdAt,
        Carbon $updatedAt,
        ?Carbon $deletedAt = null,
    ): self {
        return new self(
            $id,
            $name,
            $description,
            $source,
            $columns,
            $filters,
            $sorting,
            $createdBy,
            $isPublic,
            $createdAt,
            $updatedAt,
            $deletedAt,
        );
    }
}
