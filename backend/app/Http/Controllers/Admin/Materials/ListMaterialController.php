<?php

namespace App\Http\Controllers\Admin\Materials;

use App\Http\Controllers\Controller;
use App\Services\MaterialService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ListMaterialController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/materials/list",
     *     summary="Get list of materials",
     *     description="Retrieve a list of all available materials.",
     *     operationId="getMaterialsList",
     *     tags={"Admin Materials"},
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
    public function __invoke(MaterialService $materialService): JsonResponse
    {
        try {
            return $this->responseFactory->json($materialService->getAllMaterials());
        } catch (Throwable $e) {
            $this->logger->error('Error retrieving materials', [
                'exception' => $e,
            ]);
            return $this->responseFactory->json(
                ['message' => __('Coś poszło nie tak. Spróbuj ponownie później.')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
