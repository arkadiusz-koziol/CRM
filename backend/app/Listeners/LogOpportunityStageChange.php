<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OpportunityStageChanged;
use App\Services\ActivityService;

final class LogOpportunityStageChange
{
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    public function handle(OpportunityStageChanged $event): void
    {
        $this->activityService->logActivity(
            action: 'opportunity_stage_changed',
            userName: $event->userName,
            userEmail: $event->userEmail,
            entityType: 'opportunity',
            entityId: $event->opportunityId
        );
    }
}
