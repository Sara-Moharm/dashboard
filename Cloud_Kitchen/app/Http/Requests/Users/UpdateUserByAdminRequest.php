<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserByAdminRequest extends FormRequest
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
        return [
            'fname' => 'sometimes|string|max:255',
            'lname' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|regex:/^\+?[0-9]{7,15}$/|max:15',
            'second_phone_number' => 'sometimes|string|regex:/^\+?[0-9]{7,15}$/|max:15',
            'address' => 'sometimes|string|max:255',
            'shift_start' => ['sometimes', 'date_format:H:i'],
            'shift_end' => ['sometimes', 'date_format:H:i', 'after:shift_start'],
        ];
    }
}
