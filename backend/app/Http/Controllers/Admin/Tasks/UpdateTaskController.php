<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tasks;

use App\Factory\UpdateTaskDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class UpdateTaskController extends Controller
{
    /**
     * @OA\Put(
     *     path="/v1/admin/tasks/{task}",
     *     tags={"Admin Tasks"},
     *     summary="Update a task",
     *     description="Updates an existing task and returns the updated task details.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Fix login issue"),
     *             @OA\Property(property="description", type="string", example="Users cannot log in to the system"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed", "cancelled", "on_hold"}),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "urgent"}),
     *             @OA\Property(property="assigned_to", type="integer", example=2),
     *             @OA\Property(property="due_date", type="string", format="date-time"),
     *             @OA\Property(property="estimated_hours", type="number", format="float"),
     *             @OA\Property(property="actual_hours", type="number", format="float"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task not found")
     *         )
     *     )
     * )
     */
    public function __invoke(
        UpdateTaskRequest $request,
        Task $task,
        TaskService $taskService,
        UpdateTaskDtoFactory $dtoFactory
    ): JsonResponse {
        try {
            $dto = $dtoFactory->fromRequest($request);
            
            if (!$dto->hasChanges()) {
                return $this->responseFactory->json([
                    'message' => __('app.task.no_changes')
                ], Response::HTTP_BAD_REQUEST);
            }

            $updatedTask = $taskService->updateTask($task, $dto);
            return $this->responseFactory->successResponse($updatedTask);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'task_id' => $task->getId(),
            ]);
            return $this->responseFactory->json([
                'message' => __('app.task.update_failed')
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
