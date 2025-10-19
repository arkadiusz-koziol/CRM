<?php

declare(strict_types=1);

namespace App\Services\Export\Formatters;

class CsvFormatter
{
    public function format(array $data, array $headers = []): string
    {
        $output = fopen('php://temp', 'r+');

        // Write headers if provided
        if (! empty($headers)) {
            fputcsv($output, $headers, ',', '"', '\\');
        }

        // Write data rows
        foreach ($data as $row) {
            if (is_array($row)) {
                fputcsv($output, $row, ',', '"', '\\');
            }
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }
}
