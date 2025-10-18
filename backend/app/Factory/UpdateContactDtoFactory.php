<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\UpdateContactDto;
use App\Http\Requests\UpdateContactRequest;

final class UpdateContactDtoFactory
{
    public function fromRequest(UpdateContactRequest $request): UpdateContactDto
    {
        return new UpdateContactDto(
            firstName: $request->has('first_name') ? $request->string('first_name')->toString() : null,
            lastName: $request->has('last_name') ? $request->string('last_name')->toString() : null,
            email: $request->has('email') ? $request->string('email')->toString() : null,
            phone: $request->has('phone') ? $request->string('phone')->toString() : null,
            leadLevel: $request->has('lead_level') ? $request->string('lead_level')->toString() : null,
            ownerUserId: $request->has('owner_user_id') ? $request->string('owner_user_id')->toString() : null,
            source: $request->has('source') ? $request->string('source')->toString() : null,
            status: $request->has('status') ? $request->string('status')->toString() : null
        );
    }
}
