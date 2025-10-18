<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingCategories;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTrainingCategoryRequest;
use App\Http\Resources\TrainingCategoryResource;
use App\Services\TrainingCategoryService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UpdateTrainingCategoryController extends Controller
{
    /**
     * @OA\Put(
     *     path="/v1/admin/training-categories/{id}",
     *     summary="Update training category",
     *     description="Update an existing training category.",
     *     operationId="updateTrainingCategory",
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
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example="Updated Safety Training")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Training category updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/TrainingCategoryResource")
     *     ),
     *
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
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function __invoke(
        string $id,
        UpdateTrainingCategoryRequest $request,
        TrainingCategoryService $trainingCategoryService
    ): JsonResponse {
        try {
            $trainingCategory = $trainingCategoryService->updateCategory($id, $request->validated());

            return $this->responseFactory->json(
                TrainingCategoryResource::make($trainingCategory),
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            $this->logger->error('Error updating training category', [
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
