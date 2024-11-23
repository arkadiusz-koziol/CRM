<?php

namespace App\Http\Controllers;

use App\Factory\ResponseFactory;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager
    ) {
        parent::__construct($responseFactory, $authManager);
    }

    public function show(User $user): JsonResponse
    {
        return $this->responseFactory->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->userService->updateUser($user, $request->validated());

        return $this->responseFactory->json($user);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $this->authManager->user();

        if (!Hash::check($request->password, $user->password)) {
            return $this->responseFactory->errorResponse(__('errors.invalid_password'), 400);
        }

        $this->userService->changePassword($user, $request->new_password);

        return $this->responseFactory->successResponse(['message' => __('messages.password_changed')]);
    }

}
