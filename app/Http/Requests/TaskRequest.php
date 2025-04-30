<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'name_nl' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions_nl' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'scheduled_time' => 'required|date_format:H:i',
            'day_of_week' => 'required_if:frequency,weekly|integer|between:0,6',
            'day_of_month' => 'required_if:frequency,monthly|integer|between:1,31',
            'task_category_id' => 'required|exists:task_categories,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name_nl.required' => 'The Dutch name is required.',
            'name_en.required' => 'The English name is required.',
            'day_of_week.required_if' => 'The day of week is required for weekly tasks.',
            'day_of_month.required_if' => 'The day of month is required for monthly tasks.',
        ];
    }
} 