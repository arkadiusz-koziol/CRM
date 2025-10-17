<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingFiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttachMultipleFilesToTrainingRequest;
use App\Http\Resources\TrainingFileResource;
use App\Models\Training;
use App\Services\TrainingFileService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AttachMultipleFilesToTrainingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/trainings/{training}/files/multiple",
     *     summary="Attach multiple files to training",
     *     description="Attach multiple files to a specific training.",
     *     operationId="attachMultipleFilesToTraining",
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
     *                 required={"files"},
     *
     *                 @OA\Property(
     *                     property="files[]",
     *                     type="array",
     *
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Files to attach (.pptx, .pdf, etc.)"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Files attached to training successfully",
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
        AttachMultipleFilesToTrainingRequest $request,
        TrainingFileService $trainingFileService
    ): JsonResponse {
        try {
            $files = $request->file('files');
            $attachedFiles = $trainingFileService->attachMultipleFilesToTraining($training, $files);

            return $this->responseFactory->json(
                TrainingFileResource::collection($attachedFiles),
                Response::HTTP_CREATED
            );
        } catch (Throwable $e) {
            $this->logger->error('Error attaching multiple files to training', [
                'training_id' => $training,
                'files_count' => count($request->file('files', [])),
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
