<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StaffRegisterRequest extends FormRequest
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
            'role' => 'required|string|in:kitchen_staff,delivery,admin',
            'status' => 'string|in:available,busy',
            'shift_start' => ['required', 'date_format:H:i'],
            'shift_end' => ['required', 'date_format:H:i', 'after:shift_start'],
        ];
        
        
        return $rules;
    }
    public function messages(): array
{
    return [
        'email.unique' => 'This email is already registered.',
        'shift_end.after' => 'Shift end time must be after shift start time.',
        'password.confirmed' => 'Password confirmation does not match.',
        'phone_number.max' => 'Phone number must not exceed 15 characters.',
        'second_phone_number.max' => 'Secondary phone number must not exceed 15 characters.',
    ];
}
}
