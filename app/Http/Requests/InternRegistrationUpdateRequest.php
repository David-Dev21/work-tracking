<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InternRegistrationUpdateRequest extends FormRequest
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
            'intern_type_id' => ['required', 'integer', 'gt:0'],
            'intern_id' => ['required', 'integer', 'gt:0'],
            'area_id' => ['required', 'integer', 'gt:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ];
    }
}
