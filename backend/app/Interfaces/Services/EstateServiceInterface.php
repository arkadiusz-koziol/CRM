<?php

namespace App\Interfaces\Services;

use App\Dto\EstateDto;
use App\Models\Estate;

interface EstateServiceInterface
{
    public function createEstate(EstateDto $estateDto): Estate;

    public function updateEstate(Estate $estate, EstateDto $estateDto): bool;

    public function deleteEstate(Estate $estate): bool;

    public function findEstateById(int $id): ?Estate;

    public function getAllEstates(): iterable;
}
