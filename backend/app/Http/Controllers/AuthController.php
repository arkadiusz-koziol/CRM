<?php

namespace App\Http\Controllers;

use App\Factory\ResponseFactory;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager
    ) {
        parent::__construct($responseFactory, $authManager);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$this->authManager->attempt($credentials)) {
            return $this->responseFactory->errorResponse(__('errors.invalid_credentials'), 401);
        }

        $user = $this->authManager->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->responseFactory->successResponse(['token' => $token, 'user' => $user]);
    }

    public function logout(): JsonResponse
    {
        $user = $this->authManager->user();
        $user->tokens()->delete();

        return $this->responseFactory->successResponse(['message' => __('messages.logged_out')]);
    }
}
