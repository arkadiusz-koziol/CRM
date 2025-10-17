<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cars;

use App\Factory\CarDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCarRequest;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CreateCarController extends Controller
{
    public function __invoke(
        CreateCarRequest $request,
        CarService $carService,
        CarDtoFactory $carDtoFactory,
    ): JsonResponse {
        try {
            $carDto = $carDtoFactory->fromArray([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'registration_number' => $request->input('registration_number'),
                'technical_details' => $request->input('technical_details')
            ]);

            return $this->responseFactory->json($carService->createCar($carDto), Response::HTTP_CREATED);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
