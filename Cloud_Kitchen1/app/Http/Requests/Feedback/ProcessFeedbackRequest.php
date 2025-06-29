<?php

namespace App\Http\Requests\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class ProcessFeedbackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'feedbacks' => 'required|array',
            'feedbacks.*' => 'required|string|exists:feedback,feedback',
        ];
    }
}