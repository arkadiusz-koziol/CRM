<?php

declare(strict_types=1);

namespace App\Infrastructure\TrainingFile\Mapper;

use App\Domain\TrainingFile\Entity\TrainingFile as TrainingFileEntity;
use App\Models\TrainingFile as TrainingFileModel;
use Carbon\Carbon;

final class TrainingFileMapper
{
    public function toDomain(TrainingFileModel $model): TrainingFileEntity
    {
        return new TrainingFileEntity(
            id: $model->id,
            trainingId: $model->training_id,
            originalName: $model->original_name,
            fileName: $model->file_name,
            filePath: $model->file_path,
            mimeType: $model->mime_type,
            fileSize: $model->file_size,
            createdAt: Carbon::parse($model->created_at),
            updatedAt: Carbon::parse($model->updated_at),
            deletedAt: $model->deleted_at ? Carbon::parse($model->deleted_at) : null,
        );
    }

    public function toModel(TrainingFileEntity $entity): TrainingFileModel
    {
        $model = new TrainingFileModel;
        $model->id = $entity->id();
        $model->training_id = $entity->trainingId();
        $model->original_name = $entity->originalName();
        $model->file_name = $entity->fileName();
        $model->file_path = $entity->filePath();
        $model->mime_type = $entity->mimeType();
        $model->file_size = $entity->fileSize();
        $model->created_at = $entity->createdAt();
        $model->updated_at = $entity->updatedAt();
        $model->deleted_at = $entity->deletedAt();

        return $model;
    }
}
