<?php

namespace App\Repositories;

use App\Interfaces\Repositories\TrainingFileRepositoryInterface;
use App\Models\Training;
use Illuminate\Support\Collection;

class TrainingFileRepository implements TrainingFileRepositoryInterface
{
    public function create(Training $training, string $filePath): void
    {
        $training->files()->create(['file_path' => $filePath]);
    }

    public function all(Training $training): Collection
    {
        return $training->files;
    }

    public function deleteAll(Training $training): void
    {
        foreach ($training->files as $file) {
            $file->delete();
        }
    }
}
