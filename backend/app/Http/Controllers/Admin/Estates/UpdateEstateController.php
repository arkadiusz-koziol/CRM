<?php

namespace App\Http\Controllers\Admin\Estates;

use App\Dto\EstateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Estate\UpdateEstateRequest;
use App\Models\City;
use App\Models\Estate;
use App\Services\EstateService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UpdateEstateController extends Controller
{
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
    public function __invoke(
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
                city: City::find($request->input('city')),
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
}
