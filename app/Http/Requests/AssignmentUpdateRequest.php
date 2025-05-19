<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'intern_id' => ['required', 'integer', 'gt:0'],
            'activity_id' => ['nullable', 'integer', 'gt:0'],
            'project_id' => ['nullable', 'integer', 'gt:0'],
            'role' => ['required', 'string'],
            'assigned_date' => ['required', 'date'],
        ];
    }
}
