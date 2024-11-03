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
            'name' => $materialDto->name,
            'description' => $materialDto->description,
            'count' => $materialDto->count,
            'price' => $materialDto->price,
        ]);
    }

    public function update(Material $material, MaterialDto $materialDto): bool
    {
        return $material->update([
            'name' => $materialDto->name,
            'description' => $materialDto->description,
            'count' => $materialDto->count,
            'price' => $materialDto->price,
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
