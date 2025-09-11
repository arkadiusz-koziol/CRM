<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ToolService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class StatsController extends Controller
{
    public function __construct(
        private ToolService $toolService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/v1/admin/dashboard/stats",
     *     summary="Get dashboard statistics",
     *     description="Retrieve statistics for dashboard cards including counts of tools,
     *       materials, cars, and estates.",
     *     operationId="getDashboardStats",
     *     tags={"Admin Dashboard"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tools", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="active", type="integer")
     *             ),
     *             @OA\Property(property="materials", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="active", type="integer")
     *             ),
     *             @OA\Property(property="cars", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="active", type="integer")
     *             ),
     *             @OA\Property(property="estates", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="active", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function __invoke(): JsonResponse
    {
        // For now, we only have tools implemented, so we'll return mock data for others
        // In the future, you can add MaterialService, CarService, EstateService etc.

        $tools = $this->toolService->getAllTools();
        $toolsCount = count($tools);

        return $this->responseFactory->json([
            'tools' => [
                'total' => $toolsCount,
                'active' => $toolsCount, // Assuming all tools are active for now
            ],
            'materials' => [
                'total' => 156, // Mock data - replace with real service when available
                'active' => 142,
            ],
            'cars' => [
                'total' => 8, // Mock data - replace with real service when available
                'active' => 7,
            ],
            'estates' => [
                'total' => 12, // Mock data - replace with real service when available
                'active' => 11,
            ],
        ]);
    }
}