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

class UpdateMaterialController extends Controller
{
    /**
     * @OA\Put(
     *     path="/v1/admin/materials/{id}",
     *     summary="Update an existing material",
     *     description="Update the details of an existing material.",
     *     operationId="updateMaterial",
     *     tags={"Admin Materials"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the material to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "count", "price"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Steel",
     *                 description="Updated name of the material"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Updated high-quality steel",
     *                 description="Updated description of the material"
     *             ),
     *             @OA\Property(
     *                 property="count",
     *                 type="integer",
     *                 example=450,
     *                 description="Updated count of the material"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="number",
     *                 format="integer",
     *                 example=140,
     *                 description="Updated price of the material"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Action successful"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Action failed"
     *             )
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
        UpdateMaterialRequest $request,
        Material $material,
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

            if (!$materialService->updateMaterial($material, $materialDto)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')], 400);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'material_id' => $material->id,
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
