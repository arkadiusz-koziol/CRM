<?php

namespace App\Http\Controllers\Admin;

use App\Factory\ResponseFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePlanRequest;
use App\Interfaces\Services\PlanServiceInterface;
use App\Models\Estate;
use App\Models\Plan;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected PlanServiceInterface $planService
    )
    {
        parent::__construct($responseFactory, $authManager);
    }

    public function store(CreatePlanRequest $request, Estate $estate): JsonResponse
    {
        return $this->responseFactory->json
        (
            $this->planService->createPlan
            (
                $request->validated(),
                $estate
            ),
            201
        );
    }

    public function index(Estate $estate): JsonResponse
    {
        return $this->responseFactory->json($this->planService->getPlansByEstate($estate));
    }

    public function destroy(Plan $plan): JsonResponse
    {
        $this->planService->deletePlan($plan);

        return $this->responseFactory->json(['message' => __('app.action.success')]);
    }
}
