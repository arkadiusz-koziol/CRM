<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Reports;

use App\Factory\CreateReportDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReportRequest;
use App\Http\Resources\ReportResource;
use App\Services\Reports\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private CreateReportDtoFactory $dtoFactory
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = (int) $request->get('per_page', 15);

        if ($request->boolean('public')) {
            $reports = $this->reportService->findPublic($perPage);
        } else {
            $reports = $this->reportService->findByUser((string) $user->id, $perPage);
        }

        return response()->json($reports);
    }

    public function store(CreateReportRequest $request): JsonResponse
    {
        $user = Auth::user();
        $dto = $this->dtoFactory->fromRequest($request);

        $reportId = $this->reportService->create(
            $dto->name(),
            $dto->description(),
            $dto->source(),
            $dto->columns(),
            $dto->filters(),
            $dto->sorting(),
            (string) $user->id,
            $dto->isPublic(),
        );

        $report = $this->reportService->findById($reportId);

        return (new ReportResource($report))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $report = $this->reportService->findById($id);

        if (! $report) {
            abort(404, 'Report not found.');
        }

        return response()->json(new ReportResource($report));
    }

    public function update(CreateReportRequest $request, string $id): JsonResponse
    {
        $dto = $this->dtoFactory->fromRequest($request);

        try {
            $this->reportService->update(
                $id,
                $dto->name(),
                $dto->description(),
                $dto->columns(),
                $dto->filters(),
                $dto->sorting(),
                $dto->isPublic(),
            );

            $report = $this->reportService->findById($id);

            return response()->json(new ReportResource($report));
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                abort(404, 'Report not found');
            }

            throw $e;
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->reportService->delete($id);

            return response()->json(['message' => 'Report deleted successfully.']);
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                abort(404, 'Report not found');
            }

            throw $e;
        }
    }
}
