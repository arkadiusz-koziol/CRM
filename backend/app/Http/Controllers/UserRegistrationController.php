<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Factory\ResponseFactory;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;

class UserRegistrationController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager
    ) {
        parent::__construct($responseFactory, $authManager);
    }

    public function __invoke(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->responseFactory->successResponse([
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
