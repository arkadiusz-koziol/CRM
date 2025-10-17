<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class DestroyTaskController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/v1/admin/tasks/{task}",
     *     tags={"Admin Tasks"},
     *     summary="Delete a task",
     *     description="Deletes a specific task.",
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
     *         description="Task deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Task deleted successfully")
     *         )
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
    public function __invoke(Task $task, TaskService $taskService): JsonResponse
    {
        try {
            $deleted = $taskService->deleteTask($task);

            if (! $deleted) {
                return $this->responseFactory->json([
                    'message' => __('app.task.deletion_failed'),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->responseFactory->json([
                'message' => __('app.task.deleted_successfully'),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'task_id' => $task->getId(),
            ]);

            return $this->responseFactory->json([
                'message' => __('app.task.deletion_failed'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
