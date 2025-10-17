<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cars;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ShowCarController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/cars/{car}",
     *     summary="Show car details",
     *     description="Get detailed information about a specific car.",
     *     operationId="showCar",
     *     tags={"Admin Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Car details retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="cars"),
     *                 @OA\Property(property="id", type="string", example="1"),
     *                 @OA\Property(
     *                     property="attributes",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Toyota Camry"),
     *                     @OA\Property(property="description", type="string", example="A reliable sedan", nullable=true),
     *                     @OA\Property(property="registration_number", type="string", example="ABC123"),
     *                     @OA\Property(property="technical_details", type="string", example="2.5L Engine", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
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
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function __invoke(
        Car $car,
        CarService $carService
    ): JsonResponse {
        try {
            $carDetails = $carService->getCarById($car->id);

            if (! $carDetails) {
                return $this->responseFactory->json(
                    ['message' => __('app.car.not_found')],
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->responseFactory->json(
                new CarResource($carDetails),
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            $this->logger->error('Error retrieving car details', [
                'car_id' => $car->id,
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
