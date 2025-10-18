<?php

declare(strict_types=1);

namespace App\Infrastructure\Opportunity;

use App\Domain\Crm\Entity\Opportunity;
use App\Models\Opportunity as OpportunityModel;
use Carbon\Carbon;

final class OpportunityMapper
{
    public function toDomain(OpportunityModel $model): Opportunity
    {
        return new Opportunity(
            id: $model->id,
            title: $model->title,
            companyId: $model->company_id,
            contactId: $model->contact_id,
            value: (float) $model->value,
            currency: $model->currency,
            probability: $model->probability,
            stageId: $model->stage_id,
            ownerUserId: (string) $model->owner_user_id,
            closeDate: $model->close_date ? Carbon::parse($model->close_date) : null,
            status: $model->status,
            createdAt: Carbon::parse($model->created_at),
            updatedAt: Carbon::parse($model->updated_at),
            deletedAt: $model->deleted_at ? Carbon::parse($model->deleted_at) : null
        );
    }

    public function toModel(Opportunity $opportunity): OpportunityModel
    {
        $model = new OpportunityModel;

        if ($opportunity->id()) {
            $model->id = $opportunity->id();
        }

        $model->title = $opportunity->title();
        $model->company_id = $opportunity->companyId();
        $model->contact_id = $opportunity->contactId();
        $model->value = $opportunity->value();
        $model->currency = $opportunity->currency();
        $model->probability = $opportunity->probability();
        $model->stage_id = $opportunity->stageId();
        $model->owner_user_id = $opportunity->ownerUserId();
        $model->close_date = $opportunity->closeDate()?->format('Y-m-d');
        $model->status = $opportunity->status();

        if ($opportunity->createdAt()) {
            $model->created_at = $opportunity->createdAt();
        }

        if ($opportunity->updatedAt()) {
            $model->updated_at = $opportunity->updatedAt();
        }

        if ($opportunity->deletedAt()) {
            $model->deleted_at = $opportunity->deletedAt();
        }

        return $model;
    }
}
