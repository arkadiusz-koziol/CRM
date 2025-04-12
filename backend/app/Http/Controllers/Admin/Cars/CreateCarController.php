<?php

namespace App\Http\Controllers\Admin\Cars;

use App\Dto\CarDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCarRequest;
use App\Models\Car;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Throwable;
use Symfony\Component\HttpFoundation\Response;


class CreateCarController extends Controller
{
    public function __invoke(
        CreateCarRequest $request,
        CarService $carService
    ): JsonResponse
    {
        try {
            $carDto = new CarDto(
                name: $request->input('name'),
                description: $request->input('description'),
                registrationNumber: $request->input('registration_number'),
                technicalDetails: $request->input('technical_details')
            );
            return $this->responseFactory->json($carService->createCar($carDto),Response::HTTP_CREATED);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
