<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Opportunities;

use App\Http\Controllers\Controller;
use App\Http\Resources\OpportunityKanbanColumnResource;
use App\Services\OpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

final class KanbanController extends Controller
{
    public function __construct(
        private readonly OpportunityService $opportunityService
    ) {}

    #[OA\Get(
        path: '/api/admin/kanban/opportunities',
        summary: 'Get Kanban data',
        description: 'Get opportunities grouped by stage for Kanban view',
        tags: ['Kanban'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Kanban data grouped by stages',
                content: new OA\JsonContent(
                    properties: [
                        'data' => new OA\Property(
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/OpportunityKanbanColumnResource')
                        ),
                        'meta' => new OA\Property(
                            type: 'object',
                            properties: [
                                'total_columns' => new OA\Property(type: 'integer'),
                                'total_opportunities' => new OA\Property(type: 'integer'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function index(): JsonResponse
    {
        $kanbanData = $this->opportunityService->getKanbanData();

        return response()->json([
            'data' => OpportunityKanbanColumnResource::collection($kanbanData),
            'meta' => [
                'total_columns' => $kanbanData->count(),
                'total_opportunities' => $kanbanData->sum(fn ($column) => $column['opportunities']->count()),
            ],
        ]);
    }

    #[OA\Post(
        path: '/api/admin/kanban/opportunities/move-stage',
        summary: 'Move opportunity to different stage',
        description: 'Move an opportunity to a different stage (drag & drop functionality)',
        tags: ['Kanban'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['opportunity_id', 'new_stage_id'],
                properties: [
                    'opportunity_id' => new OA\Property(type: 'string', format: 'uuid', example: '123e4567-e89b-12d3-a456-426614174000'),
                    'new_stage_id' => new OA\Property(type: 'string', format: 'uuid', example: '123e4567-e89b-12d3-a456-426614174001'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Opportunity stage updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        'message' => new OA\Property(type: 'string', example: 'Opportunity stage updated successfully'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function moveStage(Request $request): JsonResponse
    {
        $request->validate([
            'opportunity_id' => 'required|string|uuid',
            'new_stage_id' => 'required|string|uuid',
        ]);

        $this->opportunityService->updateStage(
            opportunityId: $request->string('opportunity_id')->toString(),
            newStageId: $request->string('new_stage_id')->toString(),
            user: Auth::user()
        );

        return response()->json([
            'message' => 'Opportunity stage updated successfully',
        ]);
    }

    public function updateProbability(Request $request): JsonResponse
    {
        $request->validate([
            'opportunity_id' => 'required|string|uuid',
            'probability' => 'required|integer|min:0|max:100',
        ]);

        $this->opportunityService->updateProbability(
            opportunityId: $request->string('opportunity_id')->toString(),
            newProbability: $request->integer('probability'),
            user: Auth::user()
        );

        return response()->json([
            'message' => 'Opportunity probability updated successfully',
        ]);
    }
}
