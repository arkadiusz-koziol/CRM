<?php

namespace App\Repositories;

use App\Interfaces\Repositories\EstateRepositoryInterface;
use App\Models\Estate;

class EstateRepository implements EstateRepositoryInterface
{
    public function create(array $data): Estate
    {
        return Estate::create($data);
    }

    public function update(Estate $estate, array $data): bool
    {
        return $estate->update($data);
    }

    public function delete(Estate $estate): bool
    {
        return $estate->delete();
    }

    public function findById(int $id): ?Estate
    {
        return Estate::with('city')->find($id)->makeHidden(['city_id']);
    }

    public function findAll(): iterable
    {
        return Estate::with('city')->get()->makeHidden(['city_id']);
    }
}
