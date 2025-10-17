<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

readonly class CityDto implements Arrayable
{
    public function __construct(
        private string $name,
        private string $district,
        private string $commune,
        private string $voivodeship
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'district' => $this->district,
            'commune' => $this->commune,
            'voivodeship' => $this->voivodeship,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getCommune(): string
    {
        return $this->commune;
    }

    public function getVoivodeship(): string
    {
        return $this->voivodeship;
    }
}
