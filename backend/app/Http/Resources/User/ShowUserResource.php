<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->getId(),
            'name'       => $this->getName(),
            'surname'    => $this->getSurname(),
            'email'      => $this->getEmail(),
            'phone'      => $this->getPhone(),
            'city'       => $this->getCity(),
            'vovoidship' => $this->getVovoidship(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
