<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Config\ExportConfig;
use App\Domain\Export\Entity\Export;
use App\Exceptions\Export\ExportGenerationException;
use App\Services\Export\Formatters\CsvFormatter;
use App\Services\Export\Formatters\PdfFormatter;
use App\Services\Export\Formatters\XlsxFormatter;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;

final class ExportService
{
    public function __construct(
        private CsvFormatter $csvFormatter,
        private XlsxFormatter $xlsxFormatter,
        private PdfFormatter $pdfFormatter,
        private LoggerInterface $logger,
        private Filesystem $storage,
        private ExportConfig $exportConfig,
        private Carbon $carbon,
    ) {}

    /**
     * Export data to CSV format.
     */
    public function exportToCsv(array $data, string $filename, array $headers = []): Response
    {
        $this->logger->info('Starting CSV export', [
            'filename' => $filename,
            'record_count' => count($data),
        ]);

        $content = $this->csvFormatter->format($data, $headers);
        $filePath = $this->storeFile($content, $filename, 'csv');

        return $this->createResponse($filePath, 'text/csv; charset=UTF-8', $filename.'.csv');
    }

    /**
     * Export data to XLSX format.
     */
    public function exportToXlsx(array $data, string $filename, array $headers = [], array $options = []): Response
    {
        $this->logger->info('Starting XLSX export', [
            'filename' => $filename,
            'record_count' => count($data),
        ]);

        $content = $this->xlsxFormatter->format($data, $headers, $options);
        $filePath = $this->storeFile($content, $filename, 'xlsx');

        return $this->createResponse($filePath, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $filename.'.xlsx');
    }

    /**
     * Export data to PDF format.
     */
    public function exportToPdf(array $data, string $filename, array $headers = [], array $options = []): Response
    {
        $this->logger->info('Starting PDF export', [
            'filename' => $filename,
            'record_count' => count($data),
        ]);

        $content = $this->pdfFormatter->format($data, $headers, $options);
        $filePath = $this->storeFile($content, $filename, 'pdf');

        return $this->createResponse($filePath, 'application/pdf', $filename.'.pdf');
    }

    /**
     * Export data with chunking for large datasets.
     */
    public function exportWithChunking(
        callable $dataProvider,
        string $filename,
        string $format,
        array $headers = [],
        array $options = []
    ): Response {
        $this->logger->info('Starting chunked export', [
            'filename' => $filename,
            'format' => $format,
        ]);

        $chunkSize = $this->exportConfig->chunkSize();
        $chunks = [];
        $offset = 0;

        do {
            $chunk = $dataProvider($offset, $chunkSize);
            if (empty($chunk)) {
                break;
            }

            $chunks[] = $chunk;
            $offset += $chunkSize;

            if (memory_get_usage() > $this->exportConfig->maxFileSize()) {
                $this->logger->warning('Memory limit reached during chunked export', [
                    'memory_usage' => memory_get_usage(),
                    'chunks_processed' => count($chunks),
                ]);

                break;
            }
        } while (count($chunk) === $chunkSize);

        $allData = array_merge(...$chunks);

        return match ($format) {
            'csv' => $this->exportToCsv($allData, $filename, $headers),
            'xlsx' => $this->exportToXlsx($allData, $filename, $headers, $options),
            'pdf' => $this->exportToPdf($allData, $filename, $headers, $options),
            default => throw new ExportGenerationException($format, 'Unsupported format'),
        };
    }

    /**
     * Store file content to storage.
     */
    private function storeFile(string $content, string $filename, string $extension): string
    {
        $path = $this->exportConfig->storagePath();
        $fullPath = "{$path}/{$filename}.{$extension}";

        $this->storage->put($fullPath, $content);

        $this->logger->info('File stored successfully', [
            'path' => $fullPath,
            'size' => strlen($content),
        ]);

        return $fullPath;
    }

    /**
     * Create HTTP response for file download.
     */
    private function createResponse(string $filePath, string $mimeType, string $filename): Response
    {
        $content = $this->storage->get($filePath);

        return response($content)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Content-Length', (string) strlen($content));
    }

    /**
     * Clean up old export files.
     */
    public function cleanupOldFiles(): int
    {
        $path = $this->exportConfig->storagePath();
        $retentionDays = $this->exportConfig->fileTtl();
        $cutoffDate = $this->carbon->copy()->subDays($retentionDays);

        $files = $this->storage->files($path);
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = $this->storage->lastModified($file);
            $fileDate = $this->carbon->copy()->createFromTimestamp($lastModified);

            if ($fileDate->isBefore($cutoffDate)) {
                $this->storage->delete($file);
                $deletedCount++;
            }
        }

        $this->logger->info('Cleaned up old export files', [
            'deleted_count' => $deletedCount,
            'retention_days' => $retentionDays,
        ]);

        return $deletedCount;
    }
}
