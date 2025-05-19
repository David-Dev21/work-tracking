<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
            'area_id' => ['required', 'integer', 'gt:0'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'state' => ['required', 'in:pendiente,en_progreso,finalizado'],
            'advance' => ['nullable', 'integer'],
            'start_date' => ['required'],
            'end_date' => ['nullable'],
        ];
    }
}
