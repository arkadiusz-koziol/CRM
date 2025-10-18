<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingFiles;

use App\Factory\ResponseFactory;
use App\Http\Controllers\Controller;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Models\Training;
use App\Services\TrainingFileService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DeleteTrainingFileController extends Controller
{
    public function __construct(
        ResponseFactory $responseFactory,
        AuthManager $authManager,
        LoggerInterface $logger,
        private TrainingRepositoryInterface $trainingRepository,
        private TrainingFileService $trainingFileService
    ) {
        parent::__construct($responseFactory, $authManager, $logger);
    }

    /**
     * @OA\Delete(
     *     path="/v1/admin/trainings/{training}/files/{file}",
     *     summary="Delete training file",
     *     description="Delete a specific file from a training.",
     *     operationId="deleteTrainingFile",
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
     *     @OA\Parameter(
     *         name="file",
     *         in="path",
     *         description="File ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="File deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Training or File not found"
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
        string $fileId
    ): JsonResponse {
        try {
            // Check if training exists
            $training = $this->trainingRepository->findById((int) $training);
            if (! $training) {
                return $this->responseFactory->json(
                    ['message' => __('app.training.not_found')],
                    Response::HTTP_NOT_FOUND
                );
            }

            // Check if file belongs to this training
            $trainingFile = $this->trainingFileService->getTrainingFileById((int) $fileId);
            if (! $trainingFile || $trainingFile->trainingId() !== (string) $training->id) {
                return $this->responseFactory->json(
                    ['message' => __('app.action.failed')],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            $deleted = $this->trainingFileService->deleteTrainingFile((int) $fileId);

            if (! $deleted) {
                return $this->responseFactory->json(
                    ['message' => __('app.action.failed')],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return $this->responseFactory->json(
                ['message' => __('app.file.deleted_successfully')],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            $this->logger->error('Error deleting training file', [
                'training_id' => $training,
                'file_id' => $fileId,
                'exception' => $e,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed'), 'debug' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
