<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ValidateCategoryRequest extends FormRequest
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
        $id = $this->route('category');
        Log::info('Category route param:', ['category' => $this->route('category')]);

        return [
                'title' => $id
                            ? ['sometimes', 'required', 'string', 'max:255', Rule::unique('categories', 'title')->ignore($id)]
                            : ['required', 'string', 'max:255', Rule::unique('categories', 'title')],
            'description' => 'nullable|string|max:1020',
            'image_url' => 'nullable|string|max:2048',
            'category_dishes' => 'nullable|array',
            'category_dishes.*.id' => 'nullable|exists:category_dishes,id',
            'category_dishes.*.title' => 'required_without:category_dishes.*.id|string|max:255',
            'category_dishes.*.price' => 'required_without:category_dishes.*.id|numeric|min:0',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
       Log::error('Validation failed in request class', [
        'errors' => $validator->errors()
    ]);

    throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
        'message' => 'Validation Failed',
        'errors' => $validator->errors(),
    ], 422));
    }
}
