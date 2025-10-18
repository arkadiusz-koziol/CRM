<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingCategories;

use App\Http\Controllers\Controller;
use App\Services\TrainingCategoryService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DeleteTrainingCategoryController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/v1/admin/training-categories/{id}",
     *     summary="Delete training category",
     *     description="Delete a training category.",
     *     operationId="deleteTrainingCategory",
     *     tags={"Admin Training Categories"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Training Category ID",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="uuid", example="a1b2c3d4-e5f6-7890-1234-567890abcdef")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Training category deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Training category not found"
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
        string $id,
        TrainingCategoryService $trainingCategoryService
    ): JsonResponse {
        try {
            $deleted = $trainingCategoryService->deleteCategory($id);

            if (! $deleted) {
                return $this->responseFactory->json(
                    ['message' => __('app.action.failed')],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return $this->responseFactory->json(
                ['message' => __('app.category.deleted_successfully')],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            $this->logger->error('Error deleting training category', [
                'category_id' => $id,
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
