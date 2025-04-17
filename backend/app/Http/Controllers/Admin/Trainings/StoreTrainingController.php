<?php

namespace App\Http\Controllers\Admin\Trainings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Training\StoreTrainingRequest;
use App\Services\Training\TrainingService;
use Exception;
use Illuminate\Http\JsonResponse;

class StoreTrainingController extends Controller
{
    public function __invoke(StoreTrainingRequest $request, TrainingService $service): JsonResponse
    {
        try {
            $training = $service->store($request->validated());
            return $this->responseFactory->successResponse($training, 201);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->responseFactory->json([
                'message' => __('messages.training_store_failed')
            ], 400);
        }
    }
}
