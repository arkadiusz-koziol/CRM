<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Billing\Entity\Contract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ContractResource extends JsonResource
{
    public function __construct(Contract $contract)
    {
        parent::__construct($contract);
    }

    public function toArray(Request $request): array
    {
        /** @var Contract $contract */
        $contract = $this->resource;

        return [
            'type' => 'contracts',
            'id' => $contract->id(),
            'attributes' => [
                'number' => $contract->nr(),
                'company_id' => $contract->companyId(),
                'start_at' => $contract->startAt()->format('Y-m-d'),
                'end_at' => $contract->endAt()->format('Y-m-d'),
                'amount' => $contract->amount(),
                'currency' => $contract->currency(),
                'status' => $contract->status()->value,
                'created_at' => $contract->createdAt()->toISOString(),
                'updated_at' => $contract->updatedAt()->toISOString(),
            ],
        ];
    }
}
