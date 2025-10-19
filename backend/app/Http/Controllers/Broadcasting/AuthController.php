<?php

declare(strict_types=1);

namespace App\Http\Controllers\Broadcasting;

use App\Http\Requests\Broadcasting\AuthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Broadcast;

final class AuthController
{
    /**
     * @OA\Post(
     *     path="/broadcasting/auth",
     *     summary="Authenticate broadcasting channel",
     *     description="Authenticate user for private broadcasting channels",
     *     tags={"Broadcasting"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"socket_id", "channel_name"},
     *
     *             @OA\Property(property="socket_id", type="string", description="Socket ID for authentication"),
     *             @OA\Property(property="channel_name", type="string", description="Channel name to authenticate")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="auth", type="string", description="Authentication signature")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - insufficient permissions",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function authenticate(AuthRequest $request): JsonResponse
    {
        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');

        // Validate channel access
        if (! $this->canAccessChannel($request->user(), $channelName)) {
            return response()->json([
                'message' => 'Insufficient permissions for this channel',
                'errors' => ['channel_name' => ['Access denied']],
            ], 403);
        }

        // Generate authentication signature
        $auth = Broadcast::auth($request);

        return response()->json([
            'auth' => $auth,
        ]);
    }

    private function canAccessChannel($user, string $channelName): bool
    {
        // Parse channel name to determine access
        if (str_starts_with($channelName, 'user.')) {
            $userId = substr($channelName, 5);

            return (string) $user->id === $userId;
        }

        if (str_starts_with($channelName, 'entity.')) {
            $parts = explode('.', $channelName);
            if (count($parts) >= 3) {
                $entityType = $parts[1];
                $entityId = $parts[2];

                return $this->hasEntityAccess($user, $entityType, $entityId);
            }
        }

        if (str_starts_with($channelName, 'team.')) {
            $teamId = substr($channelName, 5);

            return $user->teams()->where('team_id', $teamId)->exists();
        }

        if ($channelName === 'admin') {
            return $user->hasRole('admin');
        }

        return false;
    }

    private function hasEntityAccess($user, string $entityType, string $entityId): bool
    {
        return match ($entityType) {
            'company' => $user->can('company.view') && $this->hasAccessToCompany($user, $entityId),
            'contact' => $user->can('contact.view') && $this->hasAccessToContact($user, $entityId),
            'opportunity' => $user->can('opportunity.view') && $this->hasAccessToOpportunity($user, $entityId),
            'task' => $user->can('task.view') && $this->hasAccessToTask($user, $entityId),
            default => false,
        };
    }

    private function hasAccessToCompany($user, string $companyId): bool
    {
        // For now, allow access if user has company.view permission
        // In a real implementation, you would check actual company relationships
        return $user->can('company.view') || $user->hasRole('admin');
    }

    private function hasAccessToContact($user, string $contactId): bool
    {
        // For now, allow access if user has contact.view permission
        // In a real implementation, you would check actual contact relationships
        return $user->can('contact.view') || $user->hasRole('admin');
    }

    private function hasAccessToOpportunity($user, string $opportunityId): bool
    {
        // For now, allow access if user has opportunity.view permission
        // In a real implementation, you would check actual opportunity relationships
        return $user->can('opportunity.view') || $user->hasRole('admin');
    }

    private function hasAccessToTask($user, string $taskId): bool
    {
        // For now, allow access if user has task.view permission
        // In a real implementation, you would check actual task relationships
        return $user->can('task.view') || $user->hasRole('admin');
    }
}
