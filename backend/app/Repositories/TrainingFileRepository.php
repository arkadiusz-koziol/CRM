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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

final class TrainingFileRepository extends EloquentRepository implements TrainingFileRepositoryInterface
{
    public function __construct(
        protected TrainingFile $model,
        protected TrainingFileMapper $mapper,
        protected FileStorageServiceInterface $fileStorageService,
        protected UuidServiceInterface $uuidService
    ) {
        parent::__construct($model);
    }

    public function attachFileToTraining(Training $training, UploadedFile $file): TrainingFileEntity
    {
        $filePath = $this->fileStorageService->store($file, 'trainings/files');
        $id = $this->uuidService->generate();

        $entity = TrainingFileEntity::create(
            id: $id,
            trainingId: $training->id,
            originalName: $file->getClientOriginalName(),
            fileName: $file->getClientOriginalName(),
            filePath: $filePath,
            mimeType: $file->getMimeType(),
            fileSize: $file->getSize(),
        );

        $model = $this->mapper->toModel($entity);
        $model->save();

        return $entity;
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
        return $training->files;
    }

    public function deleteTrainingFile(TrainingFileEntity $trainingFile): bool
    {
        $this->fileStorageService->delete($trainingFile->filePath());

        $model = $this->model->find($trainingFile->id());
        if (! $model) {
            return false;
        }

        return $model->delete();
    }

    public function findById(string $id): ?TrainingFileEntity
    {
        $model = $this->model->find($id);
        if (! $model) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }
}
