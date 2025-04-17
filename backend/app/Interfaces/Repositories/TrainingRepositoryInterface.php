<?php

namespace App\Interfaces\Repositories;

use App\Models\Training;
use Illuminate\Support\Collection;

interface TrainingRepositoryInterface
{
    public function create(array $data): Training;
    public function update(Training $training, array $data): bool;
    public function delete(Training $training): bool;
    public function findById(int $id): Training;
    public function list(): Collection;
}
