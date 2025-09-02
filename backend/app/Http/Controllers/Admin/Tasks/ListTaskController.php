<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tasks;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class ListTaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/tasks/list",
     *     tags={"Admin Tasks"},
     *     summary="List all tasks",
     *     description="Returns a paginated list of all tasks with optional filtering.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "completed", "cancelled", "on_hold"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority",
     *         @OA\Schema(type="string", enum={"low", "medium", "high", "urgent"})
     *     ),
     *     @OA\Parameter(
     *         name="assigned_to",
     *         in="query",
     *         description="Filter by assigned user ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in title and description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tasks retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request, TaskService $taskService): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $status = $request->get('status');
            $priority = $request->get('priority');
            $assignedTo = $request->get('assigned_to');
            $search = $request->get('search');

            if ($search) {
                $tasks = $taskService->searchTasks($search, $perPage);
            } elseif ($status) {
                $tasks = $taskService->getTasksByStatus($status, $perPage);
            } elseif ($priority) {
                $tasks = $taskService->getTasksByPriority($priority, $perPage);
            } elseif ($assignedTo) {
                $tasks = $taskService->getTasksByAssignedUser((int) $assignedTo, $perPage);
            } else {
                $tasks = $taskService->getAllTasks($perPage);
            }

            return $this->responseFactory->successResponse($tasks);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->responseFactory->json([
                'message' => __('app.task.list_failed')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
