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

class StoreMaterialController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/materials",
     *     summary="Create a new material",
     *     description="Create a new material with the provided details.",
     *     operationId="createMaterial",
     *     tags={"Admin Materials"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "count", "price"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Steel",
     *                 description="Name of the material"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="High-quality steel",
     *                 description="Description of the material"
     *             ),
     *             @OA\Property(
     *                 property="count",
     *                 type="integer",
     *                 example=500,
     *                 description="Count of the material"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="number",
     *                 format="integer",
     *                 example=150,
     *                 description="Price of the material"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Material created successfully",
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
    public function __invoke(
        CreateMaterialRequest $request,
        MaterialService $materialService
    ): JsonResponse
    {
        try {
            $materialDto = new MaterialDto(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count'),
                price: $request->input('price')
            );

            return $this->responseFactory->json($materialService->createMaterial($materialDto), 201);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
