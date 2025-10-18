<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ContactCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($contact) {
                return [
                    'type' => 'contacts',
                    'id' => $contact->id(),
                    'attributes' => [
                        'first_name' => $contact->firstName(),
                        'last_name' => $contact->lastName(),
                        'full_name' => $contact->firstName() . ' ' . $contact->lastName(),
                        'email' => $contact->email(),
                        'phone' => $contact->phone(),
                        'lead_level' => $contact->leadLevel()->value,
                        'owner_user_id' => $contact->ownerUserId(),
                        'source' => $contact->source(),
                        'status' => $contact->status()->value,
                        'created_at' => $contact->createdAt()->toISOString(),
                        'updated_at' => $contact->updatedAt()->toISOString(),
                    ],
                ];
            }),
            'meta' => [
                'request_id' => uniqid(),
            ],
        ];
    }
}
