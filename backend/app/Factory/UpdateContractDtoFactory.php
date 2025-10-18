<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\UpdateContractDto;
use Carbon\Carbon;

final class UpdateContractDtoFactory
{
    public function fromArray(array $data): UpdateContractDto
    {
        return new UpdateContractDto(
            id: $data['id'],
            number: $data['number'] ?? null,
            companyId: $data['company_id'] ?? null,
            startAt: isset($data['start_at']) ? Carbon::parse($data['start_at']) : null,
            endAt: isset($data['end_at']) ? Carbon::parse($data['end_at']) : null,
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            currency: $data['currency'] ?? null,
            status: $data['status'] ?? null
        );
    }
}
