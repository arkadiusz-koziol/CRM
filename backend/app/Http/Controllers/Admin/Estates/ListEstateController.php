<?php

namespace App\Http\Controllers\Admin\Estates;

use App\Http\Controllers\Controller;
use App\Services\EstateService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Throwable;

class ListEstateController extends Controller
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
    public function __invoke(EstateService $estateService): JsonResponse
    {
        try {
            return $this->responseFactory->json($estateService->getAllEstates());
        } catch (Throwable $e) {
            $this->logger->error('Error retrieving estates', [
                'exception' => $e,
            ]);
            return $this->responseFactory->json(
                ['message' => __('Coś poszło nie tak. Spróbuj ponownie później.')],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
