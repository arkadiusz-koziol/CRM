<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\UpdateCompanyDto;
use Illuminate\Http\Request;

class UpdateCompanyDtoFactory
{
    public function fromRequest(Request $request): UpdateCompanyDto
    {
        return new UpdateCompanyDto(
            name: $request->has('name') ? $request->string('name')->toString() : null,
            industry: $request->has('industry') ? $request->string('industry')->toString() : null,
            source: $request->has('source') ? $request->string('source')->toString() : null,
            status: $request->has('status') ? $request->string('status')->toString() : null,
            region: $request->has('region') ? $request->string('region')->toString() : null,
            vatId: $request->has('vat_id') ? $request->string('vat_id')->toString() : null
        );
    }

    public function fromArray(array $data): UpdateCompanyDto
    {
        return new UpdateCompanyDto(
            name: $data['name'] ?? null,
            industry: $data['industry'] ?? null,
            source: $data['source'] ?? null,
            status: $data['status'] ?? null,
            region: $data['region'] ?? null,
            vatId: $data['vat_id'] ?? null
        );
    }
}
