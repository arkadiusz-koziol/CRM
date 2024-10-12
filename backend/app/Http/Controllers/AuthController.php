<?php

namespace App\Http\Controllers;

use App\Factory\ResponseFactory;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager
    ) {
        parent::__construct($responseFactory, $authManager);
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Log in a user and generate token",
     *     description="Authenticate a user using email and password, and generate a Bearer token for API access.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@admin.pl"),
     *             @OA\Property(property="password", type="string", format="password", example="admin123!"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="Bearer token_example"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$this->authManager->attempt($credentials)) {
            return $this->responseFactory->errorResponse(__('errors.invalid_credentials'), 401);
        }

        $user = $this->authManager->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->responseFactory->successResponse(['token' => $token, 'user' => $user]);
    }

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
    public function logout(): JsonResponse
    {
        $user = $this->authManager->user();
        $user->tokens()->delete();

        return $this->responseFactory->successResponse(['message' => __('messages.logged_out')]);
    }
}
