<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'address' => 'sometimes|array',
            'address.city' => 'sometimes|string|max:255',
            'address.district' => 'sometimes|nullable|string|max:255',
            'address.street_address' => 'sometimes|string|max:255'
        ];
    }
}
