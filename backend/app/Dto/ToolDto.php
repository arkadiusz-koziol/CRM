<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class ToolDto implements Arrayable
{
    public string $name;
    public string $description;
    public int $count;

    public function __construct(string $name, string $description, int $count)
    {
        $this->name = $name;
        $this->description = $description;
        $this->count = $count;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'count' => $this->count,
        ];
    }
}
