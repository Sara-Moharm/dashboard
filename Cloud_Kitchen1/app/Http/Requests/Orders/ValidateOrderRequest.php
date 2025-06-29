<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ValidateOrderRequest extends FormRequest
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
      protected function prepareForValidation(): void
    {
        if ($this->route('order') === null && $this->has('order_items')) {
            foreach ($this->input('order_items') as $index => $item) {
                if (array_key_exists('id', $item) && ($item['id'] === '' || $item['id'] === null)) {
                    // Update the 'id' to a special value to trigger validation failure
                    $this->merge([
                        "order_items.$index.id" => '__INVALID__', // Use $index to access the correct item
                    ]);
                }
            }
        }
    }

    public function rules(): array
    {
        $id = $this->route('order');
        \Log::info('Inside request');

        return [
            'status'                         =>  'sometimes|required|in:pending,preparing,ready,delivering,delivered,cancelled,failed',
            'customer_id'                    =>  'sometimes|nullable|exists:customers,id',
            'delivery_personnel_id'          =>  'sometimes|nullable|exists:staff,id',     
            'order_items'                    =>  $id 
                                               ? 'sometimes|required|array' 
                                               : 'required|array|min:1',
            'order_items.*.id'               => $id 
                                                ? 'sometimes|required|exists:order_items,id' 
                                                : 'prohibited'
            ,
            'order_items.*.category_dish_id' => $id
                                                ? 'required_without:order_items.*.id|exists:category_dishes,id'
                                                : 'required',
            'order_items.*.quantity'         => $id
                                                ? 'required_without:order_items.*.id|integer|min:1'
                                                : 'sometimes|required|integer|min:1',
            'customer_address_id'            => $id
                                                ? 'sometimes|integer|exists:addresses,id'
                                                : 'required|integer|exists:addresses,id',
        ];
    }

     protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Log::error('Validation failed in request class', [
            'errors' => $validator->errors()
        ]);

        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'message' => 'Validation Failed',
            'errors' => $validator->errors(),
        ], 422));
    }

}
