<?php

namespace App\Http\Requests\Training;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:trainings_categories,id'],
            'assignment_type' => ['required', 'in:all,role,users'],
            'role_ids' => ['required_if:assignment_type,role', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'user_ids' => ['required_if:assignment_type,users', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'mimes:pptx,pdf'],
        ];
    }
}
