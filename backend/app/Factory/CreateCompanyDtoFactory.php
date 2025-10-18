<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CreateCompanyDto;
use Illuminate\Http\Request;

class CreateCompanyDtoFactory
{
    public function fromRequest(Request $request): CreateCompanyDto
    {
        return new CreateCompanyDto(
            name: $request->string('name')->toString(),
            industry: $request->string('industry')->toString() ?: null,
            source: $request->string('source')->toString(),
            status: $request->string('status')->toString(),
            region: $request->string('region')->toString() ?: null,
            vatId: $request->string('vat_id')->toString() ?: null
        );
    }

    public function fromArray(array $data): CreateCompanyDto
    {
        return new CreateCompanyDto(
            name: $data['name'],
            industry: $data['industry'] ?? null,
            source: $data['source'],
            status: $data['status'] ?? 'prospect',
            region: $data['region'] ?? null,
            vatId: $data['vat_id'] ?? null
        );
    }
}
