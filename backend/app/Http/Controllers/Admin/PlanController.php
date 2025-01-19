<?php

namespace App\Http\Controllers\Admin;

use App\Factory\ResponseFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePlanRequest;
use App\Interfaces\Services\PlanServiceInterface;
use App\Models\Estate;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class PlanController extends Controller
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected PlanServiceInterface $planService
    )
    {
        parent::__construct($responseFactory, $authManager);
    }

    /**
     * @OA\Post(
     *     path="/v1/admin/plans/{estate}",
     *     tags={"Admin Plans"},
     *     summary="Create a new plan for an estate",
     *     description="Creates a new plan by uploading a PDF and converting it to an image.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="estate",
     *         in="path",
     *         description="Estate ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="file", type="string", format="binary", description="PDF file of the plan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plan created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Plan")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function store(CreatePlanRequest $request, Estate $estate): JsonResponse
    {
        return $this->responseFactory->json(
            $this->planService->createPlan(
                $request->validated(),
                $estate
            ),
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/v1/admin/plans/{estate}",
     *     tags={"Admin Plans"},
     *     summary="Get plans for an estate",
     *     description="Retrieves all plans associated with a specific estate.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="estate",
     *         in="path",
     *         description="Estate ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of plans",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Plan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plans not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plans not found")
     *         )
     *     )
     * )
     */
    public function index(Estate $estate): JsonResponse
    {
        return $this->responseFactory->json($this->planService->getPlansByEstate($estate));
    }

    /**
     * @OA\Delete(
     *     path="/v1/admin/plans/{estate}",
     *     tags={"Admin Plans"},
     *     summary="Delete plans for an estate",
     *     description="Deletes all plans associated with a specific estate.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="estate",
     *         in="path",
     *         description="Estate ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plans deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Action successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plans not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plans not found")
     *         )
     *     )
     * )
     */
    public function destroy(Estate $estate): JsonResponse
    {
        $this->planService->deletePlan($this->planService->getPlansByEstate($estate));

        return $this->responseFactory->json(['message' => __('app.action.success')]);
    }
}
