<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class MaterialDto implements Arrayable
{
    public function __construct(
        public string $name,
        public string $description,
        public int $count,
        public int $price) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'count' => $this->count,
            'price' => $this->price,
        ];
    }
}
