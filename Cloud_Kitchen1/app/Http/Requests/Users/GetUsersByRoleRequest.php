<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class GetUsersByRoleRequest extends FormRequest
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
     */    public function rules(): array
    {
        return [
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['required', 'string', 'in:customer,kitchen_staff,delivery,admin'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */    public function messages(): array
    {
        return [
            'roles.array' => 'When provided, roles must be an array',
            'roles.*.required' => 'Each role in the array must have a value',
            'roles.*.in' => 'Invalid role. Must be one of: customer, kitchen_staff, delivery, admin'
        ];
    }
} 