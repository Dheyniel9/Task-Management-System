<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled in the controller via policies
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'in:pending,completed'],
            'priority' => ['sometimes', 'in:low,medium,high'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitize input to prevent XSS
        if ($this->has('title')) {
            $this->merge(['title' => strip_tags($this->title)]);
        }
        if ($this->has('description')) {
            $this->merge(['description' => strip_tags($this->description)]);
        }
    }
}
