<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingCategories;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTrainingCategoryRequest;
use App\Http\Resources\TrainingCategoryResource;
use App\Services\TrainingCategoryService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CreateTrainingCategoryController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/training-categories",
     *     summary="Create training category",
     *     description="Create a new training category.",
     *     operationId="createTrainingCategory",
     *     tags={"Admin Training Categories"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example="Safety Training")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Training category created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/TrainingCategoryResource")
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
        CreateTrainingCategoryRequest $request,
        TrainingCategoryService $trainingCategoryService
    ): JsonResponse {
        try {
            $trainingCategory = $trainingCategoryService->createCategory($request->validated());

            return $this->responseFactory->json(
                TrainingCategoryResource::make($trainingCategory),
                Response::HTTP_CREATED
            );
        } catch (Throwable $e) {
            $this->logger->error('Error creating training category', [
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
