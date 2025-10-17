<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class ShowTaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/tasks/{task}",
     *     tags={"Admin Tasks"},
     *     summary="Get task details",
     *     description="Returns the details of a specific task.",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Task details retrieved successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Task not found")
     *         )
     *     )
     * )
     */
    public function __invoke(Task $task): JsonResponse
    {
        try {
            return $this->responseFactory->successResponse($task);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'task_id' => $task->getId(),
            ]);

            return $this->responseFactory->json([
                'message' => __('app.task.retrieval_failed'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
