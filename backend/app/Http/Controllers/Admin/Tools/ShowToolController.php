<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ShowToolController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/tools/{id}",
     *     summary="Get a single tool",
     *     description="Retrieve a specific tool by its ID.",
     *     operationId="getToolById",
     *     tags={"Admin Tools"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the tool",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             additionalProperties=true
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
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

    public function __invoke(Tool $tool): JsonResponse
    {
        try {
            return $this->responseFactory->json($tool);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'tool_id' => $tool->id,
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
