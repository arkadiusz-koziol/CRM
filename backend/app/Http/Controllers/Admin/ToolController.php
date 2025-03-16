<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Dto\ToolDTO;
use App\Http\Requests\CreateToolRequest;
use App\Http\Requests\UpdateToolRequest;
use App\Models\Tool;
use App\Services\ToolService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ToolController extends Controller
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

    public function list(ToolService $toolService): JsonResponse
    {
        return $this->responseFactory->json($toolService->getAllTools());
    }

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

    public function show(Tool $tool): JsonResponse
    {
        try {
            return $this->responseFactory->json($tool);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


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

    public function store(
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
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

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

    public function update(
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
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/admin/tools/{id}",
     *     summary="Delete a tool",
     *     description="Delete an existing tool by its ID.",
     *     operationId="deleteTool",
     *     tags={"Admin Tools"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the tool to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tool deleted successfully",
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

    public function destroy(
        Tool $tool,
        ToolService $toolService
    ): JsonResponse
    {
        try {
            if (!$toolService->deleteTool($tool)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')]);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
