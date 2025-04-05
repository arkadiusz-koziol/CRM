<?php

namespace App\Http\Controllers\Admin\Plans;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Services\PlanService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ShowPlanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/plans/{estate}",
     *     tags={"Admin Plans"},
     *     summary="Get plans for an estate",
     *     description="Retrieves all plans associated with a specific estate.",
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
     *         description="List of plans",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Plan")
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
            return $this->responseFactory->json($planService->getPlansByEstate($estate));
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
                'message' => __('Błąd podczas pobierania planów')
            ], 500);
        }
    }
}
