<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OpportunityProbabilityChanged;
use App\Services\ActivityService;

final class LogOpportunityProbabilityChange
{
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    public function handle(OpportunityProbabilityChanged $event): void
    {
        $this->activityService->logActivity(
            action: 'opportunity_probability_changed',
            userName: $event->userName,
            userEmail: $event->userEmail,
            entityType: 'opportunity',
            entityId: $event->opportunityId
        );
    }
}
