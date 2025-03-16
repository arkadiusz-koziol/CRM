<?php

namespace App\Services;

use App\Dto\EstateDto;
use App\Interfaces\Repositories\EstateRepositoryInterface;
use App\Models\Estate;

class EstateService
{
    public function __construct(
        protected EstateRepositoryInterface $estateRepository,
    ) {
    }

    public function createEstate(EstateDto $estateDto): Estate
    {
        return $this->estateRepository->create([
            'name' => $estateDto->name,
            'custom_id' => $estateDto->custom_id,
            'street' => $estateDto->street,
            'postal_code' => $estateDto->postal_code,
            'city_id' => $estateDto->city->id,
            'house_number' => $estateDto->house_number,
        ]);
    }

    public function updateEstate(Estate $estate, EstateDto $estateDto): bool
    {
        return $this->estateRepository->update($estate, [
            'name' => $estateDto->name,
            'custom_id' => $estateDto->custom_id,
            'street' => $estateDto->street,
            'postal_code' => $estateDto->postal_code,
            'city_id' => $estateDto->city->id,
            'house_number' => $estateDto->house_number,
        ]);
    }

    public function deleteEstate(Estate $estate): bool
    {
        return $this->estateRepository->delete($estate);
    }

    public function findEstateById(int $id): ?Estate
    {
        return $this->estateRepository->findById($id);
    }

    public function getAllEstates(): iterable
    {
        return $this->estateRepository->findAll();
    }
}
