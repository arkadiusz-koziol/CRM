<?php

namespace App\Http\Controllers\Admin;

use App\Factory\ResponseFactory;
use App\Http\Controllers\Controller;
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
    )
    {
        parent::__construct($responseFactory, $authManager);
    }

    public function index(Plan $plan): JsonResponse
    {
        return $this->responseFactory->json($this->pinService->getPinsByPlan($plan));
    }
}
