<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AttachMultipleFilesToTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'files' => [
                'required',
                'array',
                'min:1',
                'max:10', // Maximum 10 files at once
            ],
            'files.*' => [
                'required',
                'file',
                'mimes:pptx,pdf,doc,docx,xls,xlsx,ppt',
                'max:10240', // 10MB max per file
            ],
        ];
    }
}
