<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tasks;

use App\Factory\CreateTaskDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Services\TaskService;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class StoreTaskController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/tasks",
     *     tags={"Admin Tasks"},
     *     summary="Create a new task",
     *     description="Creates a new task and returns the task details.",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"title", "description", "assigned_to"},
     *
     *             @OA\Property(property="title", type="string", example="Fix login issue"),
     *             @OA\Property(property="description", type="string", example="Users cannot log in to the system"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed",
     *      "cancelled", "on_hold"}, example="pending"),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "urgent"},
     *      example="high"),
     *             @OA\Property(property="assigned_to", type="integer", example=2),
     *             @OA\Property(property="due_date", type="string", format="date-time", example="2024-12-31T23:59:59Z"),
     *             @OA\Property(property="estimated_hours", type="number", format="float", example=4.5),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function __invoke(
        CreateTaskRequest $request,
        TaskService $taskService,
        CreateTaskDtoFactory $dtoFactory,
        AuthManager $auth
    ): JsonResponse {
        try {
            $dto = $dtoFactory->fromRequest($request);

            return $this->responseFactory->successResponse(
                $taskService->createTask($dto)
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'user_id' => $auth->id(),
            ]);

            return $this->responseFactory->json([
                'message' => __('app.task.creation_failed'),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
