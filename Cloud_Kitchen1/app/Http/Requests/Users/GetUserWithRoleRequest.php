<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class GetUserWithRoleRequest extends FormRequest
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
            'role' => ['required', 'string', 'in:customer,kitchen_staff,delivery'],
            'id' => ['required', 'integer', 'exists:users,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
