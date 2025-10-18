<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\TrainingUsers;

use App\Http\Controllers\Controller;
use App\Services\TrainingUserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class RemoveUserFromTrainingController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/v1/admin/trainings/{training}/users/{user}",
     *     summary="Remove user from training",
     *     description="Remove a specific user from a training.",
     *     operationId="removeUserFromTraining",
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
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="User removed from training successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Training or user not found"
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
        int $user,
        TrainingUserService $trainingUserService
    ): JsonResponse {
        try {
            $removed = $trainingUserService->removeUserFromTraining($training, $user);

            if (! $removed) {
                return $this->responseFactory->json(
                    ['message' => __('app.training_user.not_assigned')],
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->responseFactory->json(
                null,
                Response::HTTP_NO_CONTENT
            );
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
            $this->logger->error('Error removing user from training', [
                'training_id' => $training,
                'user_id' => $user,
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
