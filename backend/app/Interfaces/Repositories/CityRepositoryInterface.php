<?php

namespace App\Interfaces\Repositories;

use App\Dto\CityDto;
use App\Models\City;

interface CityRepositoryInterface
{
    public function create(CityDto $cityDto): City;

    public function update(City $city, CityDto $cityDto): bool;

    public function delete(City $city): bool;

    public function findById(int $id): ?City;

    public function findAll(): array;

}

