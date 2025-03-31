<?php

namespace App\Http\Controllers\Admin;

use App\Dto\EstateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEstateRequest;
use App\Http\Requests\UpdateEstateRequest;
use App\Models\City;
use App\Models\Estate;
use App\Services\EstateService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EstateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/estates/list",
     *     summary="List all estates",
     *     description="Retrieve a list of all estates, including their related city information.",
     *     operationId="listEstates",
     *     tags={"Admin Estates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of estates retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Biedronka"
     *                 ),
     *                 @OA\Property(
     *                     property="custom_id",
     *                     type="string",
     *                     example="AZL123"
     *                 ),
     *                 @OA\Property(
     *                     property="street",
     *                     type="string",
     *                     example="Gubaszewska"
     *                 ),
     *                 @OA\Property(
     *                     property="postal_code",
     *                     type="string",
     *                     example="59-700"
     *                 ),
     *                 @OA\Property(
     *                     property="house_number",
     *                     type="string",
     *                     example="12a/3"
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=11
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Wrocław"
     *                     ),
     *                     @OA\Property(
     *                         property="district",
     *                         type="string",
     *                         example="Wrocław"
     *                     ),
     *                     @OA\Property(
     *                         property="commune",
     *                         type="string",
     *                         example="Wrocław"
     *                     ),
     *                     @OA\Property(
     *                         property="voivodeship",
     *                         type="string",
     *                         example="Dolnośląskie"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-11-23T18:01:55.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-11-23T18:01:55.000000Z"
     *                 )
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
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     )
     * )
     */
    public function list(EstateService $estateService): JsonResponse
    {
        return $this->responseFactory->json($estateService->getAllEstates());
    }

    /**
     * @OA\Post(
     *     path="/v1/admin/estates",
     *     summary="Create a new estate",
     *     description="Create a new estate with the provided data and associate it with an existing city.",
     *     operationId="createEstate",
     *     tags={"Admin Estates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "custom_id", "street", "postal_code", "city", "house_number"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Biedronka"
     *             ),
     *             @OA\Property(
     *                 property="custom_id",
     *                 type="string",
     *                 example="AZL123"
     *             ),
     *             @OA\Property(
     *                 property="street",
     *                 type="string",
     *                 example="Gubaszewska"
     *             ),
     *             @OA\Property(
     *                 property="postal_code",
     *                 type="string",
     *                 example="59-700"
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 type="integer",
     *                 description="The ID of the associated city",
     *                 example=11
     *             ),
     *             @OA\Property(
     *                 property="house_number",
     *                 type="string",
     *                 example="12a/3"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Estate created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Biedronka"
     *             ),
     *             @OA\Property(
     *                 property="custom_id",
     *                 type="string",
     *                 example="AZL123"
     *             ),
     *             @OA\Property(
     *                 property="street",
     *                 type="string",
     *                 example="Gubaszewska"
     *             ),
     *             @OA\Property(
     *                 property="postal_code",
     *                 type="string",
     *                 example="59-700"
     *             ),
     *             @OA\Property(
     *                 property="house_number",
     *                 type="string",
     *                 example="12a/3"
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=11
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Wrocław"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 example="2024-11-23T18:01:55.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time",
     *                 example="2024-11-23T18:01:55.000000Z"
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
     *                 example="Invalid data provided"
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
    public function store(
        CreateEstateRequest $request,
        EstateService $estateService
    ): JsonResponse
    {
        try {
            $estateDto = new EstateDto(
                name: $request->input('name'),
                custom_id: $request->input('custom_id'),
                street: $request->input('street'),
                postal_code: $request->input('postal_code'),
                city: City::firstOrFail($request->input('city')),
                house_number: $request->input('house_number'),
            );

            return $this->responseFactory->json($estateService->createEstate($estateDto), 201);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Put(
     *     path="/v1/admin/estates/{id}",
     *     summary="Update an existing estate",
     *     description="Update an existing estate with the provided data and associate it with an existing city.",
     *     operationId="updateEstate",
     *     tags={"Admin Estates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the estate to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "custom_id", "street", "postal_code", "city", "house_number"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Biedronka Updated"
     *             ),
     *             @OA\Property(
     *                 property="custom_id",
     *                 type="string",
     *                 example="AZL124"
     *             ),
     *             @OA\Property(
     *                 property="street",
     *                 type="string",
     *                 example="Updated Street"
     *             ),
     *             @OA\Property(
     *                 property="postal_code",
     *                 type="string",
     *                 example="59-701"
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 type="integer",
     *                 description="The ID of the associated city",
     *                 example=12
     *             ),
     *             @OA\Property(
     *                 property="house_number",
     *                 type="string",
     *                 example="15b/2"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estate updated successfully",
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
    public function update(
        UpdateEstateRequest $request,
        Estate $estate,
        EstateService $estateService
    ): JsonResponse
    {
        try {
            $estateDto = new EstateDto(
                name: $request->input('name'),
                custom_id: $request->input('custom_id'),
                street: $request->input('street'),
                postal_code: $request->input('postal_code'),
                city: City::firstOrFail($request->input('city')),
                house_number: $request->input('house_number'),
            );

            if (!$estateService->updateEstate($estate, $estateDto)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')], 400);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/admin/estates/{id}",
     *     summary="Delete an estate",
     *     description="Delete an existing estate by its ID.",
     *     operationId="deleteEstate",
     *     tags={"Admin Estates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the estate to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estate deleted successfully",
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
    public function destroy(
        Estate $estate,
        EstateService $estateService
    ): JsonResponse
    {
        try {
            if (!$estateService->deleteEstate($estate)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')]);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/admin/estates/{id}",
     *     summary="Get details of a specific estate",
     *     description="Retrieve detailed information about a specific estate by its ID, including associated city information.",
     *     operationId="getEstate",
     *     tags={"Admin Estates"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the estate to retrieve",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estate details retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Biedronka"
     *             ),
     *             @OA\Property(
     *                 property="custom_id",
     *                 type="string",
     *                 example="AZL123"
     *             ),
     *             @OA\Property(
     *                 property="street",
     *                 type="string",
     *                 example="Gubaszewska"
     *             ),
     *             @OA\Property(
     *                 property="postal_code",
     *                 type="string",
     *                 example="59-700"
     *             ),
     *             @OA\Property(
     *                 property="house_number",
     *                 type="string",
     *                 example="12a/3"
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=11
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Wrocław"
     *                 ),
     *                 @OA\Property(
     *                     property="district",
     *                     type="string",
     *                     example="Wrocław"
     *                 ),
     *                 @OA\Property(
     *                     property="commune",
     *                     type="string",
     *                     example="Wrocław"
     *                 ),
     *                 @OA\Property(
     *                     property="voivodeship",
     *                     type="string",
     *                     example="Dolnośląskie"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 example="2024-11-23T18:01:55.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time",
     *                 example="2024-11-23T18:01:55.000000Z"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estate not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Estate not found"
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
    public function show(
        Estate $estate,
        EstateService $estateService
    ): JsonResponse
    {
        return $this->responseFactory->json($estateService->findEstateById($estate->id));
    }
}
