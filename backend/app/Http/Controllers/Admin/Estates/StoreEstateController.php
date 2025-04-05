<?php

namespace App\Http\Controllers\Admin\Estates;

use App\Dto\EstateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEstateRequest;
use App\Models\City;
use App\Services\EstateService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StoreEstateController extends Controller
{
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
    public function __invoke(
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
                city: City::find($request->input('city')),
                house_number: $request->input('house_number'),
            );

            return $this->responseFactory->json($estateService->createEstate($estateDto), 201);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
