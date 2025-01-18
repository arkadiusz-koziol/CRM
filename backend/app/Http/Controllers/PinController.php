<?php
namespace App\Http\Controllers;

use App\Factory\ResponseFactory;
use App\Http\Requests\CreatePinRequest;
use App\Interfaces\Services\PinServiceInterface;
use App\Models\Plan;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;

class PinController extends Controller
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected PinServiceInterface $pinService
    ) {
        parent::__construct($responseFactory, $authManager);
    }

    public function store(CreatePinRequest $request, Plan $plan): JsonResponse
    {
        $user = $this->authManager->guard()->user();

        return $this->responseFactory->json($this->pinService->createPin(array_merge(
            $request->validated(),
            ['user_id' => $user->id]
        ), $plan), 201);
    }

    public function index(Plan $plan): JsonResponse
    {
        $user = $this->authManager->guard()->user();

        return $this->responseFactory->json($this->pinService
            ->getPinsByPlan($plan)
            ->where('user_id', $user->id));
    }
}
