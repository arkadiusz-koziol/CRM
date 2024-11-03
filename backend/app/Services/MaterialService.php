<?php

namespace App\Services;

use App\Dto\MaterialDto;
use App\Interfaces\Repositories\MaterialRepositoryInterface;
use App\Interfaces\Services\MaterialServiceInterface;
use App\Models\material;

class MaterialService implements MaterialServiceInterface
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepository
    ) {
    }

    public function createMaterial(MaterialDto $materialDto): Material
    {
        return $this->materialRepository->create($materialDto);
    }

    public function updateMaterial(Material $material, MaterialDto $materialDto): bool
    {
        return $this->materialRepository->update($material, $materialDto);
    }

    public function deleteMaterial(Material $material): bool
    {
        return $this->materialRepository->delete($material);
    }

    public function getMaterialById(int $id): ?Material
    {
        return $this->materialRepository->findById($id);
    }

    public function getAllMaterials(): array
    {
        return $this->materialRepository->findAll();
    }
}
