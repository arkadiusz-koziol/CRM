<?php

namespace App\Interfaces\Repositories;

use App\Dto\CarDto;
use App\Models\Car;

interface CarRepositoryInterface
{
    public function create(CarDto $carDto): Car;
}
