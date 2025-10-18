<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingCategories;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingCategoryResource;
use App\Services\TrainingCategoryService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class GetTrainingCategoriesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/training-categories",
     *     summary="Get training categories",
     *     description="Retrieve a list of all training categories.",
     *     operationId="getTrainingCategories",
     *     tags={"Admin Training Categories"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of training categories",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/TrainingCategoryResource")
     *         )
     *     ),
     *
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
        TrainingCategoryService $trainingCategoryService
    ): JsonResponse {
        try {
            $trainingCategories = $trainingCategoryService->getAllCategories();

            return $this->responseFactory->json(
                TrainingCategoryResource::collection($trainingCategories),
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            $this->logger->error('Error getting training categories', [
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
