<?php

namespace App\Interfaces\Repositories;

use App\Models\Estate;

interface EstateRepositoryInterface
{
    public function create(array $data): Estate;

    public function update(Estate $estate, array $data): bool;

    public function delete(Estate $estate): bool;

    public function findById(int $id): ?Estate;

    public function findAll(): iterable;
}
