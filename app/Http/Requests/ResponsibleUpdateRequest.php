<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResponsibleUpdateRequest extends FormRequest
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
            'user_id' => ['nullable', 'integer', 'gt:0', 'unique:responsibles,user_id'],
            'area_role_id' => ['required', 'integer', 'gt:0'],
            'academic_degree' => ['required', 'integer', 'gt:0'],
            'name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'identity_card' => ['required', 'string', 'unique:responsibles,identity_card'],
        ];
    }
}
