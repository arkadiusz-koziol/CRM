<?php

namespace App\Http\Requests\Training;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['sometimes', 'exists:trainings_categories,id'],
            'assignment_type' => ['sometimes', 'in:all,role,manual'],
            'role_ids' => ['required_if:assignment_type,role', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'user_ids' => ['required_if:assignment_type,manual', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'mimes:pptx,pdf'],
        ];
    }
}
