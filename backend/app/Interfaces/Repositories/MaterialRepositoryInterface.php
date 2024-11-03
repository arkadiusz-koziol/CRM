<?php

namespace App\Interfaces\Repositories;

use App\Dto\MaterialDto;
use App\Models\Material;

interface MaterialRepositoryInterface
{
    public function create(MaterialDto $materialDto): Material;
    public function update(Material $material, MaterialDto $materialDto): bool;
    public function delete(Material $material): bool;
    public function findById(int $id): ?Material;
    public function findAll(): array;
}

