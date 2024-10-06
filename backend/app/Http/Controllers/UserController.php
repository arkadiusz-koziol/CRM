<?php

namespace App\Http\Controllers;

use App\Factory\ResponseFactory;
use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;

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

        return response()->json($user, 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json(['message' => __('errors.user_not_found')], 404);
        }

        return response()->json($user);
    }

    public function update(CreateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json(['message' => __('errors.user_not_found')], 404);
        }

        $this->userService->updateUser($user, $request->validated());

        return response()->json($user);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json(['message' => __('errors.user_not_found')], 404);
        }

        $this->userService->deleteUser($user);

        return response()->json(['message' => __('messages.user_deleted')]);
    }
}
