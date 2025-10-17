<?php

namespace App\Dto;

use App\Models\City;
use Illuminate\Contracts\Support\Arrayable;

readonly class EstateDto implements Arrayable
{
    public function __construct(
        private string $name,
        private string $custom_id,
        private string $street,
        private string $postal_code,
        private City $city,
        private string $house_number
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'custom_id' => $this->getCustomId(),
            'street' => $this->getStreet(),
            'postal_code' => $this->getPostalCode(),
            'city_id' => $this->getCity(),
            'house_number' => $this->getHouseNumber(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCustomId(): string
    {
        return $this->custom_id;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getPostalCode(): string
    {
        return $this->postal_code;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getHouseNumber(): string
    {
        return $this->house_number;
    }
}
