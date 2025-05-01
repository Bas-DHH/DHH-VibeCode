<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TemperatureVerificationTask;

class TemperatureTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = TemperatureVerificationTask::find($this->input('task_id'));
        return $task && $this->user()->can('complete', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $task = TemperatureVerificationTask::find($this->input('task_id'));

        $rules = [
            'task_id' => ['required', 'exists:tasks,id'],
            'temperature' => ['required', 'numeric', 'min:-50', 'max:200'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'corrective_action' => ['nullable', 'string', 'max:1000'],
        ];

        if ($task) {
            $rules['corrective_action'][] = 'required_if:temperature,<,' . $task->min_temperature;
            $rules['corrective_action'][] = 'required_if:temperature,>,' . $task->max_temperature;
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
            'temperature.required' => __('Please enter the temperature.'),
            'temperature.numeric' => __('Temperature must be a number.'),
            'temperature.min' => __('Temperature cannot be lower than -50°C.'),
            'temperature.max' => __('Temperature cannot exceed 200°C.'),
            'notes.max' => __('Notes cannot be longer than 1000 characters.'),
            'corrective_action.required_if' => __('Please provide a corrective action when temperature is out of range.'),
            'corrective_action.max' => __('Corrective action cannot be longer than 1000 characters.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert temperature to float
        if ($this->has('temperature')) {
            $this->merge([
                'temperature' => filter_var($this->temperature, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE)
            ]);
        }
    }
} 