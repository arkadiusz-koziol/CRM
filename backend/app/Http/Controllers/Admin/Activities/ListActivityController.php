<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Activities;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListActivityController extends Controller
{
    public function __construct(
        private readonly ActivityService $activityService
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 10);
        $entityType = $request->get('entity_type');

        $activities = $entityType
            ? $this->activityService->getActivitiesByEntityType($entityType, $limit)
            : $this->activityService->getRecentActivities($limit);

        return response()->json([
            'data' => $activities->toArray(),
            'meta' => [
                'total' => $activities->count(),
                'limit' => $limit,
            ],
        ]);
    }
}
