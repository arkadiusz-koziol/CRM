<?php

namespace App\Interfaces\Services;

use App\Dto\CityDto;
use App\Models\City;

interface CityServiceInterface
{
    public function createCity(CityDto $cityDto): City;
    public function updateCity(City $city, CityDto $cityDto): bool;
    public function deleteCity(City $city): bool;
}
