<?php

namespace App\Repositories;

use App\Dto\CityDto;
use App\Interfaces\Repositories\CityRepositoryInterface;
use App\Models\City;

class CityRepository implements CityRepositoryInterface
{
    public function create(CityDto $cityDto): City
    {
        return City::create([
            'name' => $cityDto->name,
            'district' => $cityDto->district,
            'commune' => $cityDto->commune,
            'voivodeship' => $cityDto->voivodeship,
        ]);
    }

    public function update(City $city, CityDto $cityDto): bool
    {
        return $city->update([
            'name' => $cityDto->name,
            'district' => $cityDto->district,
            'commune' => $cityDto->commune,
            'voivodeship' => $cityDto->voivodeship,
        ]);
    }

    public function delete(City $city): bool
    {
        return $city->delete();
    }

    public function findById(int $id): ?City
    {
        return City::find($id);
    }

    public function findAll(): array
    {
        return City::all()->toArray();
    }
}
