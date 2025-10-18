<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Companies;

use App\Http\Resources\CompanyCollection;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MyCompaniesController
{
    public function __construct(
        private CompanyService $companyService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'name', 'vat_id', 'region', 'status', 'source', 'industry',
            'sort_by', 'sort_direction',
        ]);

        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);

        $result = $this->companyService->findByUser(auth()->id(), $filters, $perPage, $page);

        return response()->json(
            (new CompanyCollection($result['data']))->toArray($request),
            Response::HTTP_OK
        );
    }
}
