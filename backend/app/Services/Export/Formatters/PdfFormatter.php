<?php

declare(strict_types=1);

namespace App\Services\Export\Formatters;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class PdfFormatter
{
    public function format(array $data, array $headers = [], array $options = []): string
    {
        $branding = config('export.branding', []);
        $pdfOptions = config('export.formats.pdf', []);

        $html = View::make('exports.pdf-template', [
            'data' => $data,
            'headers' => $headers,
            'branding' => $branding,
            'options' => $options,
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($pdfOptions['page_size'] ?? 'A4', $pdfOptions['orientation'] ?? 'portrait');

        if (isset($pdfOptions['margin'])) {
            $pdf->setOptions([
                'margin_top' => $pdfOptions['margin']['top'] ?? 20,
                'margin_right' => $pdfOptions['margin']['right'] ?? 20,
                'margin_bottom' => $pdfOptions['margin']['bottom'] ?? 20,
                'margin_left' => $pdfOptions['margin']['left'] ?? 20,
            ]);
        }

        return $pdf->output();
    }
}
