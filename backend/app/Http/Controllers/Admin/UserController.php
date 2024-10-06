<?php

namespace App\Http\Controllers\Admin;

use App\Factory\ResponseFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CreateUserRequest;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager
    ) {
        parent::__construct($responseFactory, $authManager);
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return $this->responseFactory->successResponse($user, 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->responseFactory->errorResponse(__('errors.user_not_found'), 404);
        }

        return $this->responseFactory->successResponse($user);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->responseFactory->errorResponse(__('errors.user_not_found'), 404);
        }

        $this->userService->updateUser($user, $request->validated());

        return $this->responseFactory->successResponse($user);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->responseFactory->errorResponse(__('errors.user_not_found'), 404);
        }

        $this->userService->deleteUser($user);

        return $this->responseFactory->successResponse(['message' => __('messages.user_deleted')]);
    }

    public function list(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return $this->responseFactory->successResponse($users);
    }
}
