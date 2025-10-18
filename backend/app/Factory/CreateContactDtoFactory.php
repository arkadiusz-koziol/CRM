<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CreateContactDto;
use App\Http\Requests\CreateContactRequest;

final class CreateContactDtoFactory
{
    public function fromRequest(CreateContactRequest $request): CreateContactDto
    {
        return new CreateContactDto(
            firstName: $request->string('first_name')->toString(),
            lastName: $request->string('last_name')->toString(),
            email: $request->string('email')->toString(),
            phone: $request->string('phone')->toString() ?: null,
            leadLevel: $request->string('lead_level')->toString(),
            ownerUserId: $request->string('owner_user_id')->toString() ?: null,
            source: $request->string('source')->toString(),
            status: $request->string('status')->toString()
        );
    }
}
