<?php

namespace App\Services;

use App\Dto\CityDto;
use App\Interfaces\Repositories\CityRepositoryInterface;
use App\Models\City;

class CityService
{
    public function __construct(
        protected CityRepositoryInterface $cityRepository
    ) {
    }

    public function createCity(CityDto $cityDto): City
    {
        return $this->cityRepository->create($cityDto);
    }

    public function updateCity(City $city, CityDto $cityDto): bool
    {
        return $this->cityRepository->update($city, $cityDto);
    }

    public function deleteCity(City $city): bool
    {
        return $this->cityRepository->delete($city);
    }

    public function getCityById(int $id): ?City
    {
        return $this->cityRepository->findById($id);
    }

    public function getAllCities(): array
    {
        return $this->cityRepository->findAll();
    }
}
