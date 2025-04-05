<?php

namespace App\Http\Controllers\Admin\Cities;

use App\Dto\CityDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use App\Services\CityService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CreateCityController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/cities",
     *     summary="Create a new city",
     *     description="Create a new city with the provided details.",
     *     operationId="createCity",
     *     tags={"Admin Cities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "district", "commune", "voivodeship"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Wrocław",
     *                 description="Name of the city"
     *             ),
     *             @OA\Property(
     *                 property="district",
     *                 type="string",
     *                 example="Wrocław",
     *                 description="District of the city"
     *             ),
     *              @OA\Property(
     *                  property="commune",
     *                  type="string",
     *                  example="Wrocław",
     *                  description="Commune of the city"
     *              ),
     *              @OA\Property(
     *                  property="voivodeship",
     *                  type="string",
     *                  example="Dolnośląskie",
     *                  description="Voivodeship of the city"
     *              ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="City created successfully",
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
        CreateCityRequest $request,
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

            return $this->responseFactory->json($cityService->createCity($cityDto), 201);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
