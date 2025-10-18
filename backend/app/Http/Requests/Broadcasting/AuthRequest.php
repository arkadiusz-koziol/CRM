<?php

declare(strict_types=1);

namespace App\Http\Requests\Broadcasting;

use Illuminate\Foundation\Http\FormRequest;

final class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'socket_id' => ['required', 'string', 'max:255'],
            'channel_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9._-]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'socket_id.required' => 'Socket ID is required',
            'socket_id.string' => 'Socket ID must be a string',
            'socket_id.max' => 'Socket ID must not exceed 255 characters',
            'channel_name.required' => 'Channel name is required',
            'channel_name.string' => 'Channel name must be a string',
            'channel_name.max' => 'Channel name must not exceed 255 characters',
            'channel_name.regex' => 'Channel name contains invalid characters',
        ];
    }
}
