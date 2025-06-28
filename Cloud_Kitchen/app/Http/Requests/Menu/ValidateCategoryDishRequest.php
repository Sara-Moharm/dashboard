<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ValidateCategoryDishRequest extends FormRequest
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
        $id = $this->route("category_dish");
        return [
            'title' => $id
                ? "sometimes|required|string|max:255|unique:category_dishes,title,$id"
                : 'required|string|max:255|unique:category_dishes,title',
            'price'=> $id
                ? "sometimes|required|numeric|min:0.01|max:9999.99"
                : "required|numeric|min:0.01|max:9999.99",
            'category_id' => $id
                ? "sometimes|required|integer|exists:categories,id"
                : "required|integer|exists:categories,id",
            'description' => 'nullable|string|max:1020',
            'image_url' => 'nullable|string|max:2048',
            "meal_rate"   => 'nullable|numeric|between:0,5.00',
            "availability"=> 'nullable|integer|min:0',
            "calories"    => $id
                ? "sometimes|required|float"
                : "required|float",
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