<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Export;

use App\Exceptions\Export\ExportGenerationException;
use App\Factory\Export\CreateExportDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Export\ExportRequest;
use App\Services\Export\ExportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class ExportController extends Controller
{
    public function __construct(
        private ExportService $exportService,
        private CreateExportDtoFactory $dtoFactory,
        private Carbon $carbon,
    ) {}

    /**
     * Export data to CSV format.
     */
    public function exportCsv(ExportRequest $request): Response|JsonResponse
    {
        try {
            $dto = $this->dtoFactory->fromArray([
                'filename' => $request->validated()['filename'] ?? 'export_'.$this->carbon->format('Y-m-d_H-i-s'),
                'format' => 'csv',
                'data' => $request->validated()['data'] ?? [],
                'headers' => $request->validated()['headers'] ?? [],
                'options' => [],
            ]);

            return $this->exportService->exportToCsv($dto->data(), $dto->filename(), $dto->headers());
        } catch (ExportGenerationException $e) {
            return response()->json([
                'data' => [
                    'type' => 'export_error',
                    'attributes' => [
                        'error' => 'Export generation failed',
                        'message' => $e->getMessage(),
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], $e->getHttpStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'type' => 'export_error',
                    'attributes' => [
                        'error' => 'Unexpected error occurred',
                        'message' => 'An unexpected error occurred during export generation.',
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Export data to XLSX format.
     */
    public function exportXlsx(ExportRequest $request): Response|JsonResponse
    {
        try {
            $dto = $this->dtoFactory->fromArray([
                'filename' => $request->validated()['filename'] ?? 'export_'.$this->carbon->format('Y-m-d_H-i-s'),
                'format' => 'xlsx',
                'data' => $request->validated()['data'] ?? [],
                'headers' => $request->validated()['headers'] ?? [],
                'options' => $request->validated()['options'] ?? [],
            ]);

            return $this->exportService->exportToXlsx($dto->data(), $dto->filename(), $dto->headers(), $dto->options());
        } catch (ExportGenerationException $e) {
            return response()->json([
                'data' => [
                    'type' => 'export_error',
                    'attributes' => [
                        'error' => 'Export generation failed',
                        'message' => $e->getMessage(),
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], $e->getHttpStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'type' => 'export_error',
                    'attributes' => [
                        'error' => 'Unexpected error occurred',
                        'message' => 'An unexpected error occurred during export generation.',
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Export data to PDF format.
     */
    public function exportPdf(ExportRequest $request): Response|JsonResponse
    {
        try {
            $dto = $this->dtoFactory->fromArray([
                'filename' => $request->validated()['filename'] ?? 'export_'.$this->carbon->format('Y-m-d_H-i-s'),
                'format' => 'pdf',
                'data' => $request->validated()['data'] ?? [],
                'headers' => $request->validated()['headers'] ?? [],
                'options' => $request->validated()['options'] ?? [],
            ]);

            return $this->exportService->exportToPdf($dto->data(), $dto->filename(), $dto->headers(), $dto->options());
        } catch (ExportGenerationException $e) {
            return response()->json([
                'data' => [
                    'type' => 'export_error',
                    'attributes' => [
                        'error' => 'Export generation failed',
                        'message' => $e->getMessage(),
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], $e->getHttpStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'type' => 'export_error',
                    'attributes' => [
                        'error' => 'Unexpected error occurred',
                        'message' => 'An unexpected error occurred during export generation.',
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Export data with chunking for large datasets.
     */
    public function exportChunked(ExportRequest $request): Response|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data' => 'array',
            'data.*' => 'array',
            'filename' => 'required|string|max:255',
            'format' => 'required|string|in:csv,xlsx,pdf',
            'headers' => 'array',
            'options' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [
                    'type' => 'validation_errors',
                    'attributes' => [
                        'error' => 'Validation failed',
                        'messages' => $validator->errors(),
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $dto = $this->dtoFactory->fromArray([
            'filename' => $request->input('filename'),
            'format' => $request->input('format'),
            'data' => $request->input('data', []),
            'headers' => $request->input('headers', []),
            'options' => $request->input('options', []),
        ]);

        try {
            return match ($dto->format()) {
                'csv' => $this->exportService->exportToCsv($dto->data(), $dto->filename(), $dto->headers()),
                'xlsx' => $this->exportService->exportToXlsx($dto->data(), $dto->filename(), $dto->headers(), $dto->options()),
                'pdf' => $this->exportService->exportToPdf($dto->data(), $dto->filename(), $dto->headers(), $dto->options()),
                default => throw new \InvalidArgumentException("Unsupported format: {$dto->format()}"),
            };
        } catch (ExportGenerationException $e) {
            return response()->json([
                'data' => [
                    'type' => 'export_error',
                    'attributes' => [
                        'error' => 'Export generation failed',
                        'message' => $e->getMessage(),
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], $e->getHttpStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'type' => 'export_error',
                    'attributes' => [
                        'error' => 'Unexpected error occurred',
                        'message' => 'An unexpected error occurred during export generation.',
                    ],
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Clean up old export files.
     */
    public function cleanup(): JsonResponse
    {
        $deletedCount = $this->exportService->cleanupOldFiles();

        return response()->json([
            'data' => [
                'type' => 'cleanup',
                'attributes' => [
                    'message' => 'Cleanup completed',
                    'deleted_files' => $deletedCount,
                ],
            ],
            'meta' => [
                'request_id' => uniqid(),
            ],
        ], SymfonyResponse::HTTP_OK);
    }
}
