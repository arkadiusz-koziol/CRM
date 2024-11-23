<?php

namespace App\Dto;

use App\Models\City;
use Illuminate\Contracts\Support\Arrayable;

class EstateDto implements Arrayable
{
    public string $name;
    public string $custom_id;
    public string $street;
    public string $postal_code;
    public City $city;
    public string $house_number;

    public function __construct(
        string $name,
        string $custom_id,
        string $street,
        string $postal_code,
        City $city,
        string $house_number
    ) {
        $this->name = $name;
        $this->custom_id = $custom_id;
        $this->street = $street;
        $this->postal_code = $postal_code;
        $this->city = $city;
        $this->house_number = $house_number;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'custom_id' => $this->custom_id,
            'street' => $this->street,
            'postal_code' => $this->postal_code,
            'city_id' => $this->city->id,
            'house_number' => $this->house_number,
        ];
    }
}
