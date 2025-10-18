<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class OpportunityStageChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly string $opportunityId,
        public readonly string $oldStageId,
        public readonly string $newStageId,
        public readonly string $userId,
        public readonly string $userName,
        public readonly string $userEmail
    ) {}
}
