<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Dto\ToolDTO;
use App\Http\Requests\CreateToolRequest;
use App\Http\Requests\UpdateToolRequest;
use App\Interfaces\Services\ToolServiceInterface;
use App\Models\Tool;
use App\Factory\ResponseFactory;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ToolController extends Controller
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected ToolServiceInterface $toolService,
    )
    {
        parent::__construct($responseFactory, $authManager);
    }

    public function list(): JsonResponse
    {
        return $this->responseFactory->json($this->toolService->getAllTools());
    }

    public function show(Tool $tool): JsonResponse
    {
        try {
            return $this->responseFactory->json($tool);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function store(CreateToolRequest $request): JsonResponse
    {
        try {
            $toolDTO = new ToolDTO(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count')
            );

            $tool = $this->toolService->createTool($toolDTO);
            return $this->responseFactory->json($tool, 201);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(UpdateToolRequest $request, Tool $tool): JsonResponse
    {
        try {
            $toolDTO = new ToolDTO(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count')
            );

            if (!$this->toolService->updateTool($tool, $toolDTO)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')], 400);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(Tool $tool): JsonResponse
    {
        try {
            if (!$this->toolService->deleteTool($tool)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')]);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
