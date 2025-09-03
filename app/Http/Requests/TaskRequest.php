<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
        ];

        // Only require title for create operations or when title is being updated
        if ($this->isMethod('POST') || $this->has('title')) {
            $rules['title'] = 'required|string|max:255';
        } else {
            $rules['title'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'title.max' => 'Task title cannot exceed 255 characters',
            'description.max' => 'Task description cannot exceed 1000 characters',
            'status.in' => 'Task status must be one of: pending, in_progress, completed',
            'priority.in' => 'Task priority must be one of: low, medium, high',
            'due_date.date' => 'Please provide a valid due date',
            'due_date.after_or_equal' => 'Due date cannot be in the past',
        ];
    }
}
