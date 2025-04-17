<?php

namespace App\Http\Controllers\Admin\Plans;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\CreatePlanRequest;
use App\Models\Estate;
use App\Services\PlanService;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class StorePlanController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/plans/{estate}",
     *     tags={"Admin Plans"},
     *     summary="Create a new plan for an estate",
     *     description="Creates a new plan by uploading a PDF and converting it to an image.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="estate",
     *         in="path",
     *         description="Estate ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="file", type="string", format="binary", description="PDF file of the plan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plan created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Plan")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function __invoke(
        CreatePlanRequest $request,
        Estate $estate,
        PlanService $planService
    ): JsonResponse
    {
        try {
            return $this->responseFactory->json(
                $planService->createPlan(
                    $request->validated(),
                    $estate
                ),
                201
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'estate_id' => $estate->id,
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([
                'message' => __('Błąd podczas dodawania planu')
            ], 500);
        }
    }
}
