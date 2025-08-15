<?php

namespace App\Http\Controllers\Admin\Cities;

use App\Http\Controllers\Controller;
use App\Services\CityService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ListCityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/cities/list",
     *     summary="List all cities",
     *     description="Retrieve a list of all cities.",
     *     operationId="listCities",
     *     tags={"Admin Cities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of cities retrieved successfully",
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
    public function __invoke(CityService $cityService): JsonResponse
    {
        try {
            return $this->responseFactory->json($cityService->getAllCities());
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
