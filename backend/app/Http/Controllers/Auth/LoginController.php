<?php

namespace App\Http\Controllers\Auth;

use App\Dto\AuthDto;
use App\Enums\UserRoles;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class LoginController extends Controller
{
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

    public function __invoke(LoginRequest $request, AuthService $authService): JsonResponse
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
}
