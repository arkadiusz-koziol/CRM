<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserRegistrationController extends Controller
{
    public function __invoke(
        CreateUserRequest $request,
        UserService $userService
    ): JsonResponse
    {
        $user = $userService->createUser($request->validated());

        return $this->responseFactory->successResponse([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }
}
