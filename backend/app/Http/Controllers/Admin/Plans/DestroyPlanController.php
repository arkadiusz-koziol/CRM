<?php

namespace App\Http\Controllers\Admin\Plans;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Services\PlanService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class DestroyPlanController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/v1/admin/plans/{estate}",
     *     tags={"Admin Plans"},
     *     summary="Delete plans for an estate",
     *     description="Deletes all plans associated with a specific estate.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="estate",
     *         in="path",
     *         description="Estate ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plans deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Action successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plans not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plans not found")
     *         )
     *     )
     * )
     */
    public function __invoke(
        Estate $estate,
        PlanService $planService
    ): JsonResponse
    {
        try {
            $planService->deletePlan($planService->getPlansByEstate($estate));

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (ModelNotFoundException $e) {
            $this->logger->error($e->getMessage(), [
                'estate_id' => $estate->id,
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([
                'message' => __('Nie znaleziono planu dla tej nieruchomości')
            ], 404);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'estate_id' => $estate->id,
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([
                'message' => __('Błąd podczas usuwania planów')
            ], 500);
        }
    }
}
