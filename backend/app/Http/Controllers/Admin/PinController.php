<?php

namespace App\Http\Controllers\Admin;

use App\Factory\ResponseFactory;
use App\Http\Controllers\Controller;
use App\Interfaces\Services\PinServiceInterface;
use App\Models\Plan;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class PinController extends Controller
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected PinServiceInterface $pinService
    )
    {
        parent::__construct($responseFactory, $authManager);
    }

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
    public function index(Plan $plan): JsonResponse
    {
        return $this->responseFactory->json($this->pinService->getPinsByPlan($plan));
    }
}
