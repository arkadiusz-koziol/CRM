<?php

declare(strict_types=1);

namespace App\Domain\Reports\Entity;

use Carbon\Carbon;

final class ReportFilter
{
    public function __construct(
        private string $id,
        private string $reportId,
        private string $field,
        private string $operator,
        private array $value,
        private int $order,
        private Carbon $createdAt,
        private Carbon $updatedAt,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function reportId(): string
    {
        return $this->reportId;
    }

    public function field(): string
    {
        return $this->field;
    }

    public function operator(): string
    {
        return $this->operator;
    }

    public function value(): array
    {
        return $this->value;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function createdAt(): Carbon
    {
        return $this->createdAt;
    }

    public function updatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function updateField(string $field): void
    {
        $this->field = $field;
    }

    public function updateOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    public function updateValue(array $value): void
    {
        $this->value = $value;
    }

    public function updateOrder(int $order): void
    {
        $this->order = $order;
    }

    public static function create(
        string $id,
        string $reportId,
        string $field,
        string $operator,
        array $value,
        int $order = 0,
    ): self {
        $now = Carbon::now();

        return new self(
            $id,
            $reportId,
            $field,
            $operator,
            $value,
            $order,
            $now,
            $now,
        );
    }

    public static function reconstitute(
        string $id,
        string $reportId,
        string $field,
        string $operator,
        array $value,
        int $order,
        Carbon $createdAt,
        Carbon $updatedAt,
    ): self {
        return new self(
            $id,
            $reportId,
            $field,
            $operator,
            $value,
            $order,
            $createdAt,
            $updatedAt,
        );
    }
}
