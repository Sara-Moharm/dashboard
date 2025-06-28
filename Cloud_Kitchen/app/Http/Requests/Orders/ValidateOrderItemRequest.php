<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ValidateOrderItemRequest extends FormRequest
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
        $id = $this->route("order_item");

        return [
            "quantity"         => "sometimes|required|integer|min:1",
            "order_id"         => $id ? "sometimes|required|integer|min:1|exists:orders,id"
                                      : "required|integer|min:1|exists:orders,id",
            "category_dish_id" => $id ? "sometimes|required|integer|min:1|exists:category_dishes,id"
                                      : "required|integer|min:1|exists:category_dishes,id",
            'staff_id'         =>  'sometimes|nullable|exists:staff,id',
            "status"           =>  "sometimes|required|in:pending,preparing,ready,delivered"
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
