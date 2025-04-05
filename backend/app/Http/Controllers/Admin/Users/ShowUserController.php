<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ShowUserController extends Controller
{
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
    public function __invoke(User $user): JsonResponse
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
}
