<?php

namespace App\Http\Controllers\Admin;

use App\Dto\CityDto;
use App\Factory\ResponseFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Interfaces\Services\CityServiceInterface;
use App\Models\City;
use App\Models\Material;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CityController extends Controller
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected CityServiceInterface $cityService,
    )
    {
        parent::__construct($responseFactory, $authManager);
    }


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
 *                  @OA\Property(
     *                  property="commune",
     *                  type="string",
     *                  example="Wrocław",
     *                  description="Commune of the city"
     *              ),
 *                  @OA\Property(
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
    public function store(CreateCityRequest $request): JsonResponse
    {
        try {
            $cityDto = new CityDto(
                name: $request->input('name'),
                district: $request->input('district'),
                commune: $request->input('commune'),
                voivodeship: $request->input('voivodeship')
            );

            $city = $this->cityService->createCity($cityDto);
            return $this->responseFactory->json($city, 201);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }



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
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        try {
            $cityDto = new CityDto(
                name: $request->input('name'),
                district: $request->input('district'),
                commune: $request->input('commune'),
                voivodeship: $request->input('voivodeship')
            );

            if (!$this->cityService->updateCity($city, $cityDto)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')], 400);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }



    /**
     * @OA\Delete(
     *     path="/v1/admin/cities/{id}",
     *     summary="Delete a city",
     *     description="Delete an existing city by its ID.",
     *     operationId="deleteCity",
     *     tags={"Admin Cities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the city to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City deleted successfully",
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
    public function destroy(City $city): JsonResponse
    {
        try {
            if (!$this->cityService->deleteCity($city)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')]);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
