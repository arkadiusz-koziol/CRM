<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:1000'],
            'status' => ['sometimes', 'string', Rule::enum(TaskStatus::class)],
            'priority' => ['sometimes', 'string', Rule::enum(TaskPriority::class)],
            'assigned_to' => ['sometimes', 'integer', 'exists:users,id'],
            'due_date' => ['sometimes', 'date'],
            'estimated_hours' => ['sometimes', 'numeric', 'min:0', 'max:1000'],
            'actual_hours' => ['sometimes', 'numeric', 'min:0', 'max:1000'],
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
            'title.max' => __('app.task.title_max'),
            'description.max' => __('app.task.description_max'),
            'status.in' => __('app.task.status_invalid'),
            'priority.in' => __('app.task.priority_invalid'),
            'assigned_to.exists' => __('app.task.assigned_to_exists'),
            'estimated_hours.numeric' => __('app.task.estimated_hours_numeric'),
            'estimated_hours.min' => __('app.task.estimated_hours_min'),
            'estimated_hours.max' => __('app.task.estimated_hours_max'),
            'actual_hours.numeric' => __('app.task.actual_hours_numeric'),
            'actual_hours.min' => __('app.task.actual_hours_min'),
            'actual_hours.max' => __('app.task.actual_hours_max'),
        ];
    }
}
