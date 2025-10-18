<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Contacts;

use App\Factory\CreateContactDtoFactory;
use App\Factory\UpdateContactDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(
        private ContactService $contactService,
        private CreateContactDtoFactory $createDtoFactory,
        private UpdateContactDtoFactory $updateDtoFactory
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search',
            'status',
            'lead_level',
            'source',
            'owner_user_id',
            'sort_by',
            'sort_order'
        ]);

        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);

        $result = $this->contactService->search($filters, $perPage, $page);

        return (new ContactCollection($result['data']))
            ->additional(['meta' => ['pagination' => $result['pagination']]])
            ->response();
    }

    public function store(CreateContactRequest $request): JsonResponse
    {
        $dto = $this->createDtoFactory->fromRequest($request);

        $contact = $this->contactService->create([
            'first_name' => $dto->firstName(),
            'last_name' => $dto->lastName(),
            'email' => $dto->email(),
            'phone' => $dto->phone(),
            'lead_level' => $dto->leadLevel(),
            'owner_user_id' => $dto->ownerUserId(),
            'source' => $dto->source(),
            'status' => $dto->status(),
        ], (string) $request->user()->id);

        return (new ContactResource([
            'id' => $contact->id(),
            'first_name' => $contact->firstName(),
            'last_name' => $contact->lastName(),
            'email' => $contact->email(),
            'phone' => $contact->phone(),
            'lead_level' => $contact->leadLevel()->value,
            'owner_user_id' => $contact->ownerUserId(),
            'source' => $contact->source(),
            'status' => $contact->status()->value,
            'created_at' => $contact->createdAt(),
            'updated_at' => $contact->updatedAt(),
        ]))->response()->setStatusCode(201);
    }

    public function show(string $id): JsonResponse
    {
        $contact = $this->contactService->findById($id);

        if (! $contact) {
            return response()->json(['message' => 'Contact not found.'], 404);
        }

        return (new ContactResource([
            'id' => $contact->id(),
            'first_name' => $contact->firstName(),
            'last_name' => $contact->lastName(),
            'email' => $contact->email(),
            'phone' => $contact->phone(),
            'lead_level' => $contact->leadLevel()->value,
            'owner_user_id' => $contact->ownerUserId(),
            'source' => $contact->source(),
            'status' => $contact->status()->value,
            'created_at' => $contact->createdAt(),
            'updated_at' => $contact->updatedAt(),
        ]))->response();
    }

    public function update(UpdateContactRequest $request, string $id): JsonResponse
    {
        $dto = $this->updateDtoFactory->fromRequest($request);

        $data = array_filter([
            'first_name' => $dto->firstName(),
            'last_name' => $dto->lastName(),
            'email' => $dto->email(),
            'phone' => $dto->phone(),
            'lead_level' => $dto->leadLevel(),
            'owner_user_id' => $dto->ownerUserId(),
            'source' => $dto->source(),
            'status' => $dto->status(),
        ], fn($value) => $value !== null);

        $contact = $this->contactService->update($id, $data);

        return (new ContactResource([
            'id' => $contact->id(),
            'first_name' => $contact->firstName(),
            'last_name' => $contact->lastName(),
            'email' => $contact->email(),
            'phone' => $contact->phone(),
            'lead_level' => $contact->leadLevel()->value,
            'owner_user_id' => $contact->ownerUserId(),
            'source' => $contact->source(),
            'status' => $contact->status()->value,
            'created_at' => $contact->createdAt(),
            'updated_at' => $contact->updatedAt(),
        ]))->response();
    }

    public function destroy(string $id): JsonResponse
    {
        $this->contactService->delete($id);

        return response()->json(null, 204);
    }
}
