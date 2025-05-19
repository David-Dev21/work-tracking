<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InternUpdateRequest extends FormRequest
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
            'user_id' => ['nullable', 'integer', 'gt:0', 'unique:interns,user_id'],
            'name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'identity_card' => ['required', 'string', 'unique:interns,identity_card'],
            'university_registration' => ['required', 'string', 'unique:interns,university_registration'],
        ];
    }
}
