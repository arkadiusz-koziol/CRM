<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Services\ToolService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ListToolController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/tools/list",
     *     summary="Get list of tools",
     *     description="Retrieve a list of all available tools.",
     *     operationId="getToolsList",
     *     tags={"Admin Tools"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 additionalProperties=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */

    public function __invoke(ToolService $toolService): JsonResponse
    {
        return $this->responseFactory->json($toolService->getAllTools());
    }
}
