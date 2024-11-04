<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class CityDto implements Arrayable
{
    public string $name;
    public string $district;
    public string $commune;
    public string $voivodeship;


    public function __construct(string $name, string $district, string $commune, string $voivodeship)
    {
        $this->name = $name;
        $this->district = $district;
        $this->commune = $commune;
        $this->voivodeship = $voivodeship;
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
