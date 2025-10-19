<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CreateReportDto;
use App\Http\Requests\CreateReportRequest;

final class CreateReportDtoFactory
{
    public function fromRequest(CreateReportRequest $request): CreateReportDto
    {
        return new CreateReportDto(
            name: $request->string('name')->toString(),
            description: $request->string('description')->toString() ?: null,
            source: $request->string('source')->toString(),
            columns: $request->array('columns'),
            filters: $request->array('filters'),
            sorting: $request->array('sorting'),
            isPublic: $request->boolean('is_public', false),
        );
    }
}
