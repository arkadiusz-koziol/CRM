<?php

namespace App\Http\Controllers\Admin\Estates;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Services\EstateService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ShowEstateController extends Controller
{
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
    public function __invoke(
        Estate $estate,
        EstateService $estateService
    ): JsonResponse
    {
        return $this->responseFactory->json($estateService->findEstateById($estate->id));
    }
}
