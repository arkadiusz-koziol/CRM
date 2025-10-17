<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\TrainingDto;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Models\Training;

final class TrainingService
{
    public function __construct(
        private TrainingRepositoryInterface $trainingRepository,
    ) {}

    public function createTraining(TrainingDto $trainingDto): Training
    {
        return $this->trainingRepository->createTraining($trainingDto);
    }

    public function updateTraining(Training $training, TrainingDto $trainingDto): bool
    {
        return $this->trainingRepository->updateTraining($training, $trainingDto);
    }

    public function deleteTraining(Training $training): bool
    {
        return $this->trainingRepository->deleteTraining($training);
    }

    public function getTrainingById(int $id): ?Training
    {
        return $this->trainingRepository->findById($id);
    }

    public function getAllTrainings(): array
    {
        return $this->trainingRepository->findAllTrainings();
    }

    public function getPaginatedTrainings(int $page = 1, int $limit = 10, string $search = ''): array
    {
        return $this->trainingRepository->findPaginated($page, $limit, $search);
    }
}
