<?php

namespace App\Repositories;

use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Models\Training;
use Illuminate\Support\Collection;

class TrainingRepository implements TrainingRepositoryInterface
{
    public function create(array $data): Training
    {
        return Training::create($data);
    }

    public function update(Training $training, array $data): bool
    {
        return $training->update($data);
    }

    public function delete(Training $training): bool
    {
        return $training->delete();
    }

    public function findById(int $id): Training
    {
        return Training::with(['category', 'users', 'files'])->findOrFail($id);
    }

    public function list(): Collection
    {
        return Training::with('category')->get();
    }
}
