<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\TrainingFile\Entity\TrainingFile as TrainingFileEntity;
use App\Infrastructure\TrainingFile\Mapper\TrainingFileMapper;
use App\Interfaces\Repositories\TrainingFileRepositoryInterface;
use App\Interfaces\Services\FileStorageServiceInterface;
use App\Interfaces\Services\UuidServiceInterface;
use App\Models\Training;
use App\Models\TrainingFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class TrainingFileRepository extends EloquentRepository implements TrainingFileRepositoryInterface
{
    public function __construct(
        TrainingFile $model,
        protected TrainingFileMapper $mapper,
        protected FileStorageServiceInterface $fileStorageService,
        protected UuidServiceInterface $uuidService
    ) {
        parent::__construct($model);
    }

    public function attachFileToTraining(Training $training, UploadedFile $file): TrainingFileEntity
    {
        $filePath = $this->fileStorageService->store($file, 'training-files');

        $model = $this->model->create([
            'training_id' => $training->id,
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return $this->mapper->toDomain($model);
    }

    public function attachMultipleFilesToTraining(Training $training, array $files): Collection
    {
        $attachedFiles = new Collection;
        foreach ($files as $file) {
            $attachedFiles->add($this->attachFileToTraining($training, $file));
        }

        return $attachedFiles;
    }

    public function getTrainingFiles(Training $training): Collection
    {
        return collect($training->files->map(function ($file) {
            return $this->mapper->toDomain($file);
        })->all());
    }

    public function deleteTrainingFile(TrainingFileEntity $trainingFile): bool
    {
        $this->fileStorageService->delete($trainingFile->filePath());

        $model = $this->model->find((int) $trainingFile->id());
        if (! $model) {
            return false;
        }

        return $model->delete();
    }

    public function findTrainingFileById(int $id): ?TrainingFileEntity
    {
        $model = $this->model->find($id);
        if (! $model) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }
}
