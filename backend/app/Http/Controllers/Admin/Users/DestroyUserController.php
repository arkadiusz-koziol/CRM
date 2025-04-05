<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class DestroyUserController extends Controller
{
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
    public function __invoke(
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
}
