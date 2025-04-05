<?php

namespace App\Http\Controllers\Admin\Cities;

use App\Dto\CityDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use App\Services\CityService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UpdateCityController extends Controller
{

    /**
     * @OA\Put(
     *     path="/v1/admin/cities/{id}",
     *     summary="Update an existing city",
     *     description="Update the details of an existing city.",
     *     operationId="updateCity",
     *     tags={"Admin Cities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the city to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "district", "commune", "voivodeship"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Bolesławiec",
     *                 description="Updated name of the city"
     *             ),
     *             @OA\Property(
     *                 property="district",
     *                 type="string",
     *                 example="Bolesławiec",
     *                 description="Updated district of the city"
     *             ),
     *             @OA\Property(
     *                  property="commune",
     *                  type="string",
     *                  example="Bolesławiec",
     *                  description="Updated commune of the city"
     *              ),
     *              @OA\Property(
     *                   property="voivodeship",
     *                   type="string",
     *                   example="Dolnośląskie",
     *                   description="Updated voivodeship of the city"
     *               ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City updated successfully",
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
        UpdateCityRequest $request,
        City $city,
        CityService $cityService
    ): JsonResponse
    {
        try {
            $cityDto = new CityDto(
                name: $request->input('name'),
                district: $request->input('district'),
                commune: $request->input('commune'),
                voivodeship: $request->input('voivodeship')
            );

            if (!$cityService->updateCity($city, $cityDto)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')], 400);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
