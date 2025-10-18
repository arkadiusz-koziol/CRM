<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\TrainingFile\Entity\TrainingFile as TrainingFileEntity;
use App\Interfaces\Repositories\TrainingFileRepositoryInterface;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

final class TrainingFileService
{
    public function __construct(
        private TrainingFileRepositoryInterface $trainingFileRepository,
        private TrainingRepositoryInterface $trainingRepository
    ) {}

    public function attachFileToTraining(int $trainingId, UploadedFile $file): TrainingFileEntity
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new \RuntimeException('Training not found');
        }

        return $this->trainingFileRepository->attachFileToTraining($training, $file);
    }

    public function attachMultipleFilesToTraining(int $trainingId, array $files): Collection
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new \RuntimeException('Training not found');
        }

        return $this->trainingFileRepository->attachMultipleFilesToTraining($training, $files);
    }

    public function getTrainingFiles(int $trainingId): Collection
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new \RuntimeException('Training not found');
        }

        return $this->trainingFileRepository->getTrainingFiles($training);
    }

    public function getTrainingFileById(int $fileId): ?TrainingFileEntity
    {
        return $this->trainingFileRepository->findTrainingFileById($fileId);
    }

    public function deleteTrainingFile(int $fileId): bool
    {
        $trainingFile = $this->trainingFileRepository->findTrainingFileById($fileId);
        if (! $trainingFile) {
            throw new \RuntimeException('Training file not found');
        }

        return $this->trainingFileRepository->deleteTrainingFile($trainingFile);
    }
}
