<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingUsers;

use App\Http\Controllers\Controller;
use App\Services\TrainingUserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class GetTrainingUsersController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/trainings/{training}/users",
     *     summary="Get training users",
     *     description="Get all users assigned to a specific training.",
     *     operationId="getTrainingUsers",
     *     tags={"Admin Training Users"},
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
     *         description="Training users retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array",
     *
     *                 @OA\Items(type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="training_id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John"),
     *                         @OA\Property(property="surname", type="string", example="Doe"),
     *                         @OA\Property(property="email", type="string", example="john@example.com")
     *                     ),
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
        int $training,
        TrainingUserService $trainingUserService
    ): JsonResponse {
        try {
            $trainingUsers = $trainingUserService->getTrainingUsers($training);

            return $this->responseFactory->json([
                'data' => $trainingUsers,
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                return $this->responseFactory->json(
                    ['message' => $e->getMessage()],
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (Throwable $e) {
            $this->logger->error('Error getting training users', [
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
