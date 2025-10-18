<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Contacts;

use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactCompanyController extends Controller
{
    public function __construct(
        private ContactService $contactService
    ) {}

    public function link(Request $request, string $contactId): JsonResponse
    {
        $request->validate([
            'company_id' => ['required', 'string', 'exists:companies,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['boolean'],
        ]);

        $this->contactService->linkToCompany(
            $contactId,
            $request->string('company_id')->toString(),
            $request->string('position')->toString() ?: null,
            $request->boolean('is_primary', false)
        );

        return response()->json(['message' => 'Contact linked to company successfully.']);
    }

    public function unlink(Request $request, string $contactId): JsonResponse
    {
        $request->validate([
            'company_id' => ['required', 'string', 'exists:companies,id'],
        ]);

        $this->contactService->unlinkFromCompany(
            $contactId,
            $request->string('company_id')->toString()
        );

        return response()->json(['message' => 'Contact unlinked from company successfully.']);
    }

    public function getCompanies(string $contactId): JsonResponse
    {
        $companies = $this->contactService->getContactCompanies($contactId);

        return response()->json([
            'data' => $companies,
            'meta' => [
                'request_id' => app('requestId'),
            ],
        ]);
    }

    public function bulkLink(Request $request): JsonResponse
    {
        $request->validate([
            'contact_ids' => ['required', 'array', 'min:1'],
            'contact_ids.*' => ['string', 'exists:contacts,id'],
            'company_id' => ['required', 'string', 'exists:companies,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['boolean'],
        ]);

        $this->contactService->bulkLinkToCompany(
            $request->array('contact_ids'),
            $request->string('company_id')->toString(),
            $request->string('position')->toString() ?: null,
            $request->boolean('is_primary', false)
        );

        return response()->json(['message' => 'Contacts linked to company successfully.']);
    }

    public function bulkUnlink(Request $request): JsonResponse
    {
        $request->validate([
            'contact_ids' => ['required', 'array', 'min:1'],
            'contact_ids.*' => ['string', 'exists:contacts,id'],
            'company_id' => ['required', 'string', 'exists:companies,id'],
        ]);

        $this->contactService->bulkUnlinkFromCompany(
            $request->array('contact_ids'),
            $request->string('company_id')->toString()
        );

        return response()->json(['message' => 'Contacts unlinked from company successfully.']);
    }
}
