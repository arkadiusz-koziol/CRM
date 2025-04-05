<?php

namespace App\Http\Controllers\Admin\Materials;

use App\Dto\MaterialDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use App\Services\MaterialService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ShowMaterialController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/materials/{id}",
     *     summary="Get a single material",
     *     description="Retrieve a specific material by its ID.",
     *     operationId="getMaterialById",
     *     tags={"Admin Materials"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the material",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             additionalProperties=true
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
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
    public function __invoke(Material $material): JsonResponse
    {
        try {
            return $this->responseFactory->json($material);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'material_id' => $material->id,
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
