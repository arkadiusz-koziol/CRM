<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Trainings;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Services\TrainingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DeleteTrainingController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/v1/admin/trainings/{training}",
     *     summary="Delete training",
     *     description="Delete an existing training and its associated files from the system.",
     *     operationId="deleteTraining",
     *     tags={"Admin Trainings"},
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
     *         response=204,
     *         description="Training deleted successfully"
     *     ),
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
        Training $training,
        TrainingService $trainingService
    ): JsonResponse {
        try {
            $deleted = $trainingService->deleteTraining($training);

            if (! $deleted) {
                return response()->json(
                    ['message' => __('app.action.failed')],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            // Delete associated file if it exists
            if ($training->file_path && Storage::disk('public')->exists($training->file_path)) {
                Storage::disk('public')->delete($training->file_path);
            }

            return response()->json(
                null,
                Response::HTTP_NO_CONTENT
            );
        } catch (Throwable $e) {
            \Log::error('Error deleting training', [
                'training_id' => $training->id,
                'exception' => $e,
            ]);

            return response()->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
