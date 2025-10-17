<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Trainings;

use App\Factory\TrainingDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTrainingRequest;
use App\Http\Resources\TrainingResource;
use App\Models\Training;
use App\Services\TrainingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UpdateTrainingController extends Controller
{
    /**
     * @OA\Put(
     *     path="/v1/admin/trainings/{training}",
     *     summary="Update training",
     *     description="Update an existing training in the system with optional file attachment.",
     *     operationId="updateTraining",
     *     tags={"Admin Trainings"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="training",
     *         in="path",
     *         description="Training ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="title", type="string", example="Updated Safety Training"),
     *                 @OA\Property(property="description", type="string", example="Updated comprehensive safety training course", nullable=true),
     *                 @OA\Property(property="category", type="string", example="Safety"),
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="Training file (PPTX or PDF, max 10MB)"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Training updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="trainings"),
     *                 @OA\Property(property="id", type="string", example="1"),
     *                 @OA\Property(
     *                     property="attributes",
     *                     type="object",
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string", nullable=true),
     *                     @OA\Property(property="category", type="string"),
     *                     @OA\Property(property="file_path", type="string", nullable=true),
     *                     @OA\Property(property="file_name", type="string", nullable=true),
     *                     @OA\Property(property="file_size", type="integer", nullable=true),
     *                     @OA\Property(property="mime_type", type="string", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Training not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function __invoke(
        Training $training,
        UpdateTrainingRequest $request,
        TrainingService $trainingService,
        TrainingDtoFactory $trainingDtoFactory
    ): JsonResponse {
        try {
            $fileData = $this->handleFileUpload($request->file('file'));

            $trainingDto = $trainingDtoFactory->fromArray([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'file_path' => $fileData['path'] ?? $training->file_path,
                'file_name' => $fileData['name'] ?? $training->file_name,
                'file_size' => $fileData['size'] ?? $training->file_size,
                'mime_type' => $fileData['mime_type'] ?? $training->mime_type,
            ]);

            $updated = $trainingService->updateTraining($training, $trainingDto);

            if (! $updated) {
                return $this->responseFactory->json(
                    ['message' => __('app.action.failed')],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return $this->responseFactory->json(
                new TrainingResource($training->fresh()),
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            $this->logger->error('Error updating training', [
                'training_id' => $training->id,
                'request_data' => $request->except(['file']),
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function handleFileUpload(?UploadedFile $file): array
    {
        if (! $file) {
            return [];
        }

        $path = $file->store('trainings', 'public');
        $fileName = $file->getClientOriginalName();

        return [
            'path' => $path,
            'name' => $fileName,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }
}
