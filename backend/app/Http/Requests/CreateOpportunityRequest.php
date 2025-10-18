<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateOpportunityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'company_id' => 'required|string|uuid|exists:companies,id',
            'contact_id' => 'nullable|string|uuid|exists:contacts,id',
            'value' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'probability' => 'required|integer|min:0|max:100',
            'stage_id' => 'required|string|uuid|exists:stages,id',
            'owner_user_id' => 'required|integer|exists:users,id',
            'close_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The opportunity title is required.',
            'title.max' => 'The opportunity title may not be greater than 255 characters.',
            'company_id.required' => 'The company is required.',
            'company_id.uuid' => 'The company ID must be a valid UUID.',
            'company_id.exists' => 'The selected company does not exist.',
            'contact_id.uuid' => 'The contact ID must be a valid UUID.',
            'contact_id.exists' => 'The selected contact does not exist.',
            'value.required' => 'The opportunity value is required.',
            'value.numeric' => 'The opportunity value must be a number.',
            'value.min' => 'The opportunity value must be at least 0.',
            'currency.required' => 'The currency is required.',
            'currency.size' => 'The currency must be exactly 3 characters.',
            'probability.required' => 'The probability is required.',
            'probability.integer' => 'The probability must be an integer.',
            'probability.min' => 'The probability must be at least 0.',
            'probability.max' => 'The probability must not be greater than 100.',
            'stage_id.required' => 'The stage is required.',
            'stage_id.uuid' => 'The stage ID must be a valid UUID.',
            'stage_id.exists' => 'The selected stage does not exist.',
            'owner_user_id.required' => 'The owner is required.',
            'owner_user_id.integer' => 'The owner ID must be an integer.',
            'owner_user_id.exists' => 'The selected owner does not exist.',
            'close_date.date' => 'The close date must be a valid date.',
            'close_date.after_or_equal' => 'The close date must be today or in the future.',
        ];
    }
}
