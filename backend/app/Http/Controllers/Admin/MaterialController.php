<?php

namespace App\Http\Controllers\Admin;

use App\Dto\MaterialDto;
use App\Http\Controllers\Controller;
use App\Factory\ResponseFactory;
use App\Http\Requests\CreateMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use Illuminate\Auth\AuthManager;
use App\Interfaces\Services\MaterialServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MaterialController extends Controller
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected MaterialServiceInterface $materialService,
    )
    {
        parent::__construct($responseFactory, $authManager);
    }

    public function list(): JsonResponse
    {
        return $this->responseFactory->json($this->materialService->getAllMaterials());
    }

    public function show(Material $material): JsonResponse
    {
        try {
            return $this->responseFactory->json($material);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function store(CreateMaterialRequest $request): JsonResponse
    {
        try {
            $materialDto = new MaterialDto(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count'),
                price: $request->input('price')
            );

            $tool = $this->materialService->createMaterial($materialDto);
            return $this->responseFactory->json($tool, 201);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(UpdateMaterialRequest $request, Material $material): JsonResponse
    {
        try {
            $materialDto = new MaterialDto(
                name: $request->input('name'),
                description: $request->input('description'),
                count: $request->input('count'),
                price: $request->input('price')
            );

            if (!$this->materialService->updateMaterial($material, $materialDto)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')], 400);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(Material $material): JsonResponse
    {
        try {
            if (!$this->materialService->deleteMaterial($material)) {
                return $this->responseFactory->json(['message' => __('app.action.failed')]);
            }

            return $this->responseFactory->json(['message' => __('app.action.success')]);
        } catch (Throwable $e) {
            return $this->responseFactory->json([$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
