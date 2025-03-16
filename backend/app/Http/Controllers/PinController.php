<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePinRequest;
use App\Interfaces\Services\PinServiceInterface;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;

class PinController extends Controller
{
    public function store(
        CreatePinRequest $request,
        Plan $plan,
        PinServiceInterface $pinService
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
        PinServiceInterface $pinService
    ): JsonResponse
    {
        return $this->responseFactory->json(
            $pinService->getPinsByPlan($plan)
                ->where('user_id', $this->authManager->guard()->user()->id)
        );
    }
}
