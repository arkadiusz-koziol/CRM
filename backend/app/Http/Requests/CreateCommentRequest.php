<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
            'commentable_type' => ['required', 'string', 'in:App\\Models\\Task,App\\Models\\Company,App\\Models\\Contact,App\\Models\\Opportunity,App\\Models\\Estate'],
            'commentable_id' => ['required', 'string'],
            'parent_id' => ['nullable', 'string', 'uuid'],
            'is_private' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Comment content is required.',
            'content.max' => 'Comment content cannot exceed 5000 characters.',
            'commentable_type.required' => 'Commentable type is required.',
            'commentable_type.in' => 'Invalid commentable type.',
            'commentable_id.required' => 'Commentable ID is required.',
            'parent_id.uuid' => 'Parent ID must be a valid UUID.',
        ];
    }
}
