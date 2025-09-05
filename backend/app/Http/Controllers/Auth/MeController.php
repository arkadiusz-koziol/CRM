<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\MeResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class MeController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="MeResource",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John"),
     *     @OA\Property(property="surname", type="string", example="Doe"),
     *     @OA\Property(property="email", type="string", example="john@example.com"),
     *     @OA\Property(property="phone", type="string", example="+1234567890"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     *
     * @OA\Get(
     *     path="/v1/auth/me",
     *     tags={"Auth"},
     *     summary="Get current user profile",
     *     description="Get the profile information of the currently authenticated user.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/MeResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function __invoke(): JsonResponse
    {
        $user = $this->authManager->user();

        if (!$user) {
            return $this->responseFactory->errorResponse('Unauthenticated', 401);
        }

        return $this->responseFactory->successResponse(new MeResource($user));
    }
}
