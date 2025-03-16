<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class ToolDto implements Arrayable
{
    public function __construct(
        public string $name,
        public string $description,
        public int $count
    ){
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
