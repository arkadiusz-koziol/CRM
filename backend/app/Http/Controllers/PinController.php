<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePinRequest;
use App\Models\Plan;
use App\Services\PinService;
use Illuminate\Http\JsonResponse;

class PinController extends Controller
{
    public function store(
        CreatePinRequest $request,
        Plan $plan,
        PinService $pinService
    ): JsonResponse
    {
        return $this->responseFactory->json(
            $pinService->createPin(array_merge(
                $request->validated(),
                [
                    'user_id' => $this->authManager->guard()->user()->id
                ]
            ), $plan), 201);
    }

    public function index(
        Plan $plan,
        PinService $pinService
    ): JsonResponse
    {
        return $this->responseFactory->json(
            $pinService->getPinsByPlan($plan)
                ->where('user_id', $this->authManager->guard()->user()->id)
        );
    }
}
