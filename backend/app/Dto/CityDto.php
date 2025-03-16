<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class CityDto implements Arrayable
{
    public function __construct(
        public string $name,
        public string $district,
        public string $commune,
        public string $voivodeship
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'district' => $this->district,
            'commune' => $this->commune,
            'voivodeship' => $this->voivodeship,
        ];
    }
}
