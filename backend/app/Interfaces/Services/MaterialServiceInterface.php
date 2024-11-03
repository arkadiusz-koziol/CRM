<?php

namespace App\Interfaces\Services;

use App\Dto\MaterialDto;
use App\Models\Material;

interface MaterialServiceInterface
{
    public function createMaterial(MaterialDto $materialDto): Material;
    public function updateMaterial(Material $material, MaterialDto $materialDto): bool;
    public function deleteMaterial(Material $material): bool;
    public function getMaterialById(int $id): ?Material;
    public function getAllMaterials(): array;
}
