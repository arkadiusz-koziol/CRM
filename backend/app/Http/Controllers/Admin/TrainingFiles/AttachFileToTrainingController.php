<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingFiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttachFileToTrainingRequest;
use App\Http\Resources\TrainingFileResource;
use App\Models\Training;
use App\Services\TrainingFileService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AttachFileToTrainingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/trainings/{training}/files",
     *     summary="Attach file to training",
     *     description="Attach a file to a specific training.",
     *     operationId="attachFileToTraining",
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
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"file"},
     *
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="File to attach (.pptx, .pdf, etc.)"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="File attached to training successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/TrainingFile")
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
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function __invoke(
        string $training,
        AttachFileToTrainingRequest $request,
        TrainingFileService $trainingFileService
    ): JsonResponse {
        try {
            $file = $request->file('file');
            $trainingFile = $trainingFileService->attachFileToTraining($training, $file);

            return $this->responseFactory->json(
                TrainingFileResource::make($trainingFile),
                Response::HTTP_CREATED
            );
        } catch (Throwable $e) {
            $this->logger->error('Error attaching file to training', [
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
