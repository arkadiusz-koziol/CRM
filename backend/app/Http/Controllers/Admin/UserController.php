<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CreateUserRequest;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/users/user",
     *     tags={"Admin Users"},
     *     summary="Create a new user",
     *     description="Creates a new user and returns the user details.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "surname", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="surname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="phone", type="string", example="111222333"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function store(
        CreateUserRequest $request,
        UserService $userService
    ): JsonResponse
    {
        try {
            return $this->responseFactory->successResponse($userService->createUser($request->validated()), 201);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([
                'message' => __('messages.user_creation_failed')
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/admin/users/user/{id}",
     *     tags={"Admin Users"},
     *     summary="Get a user by ID",
     *     description="Returns the details of a user based on the given ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function show(User $user): JsonResponse
    {
        try {
            return $this->responseFactory->successResponse($user);

        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([
                'message' => __('messages.user_not_found')
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/v1/admin/users/user/{id}",
     *     tags={"Admin Users"},
     *     summary="Update a user by ID",
     *     description="Updates the details of a user based on the given ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="surname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="phone", type="string", example="111222333"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function update(
        UpdateUserRequest $request,
        User $user,
        UserService $userService
    ): JsonResponse
    {
        try {
            $userService->updateUser($user, $request->validated());

            return $this->responseFactory->successResponse($user);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([
                'message' => __('messages.user_not_found')
            ], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/admin/users/user/{id}",
     *     tags={"Admin Users"},
     *     summary="Delete a user by ID",
     *     description="Deletes the user with the given ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function destroy(
        User $user,
        UserService $userService
    ): JsonResponse
    {
        try {
            $userService->deleteUser($user);

            return $this->responseFactory->successResponse(['message' => __('messages.user_deleted')]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([
                'message' => __('messages.user_not_found')
            ], 404);
        }

    }

    /**
     * @OA\Get(
     *     path="/v1/admin/users/list",
     *     tags={"Admin Users"},
     *     summary="Get list of users",
     *     description="Returns a list of all users.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function list(UserService $userService): JsonResponse
    {
        return $this->responseFactory->successResponse($userService->getAllUsers());
    }
}
