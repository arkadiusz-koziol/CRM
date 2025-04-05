<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class UpdateUserController extends Controller
{
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
    public function __invoke(
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
}
