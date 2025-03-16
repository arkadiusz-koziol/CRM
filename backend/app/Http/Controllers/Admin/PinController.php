<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PinService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class PinController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/pins/{plan}",
     *     tags={"Admin Pins"},
     *     summary="Get pins for a specific plan",
     *     description="Retrieve all pins associated with a specific plan.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="plan",
     *         in="path",
     *         description="Plan ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of pins",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Pin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pins not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pins not found")
     *         )
     *     )
     * )
     */
    public function index(
        Plan $plan,
        PinService $pinService
    ): JsonResponse
    {
        return $this->responseFactory->json($pinService->getPinsByPlan($plan));
    }
}
