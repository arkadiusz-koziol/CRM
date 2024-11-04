<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Dto\MaterialDto;
use App\Http\Requests\CreateMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use App\Factory\ResponseFactory;
use Illuminate\Auth\AuthManager;
use App\Interfaces\Services\MaterialServiceInterface;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MaterialController extends Controller
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected MaterialServiceInterface $materialService,
    )
    {
        parent::__construct($responseFactory, $authManager);
    }

    /**
     * @OA\Get(
     *     path="/v1/admin/materials/list",
     *     summary="Get list of materials",
     *     description="Retrieve a list of all available materials.",
     *     operationId="getMaterialsList",
     *     tags={"Admin Materials"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 additionalProperties=true
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
    public function list(): JsonResponse
    {
        return $this->responseFactory->json($this->materialService->getAllMaterials());
    }

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
    public function show(Material $material): JsonResponse
    {
        try {
            return $this->responseFactory->json($material);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

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
    public function store(CreateMaterialRequest $request): JsonResponse
    {
        try {
            $materialDto = new MaterialDto(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count'),
                price: $request->input('price')
            );

            $material = $this->materialService->createMaterial($materialDto);
            return $this->responseFactory->json($material, 201);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

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
    public function update(UpdateMaterialRequest $request, Material $material): JsonResponse
    {
        try {
            $materialDto = new MaterialDto(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count'),
                price: $request->input('price')
            );

            if (!$this->materialService->updateMaterial($material, $materialDto)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')], 400);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/admin/materials/{id}",
     *     summary="Delete a material",
     *     description="Delete an existing material by its ID.",
     *     operationId="deleteMaterial",
     *     tags={"Admin Materials"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the material to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material deleted successfully",
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
    public function destroy(Material $material): JsonResponse
    {
        try {
            if (!$this->materialService->deleteMaterial($material)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')]);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
