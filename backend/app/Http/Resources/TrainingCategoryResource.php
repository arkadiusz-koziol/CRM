<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\TrainingCategory\Entity\TrainingCategory as TrainingCategoryEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="TrainingCategoryResource",
 *     title="Training Category Resource",
 *     description="Training category data",
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="a1b2c3d4-e5f6-7890-1234-567890abcdef"),
 *     @OA\Property(property="name", type="string", example="Safety Training"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 * )
 */
final class TrainingCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var TrainingCategoryEntity $entity */
        $entity = $this->resource;

        return [
            'data' => [
                'type' => 'training-categories',
                'id' => $entity->id(),
                'attributes' => [
                    'name' => $entity->name(),
                    'created_at' => $entity->createdAt()->toISOString(),
                    'updated_at' => $entity->updatedAt()->toISOString(),
                ],
            ],
        ];
    }
}
