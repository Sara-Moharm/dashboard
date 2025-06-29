<?php

namespace App\Http\Requests\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'feedback' => 'required|string|min:2|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'feedback.min' => 'Feedback must be at least 2 characters',
        ];
    }
}