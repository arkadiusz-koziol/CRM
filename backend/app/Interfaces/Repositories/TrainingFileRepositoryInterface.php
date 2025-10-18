<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\TrainingFile\Entity\TrainingFile as TrainingFileEntity;
use App\Models\Training;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface TrainingFileRepositoryInterface
{
    public function attachFileToTraining(Training $training, UploadedFile $file): TrainingFileEntity;

    public function attachMultipleFilesToTraining(Training $training, array $files): Collection;

    public function getTrainingFiles(Training $training): Collection;

    public function deleteTrainingFile(TrainingFileEntity $trainingFile): bool;

    public function findTrainingFileById(int $id): ?TrainingFileEntity;
}
