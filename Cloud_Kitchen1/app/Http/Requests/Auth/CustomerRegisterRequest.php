<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegisterRequest extends FormRequest
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
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'required|string|regex:/^\+?[0-9]{7,15}$/|max:15',
            'password' => 'required|string|min:8|confirmed',
            'second_phone_number' => 'string|regex:/^\+?[0-9]{7,15}$/|max:15',
            'address' => 'required|array',
            'address.city' => 'required|string|max:255',
            'address.district' => 'nullable|string|max:255',
            'address.street_address' => 'required|string|max:255',
        ];
        return $rules;
    }
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone_number.max' => 'Phone number must not exceed 15 characters.',
            'second_phone_number.max' => 'Secondary phone number must not exceed 15 characters.',
        ];
    }
}
