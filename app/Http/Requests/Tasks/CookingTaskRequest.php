<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CookingVerificationTask;

class CookingTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = CookingVerificationTask::find($this->input('task_id'));
        return $task && $this->user()->can('complete', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $task = CookingVerificationTask::find($this->input('task_id'));

        $rules = [
            'task_id' => ['required', 'exists:tasks,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'temperature' => ['required', 'numeric', 'min:0', 'max:200'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'corrective_action' => [
                'required_if:temperature,<,' . ($task?->temperature_norm ?? 0),
                'nullable',
                'string',
                'max:1000'
            ],
        ];

        if ($task?->cooking_time_required) {
            $rules['cooking_time'] = ['required', 'numeric', 'min:1', 'max:1440']; // max 24 hours
        }

        if ($task?->visual_checks_required) {
            $rules['visual_check_passed'] = ['required', 'boolean'];
            $rules['corrective_action'][] = 'required_if:visual_check_passed,false';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'task_id.required' => __('The task ID is required.'),
            'task_id.exists' => __('The selected task is invalid.'),
            'product_name.required' => __('Please enter the product name.'),
            'product_name.max' => __('Product name cannot be longer than 255 characters.'),
            'temperature.required' => __('Please enter the temperature.'),
            'temperature.numeric' => __('Temperature must be a number.'),
            'temperature.min' => __('Temperature cannot be negative.'),
            'temperature.max' => __('Temperature cannot exceed 200°C.'),
            'cooking_time.required' => __('Please enter the cooking time.'),
            'cooking_time.numeric' => __('Cooking time must be a number.'),
            'cooking_time.min' => __('Cooking time must be at least 1 minute.'),
            'cooking_time.max' => __('Cooking time cannot exceed 24 hours.'),
            'visual_check_passed.required' => __('Please indicate whether the visual check passed.'),
            'visual_check_passed.boolean' => __('Visual check must be passed or failed.'),
            'notes.max' => __('Notes cannot be longer than 1000 characters.'),
            'corrective_action.required_if' => __('Please provide a corrective action when task requirements are not met.'),
            'corrective_action.max' => __('Corrective action cannot be longer than 1000 characters.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string boolean values to actual booleans
        if ($this->has('visual_check_passed')) {
            $this->merge([
                'visual_check_passed' => filter_var($this->visual_check_passed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

        // Convert temperature to float
        if ($this->has('temperature')) {
            $this->merge([
                'temperature' => filter_var($this->temperature, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE)
            ]);
        }

        // Convert cooking time to integer
        if ($this->has('cooking_time')) {
            $this->merge([
                'cooking_time' => filter_var($this->cooking_time, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)
            ]);
        }
    }
} 