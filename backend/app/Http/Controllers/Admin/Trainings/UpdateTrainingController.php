<?php

namespace App\Http\Controllers\Admin\Trainings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Training\UpdateTrainingRequest;
use App\Models\Training;
use App\Services\Training\TrainingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class UpdateTrainingController extends Controller
{
    public function __invoke(UpdateTrainingRequest $request, Training $training, TrainingService $service): JsonResponse
    {
        try {
            return $this->responseFactory->successResponse($service->update($training, $request->validated()));
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->responseFactory->json([
                'message' => __('messages.training_update_failed')
            ], 400);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            return $this->responseFactory->json([
                'message' => __('messages.training_update_failed')
            ], 500);
        }
    }
}
