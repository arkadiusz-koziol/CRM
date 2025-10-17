<?php

namespace App\Services;

use App\Dto\EstateDto;
use App\Interfaces\Repositories\EstateRepositoryInterface;
use App\Models\Estate;

class EstateService
{
    public function __construct(
        protected EstateRepositoryInterface $estateRepository,
    ) {}

    public function createEstate(EstateDto $estateDto): Estate
    {
        return $this->estateRepository->create([
            'name' => $estateDto->getName(),
            'custom_id' => $estateDto->getCustomId(),
            'street' => $estateDto->getStreet(),
            'postal_code' => $estateDto->getPostalCode(),
            'city_id' => $estateDto->getCity()->id,
            'house_number' => $estateDto->getHouseNumber(),
        ]);
    }

    public function updateEstate(Estate $estate, EstateDto $estateDto): bool
    {
        return $this->estateRepository->update($estate, [
            'name' => $estateDto->getName(),
            'custom_id' => $estateDto->getCustomId(),
            'street' => $estateDto->getStreet(),
            'postal_code' => $estateDto->getPostalCode(),
            'city_id' => $estateDto->getCity()->id,
            'house_number' => $estateDto->getHouseNumber(),
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
