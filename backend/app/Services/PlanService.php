<?php

namespace App\Services;

use App\Interfaces\Repositories\PlanRepositoryInterface;
use App\Models\Estate;
use App\Models\Plan;
use Exception;
use Illuminate\Support\Collection;
use RuntimeException;
use Spatie\PdfToImage\Pdf;

class PlanService
{
    public function __construct(
        protected PlanRepositoryInterface $planRepository
    ) {}

    public function createPlan(array $data, Estate $estate): Plan
    {
        $pdfPath = $data['file']->store('plans/pdf', 'public');

        $imagePath = 'plans/images/' . pathinfo($data['file']->getClientOriginalName(), PATHINFO_FILENAME) . '.jpg';

        $imageDirectory = storage_path('app/public/plans/images');
        if (!is_dir($imageDirectory)) {
            mkdir($imageDirectory, 0755, true);
        }

        try {
            $pdf = new Pdf(storage_path("app/public/$pdfPath"));
            $pdf->save(storage_path("app/public/$imagePath"));
        } catch (Exception $e) {
            throw new RuntimeException(__('Błąd podczas konwersji pliku PDF na obraz'), 500, $e);
        }

        return $this->planRepository->create([
            'estate_id' => $estate->id,
            'file_path' => $pdfPath,
            'image_path' => $imagePath,
        ]);
    }

    public function deletePlan(Collection $plans): void
    {
        foreach ($plans as $plan) {
            $this->planRepository->delete($plan);
        }
    }

    public function getPlansByEstate(Estate $estate): Collection
    {
        return $this->planRepository->getPlansByEstateId($estate->id);
    }
}
