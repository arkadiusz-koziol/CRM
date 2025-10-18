<?php

declare(strict_types=1);

namespace App\Infrastructure\Billing;

use App\Domain\Billing\Entity\Contract;
use App\Models\Contract as ContractModel;
use Carbon\Carbon;

final class ContractMapper
{
    public function toDomain(ContractModel $model): Contract
    {
        return new Contract(
            id: $model->id,
            nr: $model->nr,
            companyId: $model->company_id,
            startAt: Carbon::parse($model->start_at),
            endAt: Carbon::parse($model->end_at),
            amount: (float) $model->amount,
            currency: $model->currency,
            status: $model->status,
            createdAt: Carbon::parse($model->created_at),
            updatedAt: Carbon::parse($model->updated_at),
            deletedAt: $model->deleted_at ? Carbon::parse($model->deleted_at) : null
        );
    }

    public function toModel(Contract $contract): ContractModel
    {
        $model = new ContractModel;
        $model->id = $contract->id();
        $model->nr = $contract->nr();
        $model->company_id = $contract->companyId();
        $model->start_at = $contract->startAt()->format('Y-m-d');
        $model->end_at = $contract->endAt()->format('Y-m-d');
        $model->amount = $contract->amount();
        $model->currency = $contract->currency();
        $model->status = $contract->status()->value;
        $model->created_at = $contract->createdAt();
        $model->updated_at = $contract->updatedAt();
        $model->deleted_at = $contract->deletedAt();

        return $model;
    }
}
