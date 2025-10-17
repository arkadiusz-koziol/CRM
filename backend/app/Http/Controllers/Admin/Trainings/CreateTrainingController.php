<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Trainings;

use App\Factory\TrainingDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTrainingRequest;
use App\Http\Resources\TrainingResource;
use App\Services\TrainingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CreateTrainingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/trainings",
     *     summary="Create training",
     *     description="Create a new training in the system with optional file attachment.",
     *     operationId="createTraining",
     *     tags={"Admin Trainings"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="title", type="string", example="Safety Training"),
     *                 @OA\Property(property="description", type="string", example="Comprehensive safety training course", nullable=true),
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
     *         response=201,
     *         description="Training created successfully",
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
        CreateTrainingRequest $request,
        TrainingService $trainingService,
        TrainingDtoFactory $trainingDtoFactory
    ): JsonResponse {
        try {
            $fileData = $this->handleFileUpload($request->file('file'));

            $trainingDto = $trainingDtoFactory->fromArray([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'file_path' => $fileData['path'] ?? null,
                'file_name' => $fileData['name'] ?? null,
                'file_size' => $fileData['size'] ?? null,
                'mime_type' => $fileData['mime_type'] ?? null,
            ]);

            $training = $trainingService->createTraining($trainingDto);

            return $this->responseFactory->json(
                new TrainingResource($training),
                Response::HTTP_CREATED
            );
        } catch (Throwable $e) {
            $this->logger->error('Error creating training', [
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
