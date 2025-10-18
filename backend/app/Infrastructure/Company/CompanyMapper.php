<?php

declare(strict_types=1);

namespace App\Infrastructure\Company;

use App\Domain\Crm\Entity\Company as CompanyEntity;
use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use App\Models\Company as CompanyModel;
use Carbon\Carbon;

final class CompanyMapper
{
    public function toDomain(CompanyModel $model): CompanyEntity
    {
        return new CompanyEntity(
            id: $model->id,
            name: $model->name,
            industry: $model->industry,
            source: $model->source instanceof CompanySource ? $model->source : CompanySource::from($model->source),
            status: $model->status instanceof CompanyStatus ? $model->status : CompanyStatus::from($model->status),
            region: $model->region,
            vatId: $model->vat_id,
            createdBy: (string) $model->created_by,
            createdAt: Carbon::parse($model->created_at),
            updatedAt: Carbon::parse($model->updated_at),
            deletedAt: $model->deleted_at ? Carbon::parse($model->deleted_at) : null
        );
    }

    public function toModel(CompanyEntity $entity): CompanyModel
    {
        $model = new CompanyModel;
        $model->id = $entity->id();
        $model->name = $entity->name();
        $model->industry = $entity->industry();
        $model->source = $entity->source()->value;
        $model->status = $entity->status()->value;
        $model->region = $entity->region();
        $model->vat_id = $entity->vatId();
        $model->created_by = $entity->createdBy();
        $model->created_at = $entity->createdAt();
        $model->updated_at = $entity->updatedAt();
        $model->deleted_at = $entity->deletedAt();

        return $model;
    }
}
