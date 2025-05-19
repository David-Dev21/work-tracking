<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityStoreRequest extends FormRequest
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
            'location_id' => ['nullable', 'integer', 'gt:0'],
            'area_id' => ['required', 'integer', 'gt:0'],
            'project_id' => ['nullable', 'integer', 'gt:0'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'state' => ['required', 'in:pendiente,en_progreso,finalizado'],
            'priority' => ['required', 'in:baja,media,alta'],
            'start_date' => ['required'],
            'end_date' => ['nullable'],
        ];
    }
}
