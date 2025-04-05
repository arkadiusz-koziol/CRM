<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class LogoutController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/auth/logout",
     *     tags={"Auth"},
     *     summary="Log out a user",
     *     description="Log out the authenticated user by invalidating the Bearer token.",
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function __invoke(): JsonResponse
    {
        $this->authManager->user()->tokens()->delete();

        return $this->responseFactory->successResponse(['message' => __('messages.logged_out')]);
    }
}
