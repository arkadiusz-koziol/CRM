<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Dto\ToolDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tool\UpdateToolRequest;
use App\Models\Tool;
use App\Services\ToolService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UpdateToolController extends Controller
{
    /**
     * @OA\Put(
     *     path="/v1/admin/tools/{id}",
     *     summary="Update an existing tool",
     *     description="Update the details of an existing tool.",
     *     operationId="updateTool",
     *     tags={"Admin Tools"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the tool to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "count"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Młotek",
     *                 description="Updated name of the tool"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Duży, gumowy, różowy.",
     *                 description="Updated description of the tool"
     *             ),
     *             @OA\Property(
     *                 property="count",
     *                 type="integer",
     *                 example=99,
     *                 description="Updated count of the tool"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tool updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Action successful"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Action failed"
     *             )
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

    public function __invoke(
        UpdateToolRequest $request,
        Tool $tool,
        ToolService $toolService
    ): JsonResponse
    {
        try {
            $toolDTO = new ToolDTO(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count')
            );

            if (!$toolService->updateTool($tool, $toolDTO)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')], 400);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'tool_id' => $tool->id,
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
