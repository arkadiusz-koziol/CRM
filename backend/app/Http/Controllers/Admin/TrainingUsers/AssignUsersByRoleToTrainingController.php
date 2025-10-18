<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingUsers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignUsersByRoleToTrainingRequest;
use App\Services\TrainingUserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AssignUsersByRoleToTrainingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/trainings/{training}/users/assign-by-role",
     *     summary="Assign users by role to training",
     *     description="Assign all users with a specific role to a training.",
     *     operationId="assignUsersByRoleToTraining",
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
     *             required={"role"},
     *
     *             @OA\Property(property="role", type="string", example="admin")
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
     *                 @OA\Property(property="role", type="string", example="admin"),
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
        AssignUsersByRoleToTrainingRequest $request,
        TrainingUserService $trainingUserService
    ): JsonResponse {
        try {
            $role = $request->string('role')->toString();
            $assignedCount = $trainingUserService->assignUsersByRoleToTraining($training, $role);

            return $this->responseFactory->json([
                'message' => __('app.training_user.users_by_role_assigned_successfully'),
                'data' => [
                    'training_id' => $training,
                    'role' => $role,
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

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (Throwable $e) {
            $this->logger->error('Error assigning users by role to training', [
                'training_id' => $training,
                'role' => $request->string('role')->toString(),
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
