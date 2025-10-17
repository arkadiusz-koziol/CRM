<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingFiles;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingFileResource;
use App\Models\Training;
use App\Services\TrainingFileService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class GetTrainingFilesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/trainings/{training}/files",
     *     summary="Get training files",
     *     description="Retrieve all files attached to a specific training.",
     *     operationId="getTrainingFiles",
     *     tags={"Admin Training Files"},
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
     *     @OA\Response(
     *         response=200,
     *         description="List of training files",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/TrainingFile")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Training not found"
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
        string $training,
        TrainingFileService $trainingFileService
    ): JsonResponse {
        try {
            $files = $trainingFileService->getTrainingFiles($training);

            return $this->responseFactory->json(
                TrainingFileResource::collection($files),
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            $this->logger->error('Error getting training files', [
                'training_id' => $training,
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
