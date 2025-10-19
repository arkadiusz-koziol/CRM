<?php

declare(strict_types=1);

namespace App\Services\Export\Formatters;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class XlsxFormatter
{
    public function format(array $data, array $headers = [], array $options = []): string
    {
        $export = new class($data, $headers, $options) implements FromArray, WithHeadings, WithStyles, WithTitle
        {
            public function __construct(
                private array $data,
                private array $headers,
                private array $options
            ) {}

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headers;
            }

            public function title(): string
            {
                return $this->options['title'] ?? 'Export Data';
            }

            public function styles(Worksheet $sheet): array
            {
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                return [
                    // Header row styling
                    1 => [
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '366092'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ],
                    // Data rows styling
                    'A1:'.$lastColumn.$lastRow => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ],
                ];
            }
        };

        // Use output buffering to capture the Excel content
        ob_start();
        Excel::download($export, 'temp_export.xlsx');
        $content = ob_get_clean();

        return $content;
    }
}
