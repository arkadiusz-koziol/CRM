<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Materials;

use App\Exceptions\UsableCountUnavailableException;
use App\Http\Controllers\Controller;
use App\Services\GetUsableMaterialCountService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

final class GetUsableMaterialCountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/materials/usable-count",
     *     summary="Get usable materials count",
     *     description="Retrieve the count of materials where count > 0 (usable materials).",
     *     operationId="getUsableMaterialCount",
     *     tags={"Materials"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="entity", type="string", example="materials"),
     *             @OA\Property(property="count", type="integer", example=456)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="error", type="string", example="USABLE_COUNT_UNAVAILABLE"),
     *             @OA\Property(property="message", type="string", example="Usable count could not be determined at this time.")
     *         )
     *     ),
     *
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
    public function __invoke(
        GetUsableMaterialCountService $service
    ): JsonResponse {
        try {
            $dto = $service->handle();

            return $this->responseFactory->json([
                'entity' => $dto->entity(),
                'count' => $dto->count(),
            ], Response::HTTP_OK);
        } catch (UsableCountUnavailableException $e) {
            return $this->responseFactory->json([
                'error' => 'USABLE_COUNT_UNAVAILABLE',
                'message' => 'Usable count could not be determined at this time.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
