<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingUsers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignUserToTrainingRequest;
use App\Services\TrainingUserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AssignUserToTrainingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/trainings/{training}/users",
     *     summary="Assign user to training",
     *     description="Assign a specific user to a training.",
     *     operationId="assignUserToTraining",
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
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"user_id"},
     *
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User assigned to training successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User assigned to training successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="training_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Training or user not found"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="User already assigned to training"
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
        AssignUserToTrainingRequest $request,
        TrainingUserService $trainingUserService
    ): JsonResponse {
        try {
            $trainingUserService->assignUserToTraining($training, $request->integer('user_id'));

            return $this->responseFactory->json([
                'message' => __('app.training_user.assigned_successfully'),
                'data' => [
                    'training_id' => $training,
                    'user_id' => $request->integer('user_id'),
                ],
            ], Response::HTTP_CREATED);
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                return $this->responseFactory->json(
                    ['message' => $e->getMessage()],
                    Response::HTTP_NOT_FOUND
                );
            }

            if (str_contains($e->getMessage(), 'already assigned')) {
                return $this->responseFactory->json(
                    ['message' => $e->getMessage()],
                    Response::HTTP_CONFLICT
                );
            }

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (Throwable $e) {
            $this->logger->error('Error assigning user to training', [
                'training_id' => $training,
                'user_id' => $request->integer('user_id'),
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
