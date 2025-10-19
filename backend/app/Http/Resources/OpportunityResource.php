<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Crm\Entity\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OpportunityResource extends JsonResource
{
    public function __construct(private readonly Opportunity $opportunity)
    {
        parent::__construct($opportunity);
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'type' => 'opportunities',
                'id' => $this->opportunity->id(),
                'attributes' => [
                    'title' => $this->opportunity->title(),
                    'company_id' => $this->opportunity->companyId(),
                    'contact_id' => $this->opportunity->contactId(),
                    'value' => $this->opportunity->value(),
                    'currency' => $this->opportunity->currency(),
                    'probability' => $this->opportunity->probability(),
                    'stage_id' => $this->opportunity->stageId(),
                    'owner_user_id' => $this->opportunity->ownerUserId(),
                    'close_date' => $this->opportunity->closeDate()?->format('Y-m-d'),
                    'status' => $this->opportunity->status()->value,
                    'created_at' => $this->opportunity->createdAt()->toISOString(),
                    'updated_at' => $this->opportunity->updatedAt()->toISOString(),
                ],
            ],
            'meta' => [
                'request_id' => app('requestId'),
            ],
        ];
    }
}
