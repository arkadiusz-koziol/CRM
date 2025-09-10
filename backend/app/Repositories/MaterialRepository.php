<?php

namespace App\Repositories;

use App\Dto\MaterialDto;
use App\Interfaces\Repositories\MaterialRepositoryInterface;
use App\Models\Material;

class MaterialRepository implements MaterialRepositoryInterface
{
    public function create(MaterialDto $materialDto): Material
    {
        return Material::create([
            'name' => $materialDto->getName(),
            'description' => $materialDto->getDescription(),
            'count' => $materialDto->getCount(),
            'price' => $materialDto->getPrice(),
        ]);
    }

    public function update(Material $material, MaterialDto $materialDto): bool
    {
        return $material->update([
            'name' => $materialDto->getName(),
            'description' => $materialDto->getDescription(),
            'count' => $materialDto->getCount(),
            'price' => $materialDto->getPrice(),
        ]);
    }

    public function delete(Material $material): bool
    {
        return $material->delete();
    }

    public function findById(int $id): ?Material
    {
        return Material::find($id);
    }

    public function findAll(): array
    {
        return Material::all()->toArray();
    }
}
