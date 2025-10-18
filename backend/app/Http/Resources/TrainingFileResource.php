<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\TrainingFile\Entity\TrainingFile as TrainingFileEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TrainingFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var TrainingFileEntity $entity */
        $entity = $this->resource;

        return [
            'data' => [
                'type' => 'training-files',
                'id' => $entity->id(),
                'attributes' => [
                    'original_name' => $entity->originalName(),
                    'file_name' => $entity->fileName(),
                    'file_path' => $entity->filePath(),
                    'mime_type' => $entity->mimeType(),
                    'file_size' => $entity->fileSize(),
                    'created_at' => $entity->createdAt()->toISOString(),
                    'updated_at' => $entity->updatedAt()->toISOString(),
                ],
            ],
        ];
    }
}
