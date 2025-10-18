<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AttachFileToTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:pptx,pdf,doc,docx,xls,xlsx,ppt',
                'max:10240', // 10MB max
            ],
        ];
    }
}
