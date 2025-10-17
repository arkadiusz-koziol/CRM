<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

readonly class ToolDto implements Arrayable
{
    public function __construct(
        private string $name,
        private string $description,
        private int $count
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'count' => $this->count,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
