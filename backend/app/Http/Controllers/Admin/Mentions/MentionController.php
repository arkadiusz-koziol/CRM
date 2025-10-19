<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Mentions;

use App\Http\Controllers\Controller;
use App\Http\Resources\MentionResource;
use App\Services\Collab\MentionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Ramsey\Uuid\Uuid;

final class MentionController extends Controller
{
    public function __construct(
        private readonly MentionService $mentionService,
    ) {}

    /**
     * Get mentions for the authenticated user.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) $request->get('limit', 50), 100);

        $mentions = $this->mentionService->getMentionsForUser(
            (int) $request->user()->id,
            $limit
        );

        return MentionResource::collection($mentions);
    }

    /**
     * Mark a mention as read.
     */
    public function markAsRead(Request $request, string $mentionId): JsonResponse
    {
        try {
            $this->mentionService->markAsRead(
                Uuid::fromString($mentionId),
                (int) $request->user()->id
            );

            return response()->json([
                'message' => 'Mention marked as read',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to mark mention as read', [
                'mention_id' => $mentionId,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to mark mention as read',
            ], 500);
        }
    }

    /**
     * Get mention statistics for the authenticated user.
     */
    public function stats(Request $request): JsonResponse
    {
        $stats = $this->mentionService->getMentionStats(
            (int) $request->user()->id
        );

        return response()->json([
            'data' => $stats,
        ]);
    }
}
