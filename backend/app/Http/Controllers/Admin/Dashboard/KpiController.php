<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Dashboard;

use App\Services\Analytics\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class KpiController
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * @OA\Get(
     *     path="/admin/dashboard/kpi",
     *     summary="Get dashboard KPI data",
     *     description="Retrieve key performance indicators for the business dashboard including active clients, completed tasks, training completion rate, and material usage rate",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="If-None-Match",
     *         in="header",
     *         description="ETag for conditional requests",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="KPI data retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="active_clients", type="integer", description="Number of active clients"),
     *             @OA\Property(property="completed_tasks_30d", type="integer", description="Number of completed tasks in the last 30 days"),
     *             @OA\Property(property="training_completion_rate", type="number", format="float", description="Percentage of users who completed training"),
     *             @OA\Property(property="material_usage_rate", type="number", format="float", description="Percentage of materials being used"),
     *             @OA\Property(property="generated_at", type="string", format="date-time", description="When the data was generated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=304,
     *         description="Not Modified - data has not changed since last request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $kpiData = $this->dashboardService->getKpiDataWithETag();
            $etag = $kpiData['etag'];
            $data = $kpiData['data'];

            // Check if client has the same ETag
            $clientETag = $request->header('If-None-Match');
            if ($clientETag === $etag) {
                return response()->json(null, Response::HTTP_NOT_MODIFIED)
                    ->header('ETag', $etag)
                    ->header('Cache-Control', 'public, max-age=300');
            }

            return response()->json($data)
                ->header('ETag', $etag)
                ->header('Cache-Control', 'public, max-age=300')
                ->header('Last-Modified', $data['generated_at']);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve dashboard KPI data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/dashboard/kpi/refresh",
     *     summary="Refresh dashboard KPI cache",
     *     description="Clear the cached KPI data to force regeneration on next request",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Cache cleared successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Dashboard KPI cache cleared successfully")
     *         )
     *     )
     * )
     */
    public function refresh(): JsonResponse
    {
        try {
            $this->dashboardService->clearCache();

            return response()->json([
                'message' => 'Dashboard KPI cache cleared successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to clear dashboard KPI cache',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
