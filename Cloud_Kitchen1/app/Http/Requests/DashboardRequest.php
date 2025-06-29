<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
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
            'period' => 'sometimes|in:today,this_week,this_month,last_3_months,last_6_months,this_year',
            'sentiments' => 'sometimes|array',
            'sentiments.*' => 'in:positive,negative,neutral',
            'aspects' => 'sometimes|array',
            'aspects.*' => 'string|max:255'
        ];
    }
}
