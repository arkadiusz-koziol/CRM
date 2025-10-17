<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cars;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DeleteCarController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/v1/admin/cars/{car}",
     *     summary="Delete car",
     *     description="Delete an existing car from the system.",
     *     operationId="deleteCar",
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
     *         response=204,
     *         description="Car deleted successfully"
     *     ),
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
            $deleted = $carService->deleteCar($car);

            if (! $deleted) {
                return $this->responseFactory->json(
                    ['message' => __('app.action.failed')],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return $this->responseFactory->json(
                null,
                Response::HTTP_NO_CONTENT
            );
        } catch (Throwable $e) {
            $this->logger->error('Error deleting car', [
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

