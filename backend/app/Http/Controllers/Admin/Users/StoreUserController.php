<?php

namespace App\Http\Controllers\Admin\Users;

use App\Factory\CreateUserDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class StoreUserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/users/user",
     *     tags={"Admin Users"},
     *     summary="Create a new user",
     *     description="Creates a new user and returns the user details.",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "surname", "email", "password"},
     *
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="surname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password",
     *      example="password123"),
     *             @OA\Property(property="phone", type="string", example="111222333"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function __invoke(
        CreateUserRequest $request,
        UserService $userService,
        CreateUserDtoFactory $dtoFactory,
        AuthManager $auth
    ): JsonResponse {
        try {
            $dto = $dtoFactory->fromRequest($request);

            return $this->responseFactory->successResponse(
                $userService->createUser($dto)
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [
                'user_id' => $auth->id(),
            ]);

            return $this->responseFactory->json([
                'message' => __('messages.user_creation_failed'),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
