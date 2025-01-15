<?php

namespace App\Services;

use App\Interfaces\Repositories\PlanRepositoryInterface;
use App\Interfaces\Services\PlanServiceInterface;
use App\Models\Estate;
use App\Models\Plan;
use Spatie\PdfToImage\Pdf;

class PlanService implements PlanServiceInterface
{
    public function __construct(
        protected PlanRepositoryInterface $planRepository
    ) {}

    public function createPlan(array $data, Estate $estate): Plan
    {
        // Store the PDF
        $pdfPath = $data['file']->store('plans/pdf', 'public');

        // Create a path for the output image
        $imagePath = 'plans/images/' . pathinfo($data['file']->getClientOriginalName(), PATHINFO_FILENAME) . '.jpg';

        try {
            // Initialize the PDF-to-image converter
            $pdf = new Pdf(storage_path("app/public/$pdfPath"));

            // (Optional) You can set resolution, page, and other options if needed
            // $pdf->setResolution(300);
            // $pdf->setPage(1);

            // Convert & save the PDF as a single image
            $pdf->save(storage_path("app/public/$imagePath"));
        } catch (\Exception $e) {
            throw new \RuntimeException(__('Błąd podczas konwersji pliku PDF na obraz'), 0, $e);
        }

        return $this->planRepository->create([
            'estate_id' => $estate->id,
            'file_path' => $pdfPath,
            'image_path' => $imagePath,
        ]);
    }

    public function deletePlan(Plan $plan): bool
    {
        return $this->planRepository->delete($plan);
    }

    public function getPlansByEstate(Estate $estate): iterable
    {
        return $this->planRepository->getPlansByEstateId($estate->id);
    }
}
