<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cars;

use App\Factory\CarDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UpdateCarController extends Controller
{
    /**
     * @OA\Put(
     *     path="/v1/admin/cars/{car}",
     *     summary="Update car",
     *     description="Update an existing car with new information.",
     *     operationId="updateCar",
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
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","registration_number"},
     *
     *             @OA\Property(property="name", type="string", example="BMW X5"),
     *             @OA\Property(property="description", type="string", example="Luxury SUV", nullable=true),
     *             @OA\Property(property="registration_number", type="string", example="ABC123"),
     *             @OA\Property(property="technical_details", type="string", example="V8 Engine", nullable=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Car updated successfully",
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
     *                     @OA\Property(property="name", type="string", example="BMW X5"),
     *                     @OA\Property(property="description", type="string", example="Luxury SUV"),
     *                     @OA\Property(property="registration_number", type="string", example="ABC123"),
     *                     @OA\Property(property="technical_details", type="string", example="V8 Engine"),
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
     *         response=422,
     *         description="Validation error"
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
        UpdateCarRequest $request,
        Car $car,
        CarService $carService,
        CarDtoFactory $carDtoFactory
    ): JsonResponse {
        try {
            $carDto = $carDtoFactory->fromArray([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'registration_number' => $request->input('registration_number'),
                'technical_details' => $request->input('technical_details'),
            ]);

            $updated = $carService->updateCar($car, $carDto);

            if (! $updated) {
                return response()->json(
                    ['message' => __('app.action.failed')],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            $car->refresh();

            return response()->json(
                new CarResource($car->toArray()),
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            Log::error('Error updating car', [
                'car_id' => $car->id,
                'exception' => $e,
            ]);

            return response()->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
