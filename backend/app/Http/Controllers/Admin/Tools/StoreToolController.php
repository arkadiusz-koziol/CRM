<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Dto\ToolDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateToolRequest;
use App\Http\Requests\UpdateToolRequest;
use App\Models\Tool;
use App\Services\ToolService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StoreToolController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/admin/tools",
     *     summary="Create a new tool",
     *     description="Create a new tool with the provided details.",
     *     operationId="createTool",
     *     tags={"Admin Tools"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "count"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Młotek",
     *                 description="Name of the tool"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Czarny, mały, drewniany",
     *                 description="Description of the tool"
     *             ),
     *             @OA\Property(
     *                 property="count",
     *                 type="integer",
     *                 example=100,
     *                 description="Count of the tool"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tool created successfully",
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

    public function __invoke(
        CreateToolRequest $request,
        ToolService $toolService
    ): JsonResponse
    {
        try {
            $toolDTO = new ToolDTO(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count')
            );

            return $this->responseFactory->json($toolService->createTool($toolDTO), 201);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
