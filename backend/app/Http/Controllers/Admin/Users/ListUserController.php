<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ListUserController extends Controller
{
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
    public function __invoke(UserService $userService): JsonResponse
    {
        return $this->responseFactory->successResponse($userService->getAllUsers());
    }
}
