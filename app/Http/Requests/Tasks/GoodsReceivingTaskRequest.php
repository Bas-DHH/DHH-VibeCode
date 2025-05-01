<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\GoodsReceivingTask;

class GoodsReceivingTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = GoodsReceivingTask::find($this->input('task_id'));
        return $task && $this->user()->can('complete', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $task = GoodsReceivingTask::find($this->input('task_id'));

        $rules = [
            'task_id' => ['required', 'exists:tasks,id'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'batch_number' => ['required', 'string', 'max:255'],
            'expiry_date' => ['required', 'date', 'after:today'],
            'packaging_intact' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'corrective_action' => ['nullable', 'string', 'max:1000'],
        ];

        if ($task?->temperature_check_required) {
            $rules['temperature'] = ['required', 'numeric', 'min:-50', 'max:200'];
            
            if ($task->min_temperature !== null) {
                $rules['corrective_action'][] = 'required_if:temperature,<,' . $task->min_temperature;
            }
            
            if ($task->max_temperature !== null) {
                $rules['corrective_action'][] = 'required_if:temperature,>,' . $task->max_temperature;
            }
        }

        if ($task?->visual_check_required) {
            $rules['visual_check_passed'] = ['required', 'boolean'];
            $rules['corrective_action'][] = 'required_if:visual_check_passed,false';
        }

        // Require corrective action if packaging is not intact
        $rules['corrective_action'][] = 'required_if:packaging_intact,false';

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
            'supplier_name.required' => __('Please enter the supplier name.'),
            'supplier_name.max' => __('Supplier name cannot be longer than 255 characters.'),
            'product_name.required' => __('Please enter the product name.'),
            'product_name.max' => __('Product name cannot be longer than 255 characters.'),
            'batch_number.required' => __('Please enter the batch number.'),
            'batch_number.max' => __('Batch number cannot be longer than 255 characters.'),
            'expiry_date.required' => __('Please enter the expiry date.'),
            'expiry_date.date' => __('Please enter a valid date.'),
            'expiry_date.after' => __('Expiry date must be in the future.'),
            'temperature.required' => __('Please enter the temperature.'),
            'temperature.numeric' => __('Temperature must be a number.'),
            'temperature.min' => __('Temperature cannot be lower than -50°C.'),
            'temperature.max' => __('Temperature cannot exceed 200°C.'),
            'packaging_intact.required' => __('Please indicate whether the packaging is intact.'),
            'packaging_intact.boolean' => __('The packaging intact field must be true or false.'),
            'visual_check_passed.required' => __('Please indicate whether the visual check passed.'),
            'visual_check_passed.boolean' => __('The visual check field must be true or false.'),
            'notes.max' => __('Notes cannot be longer than 1000 characters.'),
            'corrective_action.required_if' => __('Please provide a corrective action when requirements are not met.'),
            'corrective_action.max' => __('Corrective action cannot be longer than 1000 characters.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string boolean values to actual booleans
        if ($this->has('packaging_intact')) {
            $this->merge([
                'packaging_intact' => filter_var($this->packaging_intact, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

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
    }
} 