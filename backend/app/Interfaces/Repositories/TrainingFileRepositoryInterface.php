<?php

namespace App\Interfaces\Repositories;

use App\Models\Training;
use Illuminate\Support\Collection;

interface TrainingFileRepositoryInterface
{
    public function create(Training $training, string $filePath): void;

    public function all(Training $training): Collection;

    public function deleteAll(Training $training): void;
}
