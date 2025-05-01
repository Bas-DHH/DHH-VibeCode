<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CleaningVerificationTask;

class CleaningTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = CleaningVerificationTask::find($this->input('task_id'));
        return $task && $this->user()->can('complete', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'task_id' => ['required', 'exists:tasks,id'],
            'cleaned' => ['required', 'boolean'],
            'disinfected' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'corrective_action' => [
                'required_if:cleaned,false',
                'required_if:disinfected,false',
                'nullable',
                'string',
                'max:1000'
            ],
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
            'task_id.required' => __('The task ID is required.'),
            'task_id.exists' => __('The selected task is invalid.'),
            'cleaned.required' => __('Please indicate whether the task was cleaned.'),
            'cleaned.boolean' => __('The cleaned field must be true or false.'),
            'disinfected.boolean' => __('The disinfected field must be true or false.'),
            'notes.max' => __('Notes cannot be longer than 1000 characters.'),
            'corrective_action.required_if' => __('Please provide a corrective action when task is not completed.'),
            'corrective_action.max' => __('Corrective action cannot be longer than 1000 characters.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string boolean values to actual booleans
        if ($this->has('cleaned')) {
            $this->merge([
                'cleaned' => filter_var($this->cleaned, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

        if ($this->has('disinfected')) {
            $this->merge([
                'disinfected' => filter_var($this->disinfected, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }
    }
} 