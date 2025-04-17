<?php

namespace App\Http\Controllers\Admin\Trainings;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Services\Training\TrainingService;
use Exception;
use Illuminate\Http\JsonResponse;

class DestroyTrainingController extends Controller
{
    public function __invoke(Training $training, TrainingService $service): JsonResponse
    {
        try {
            $service->delete($training);
            return $this->responseFactory->successResponse(['message' => __('messages.training_deleted')]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->responseFactory->json([
                'message' => __('messages.training_delete_failed')
            ], 400);
        }
    }
}
