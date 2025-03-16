<?php

namespace App\Http\Controllers;

use App\Dto\AuthDto;
use App\Enums\UserRoles;
use App\Factory\ResponseFactory;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

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
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
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

    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        try {
            $authDto = new AuthDto(
                $request->input('email'),
                $request->input('password')
            );

            $response = $authService->authUser(
                $authDto,
                $request->input('remember', false),
                UserRoles::allowedForApi(),
            );

            return $this->responseFactory->json([
                'access_token' => $response,
            ]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], ResponseAlias::HTTP_UNAUTHORIZED);
        }
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
        $this->authManager->user()->tokens()->delete();

        return $this->responseFactory->successResponse(['message' => __('messages.logged_out')]);
    }
}
