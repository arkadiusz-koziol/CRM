<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingUsers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignSelectedUsersToTrainingRequest;
use App\Services\TrainingUserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AssignSelectedUsersToTrainingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/trainings/{training}/users/assign-selected",
     *     summary="Assign selected users to training",
     *     description="Assign manually selected users to a training.",
     *     operationId="assignSelectedUsersToTraining",
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
     *             required={"user_ids"},
     *
     *             @OA\Property(
     *                 property="user_ids",
     *                 type="array",
     *
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Users assigned to training successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Users assigned to training successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="training_id", type="integer", example=1),
     *                 @OA\Property(property="user_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="assigned_count", type="integer", example=3)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Training not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No user IDs provided"
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
        AssignSelectedUsersToTrainingRequest $request,
        TrainingUserService $trainingUserService
    ): JsonResponse {
        try {
            $userIds = $request->array('user_ids');
            $assignedCount = $trainingUserService->assignSelectedUsersToTraining($training, $userIds);

            return $this->responseFactory->json([
                'message' => __('app.training_user.selected_users_assigned_successfully'),
                'data' => [
                    'training_id' => $training,
                    'user_ids' => $userIds,
                    'assigned_count' => $assignedCount,
                ],
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                return $this->responseFactory->json(
                    ['message' => $e->getMessage()],
                    Response::HTTP_NOT_FOUND
                );
            }

            if (str_contains($e->getMessage(), 'No user IDs provided')) {
                return $this->responseFactory->json(
                    ['message' => $e->getMessage()],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (Throwable $e) {
            $this->logger->error('Error assigning selected users to training', [
                'training_id' => $training,
                'user_ids' => $request->array('user_ids'),
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
