<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:temperature,goods_receiving,cooking,verification,cleaning'],
            'frequency' => ['required', 'string', 'in:daily,weekly,monthly'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A task title is required',
            'title.max' => 'The task title cannot exceed 255 characters',
            'category.required' => 'A task category is required',
            'category.in' => 'The selected category is invalid',
            'frequency.required' => 'A task frequency is required',
            'frequency.in' => 'The selected frequency is invalid',
            'due_date.date' => 'The due date must be a valid date',
        ];
    }
} 