<?php

namespace App\Http\Controllers\Admin\Cars;

use App\Factory\CarDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCarRequest;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CreateCarController extends Controller
{
    public function __invoke(
        CreateCarRequest $request,
        CarService $carService,
        CarDtoFactory $carDtoFactory,
    ): JsonResponse {
        try {
            $carDto = $carDtoFactory->fromRequest(
                name: $request->input('name'),
                description: $request->input('description'),
                registrationNumber: $request->input('registration_number'),
                technicalDetails: $request->input('technical_details')
            );

            return $this->responseFactory->json($carService->createCar($carDto), Response::HTTP_CREATED);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
