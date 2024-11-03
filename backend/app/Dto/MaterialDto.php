<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class MaterialDto implements Arrayable
{
    public string $name;
    public string $description;
    public int $count;
    public int $price;

    public function __construct(string $name, string $description, int $count, int $price)
    {
        $this->name = $name;
        $this->description = $description;
        $this->count = $count;
        $this->price = $price;
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
