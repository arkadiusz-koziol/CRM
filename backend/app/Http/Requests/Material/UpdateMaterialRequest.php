<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:materials,name,' . $this->route('material')->id],
            'description' => ['required', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric'],
        ];
    }
}
