<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
    public function rules()
    {
        return [
            'resource_id' => 'required|exists:resources,id',
            'reserved_at' => 'required|date_format:Y-m-d H:i:s',
            'duration' => 'required|date_format:H:i:s', // Formato adecuado de duración
        ];
    }

    public function messages()
    {
        return [
            'reserved_at.required' => 'The reserved_at field is required.',
            'reserved_at.date_format' => 'The reserved_at field must be in Y-m-d H:i:s format.',
            'duration.required' => 'The duration field is required.',
            'duration.date_format' => 'The duration field must be in H:i:s format.',
        ];
    }
}
