<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Opportunities;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;
use App\Http\Requests\CreateOpportunityRequest;
use App\Http\Requests\UpdateOpportunityRequest;
use App\Http\Resources\OpportunityResource;
use App\Services\OpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class OpportunityController extends Controller
{
    public function __construct(
        private readonly OpportunityService $opportunityService
    ) {}

    #[OA\Get(
        path: '/api/admin/opportunities',
        summary: 'List opportunities',
        description: 'Get a list of opportunities with optional filtering',
        tags: ['Opportunities'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'owner',
                in: 'query',
                description: 'Filter by owner user ID',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'company',
                in: 'query',
                description: 'Filter by company ID',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
            new OA\Parameter(
                name: 'stage',
                in: 'query',
                description: 'Filter by stage ID',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
            new OA\Parameter(
                name: 'status',
                in: 'query',
                description: 'Filter by status',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['open', 'won', 'lost'])
            ),
            new OA\Parameter(
                name: 'search',
                in: 'query',
                description: 'Search in title and company name',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of opportunities',
                content: new OA\JsonContent(
                    properties: [
                        'data' => new OA\Property(
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/OpportunityResource')
                        ),
                        'meta' => new OA\Property(
                            type: 'object',
                            properties: [
                                'total' => new OA\Property(type: 'integer'),
                                'filters_applied' => new OA\Property(type: 'object')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'owner',
            'company',
            'stage',
            'status',
            'date_from',
            'date_to',
            'search'
        ]);

        $opportunities = $this->opportunityService->getFiltered($filters);

        return response()->json([
            'data' => OpportunityResource::collection($opportunities),
            'meta' => [
                'total' => $opportunities->count(),
                'filters_applied' => array_filter($filters)
            ]
        ]);
    }

    #[OA\Post(
        path: '/api/admin/opportunities',
        summary: 'Create opportunity',
        description: 'Create a new opportunity',
        tags: ['Opportunities'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'company_id', 'value', 'currency', 'probability', 'stage_id', 'owner_user_id'],
                properties: [
                    'title' => new OA\Property(type: 'string', example: 'Enterprise Software License'),
                    'company_id' => new OA\Property(type: 'string', format: 'uuid', example: '123e4567-e89b-12d3-a456-426614174000'),
                    'contact_id' => new OA\Property(type: 'string', format: 'uuid', example: '123e4567-e89b-12d3-a456-426614174001'),
                    'value' => new OA\Property(type: 'number', format: 'float', example: 50000.00),
                    'currency' => new OA\Property(type: 'string', example: 'USD'),
                    'probability' => new OA\Property(type: 'integer', minimum: 0, maximum: 100, example: 75),
                    'stage_id' => new OA\Property(type: 'string', format: 'uuid', example: '123e4567-e89b-12d3-a456-426614174002'),
                    'owner_user_id' => new OA\Property(type: 'integer', example: 1),
                    'close_date' => new OA\Property(type: 'string', format: 'date', example: '2024-12-31')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Opportunity created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/OpportunityResource')
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(CreateOpportunityRequest $request): JsonResponse
    {
        $opportunityId = $this->opportunityService->create(
            title: $request->string('title')->toString(),
            companyId: $request->string('company_id')->toString(),
            contactId: $request->string('contact_id')->toString() ?: null,
            value: $request->float('value'),
            currency: $request->string('currency')->toString(),
            probability: $request->integer('probability'),
            stageId: $request->string('stage_id')->toString(),
            ownerUserId: $request->string('owner_user_id')->toString(),
            closeDate: $request->string('close_date')->toString() ?: null
        );

        $opportunity = $this->opportunityService->findById($opportunityId);

        return (new OpportunityResource($opportunity))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $opportunity = $this->opportunityService->findById($id);

        if (!$opportunity) {
            return response()->json([
                'message' => 'Opportunity not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new OpportunityResource($opportunity)
        ]);
    }

    public function update(UpdateOpportunityRequest $request, string $id): JsonResponse
    {
        $this->opportunityService->update(
            id: $id,
            title: $request->string('title')->toString() ?: null,
            companyId: $request->string('company_id')->toString() ?: null,
            contactId: $request->string('contact_id')->toString() ?: null,
            value: $request->has('value') ? $request->float('value') : null,
            currency: $request->string('currency')->toString() ?: null,
            probability: $request->has('probability') ? $request->integer('probability') : null,
            stageId: $request->string('stage_id')->toString() ?: null,
            ownerUserId: $request->string('owner_user_id')->toString() ?: null,
            closeDate: $request->string('close_date')->toString() ?: null
        );

        $opportunity = $this->opportunityService->findById($id);

        return response()->json([
            'data' => new OpportunityResource($opportunity)
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->opportunityService->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
