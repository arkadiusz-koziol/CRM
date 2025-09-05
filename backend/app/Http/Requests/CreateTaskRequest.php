<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'status' => ['sometimes', 'string', Rule::enum(TaskStatus::class)],
            'priority' => ['sometimes', 'string', Rule::enum(TaskPriority::class)],
            'assigned_to' => ['required', 'integer', 'exists:users,id'],
            'due_date' => ['sometimes', 'date', 'after:now'],
            'estimated_hours' => ['sometimes', 'numeric', 'min:0', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => __('app.task.title_required'),
            'title.max' => __('app.task.title_max'),
            'description.required' => __('app.task.description_required'),
            'description.max' => __('app.task.description_max'),
            'status.in' => __('app.task.status_invalid'),
            'priority.in' => __('app.task.priority_invalid'),
            'assigned_to.required' => __('app.task.assigned_to_required'),
            'assigned_to.exists' => __('app.task.assigned_to_exists'),
            'due_date.after' => __('app.task.due_date_after'),
            'estimated_hours.numeric' => __('app.task.estimated_hours_numeric'),
            'estimated_hours.min' => __('app.task.estimated_hours_min'),
            'estimated_hours.max' => __('app.task.estimated_hours_max'),
        ];
    }
}
