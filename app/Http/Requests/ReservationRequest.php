<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(Request $request): array
    {
        return [
            'checkin' => $request->method() == 'POST' ? ['required', 'date', 'date_format:Y-m-d', 'after:yesterday',] : ['required', 'date', 'date_format:Y-m-d',],
            'checkout' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:checkin',],
            'price_for_night' => ['required', 'numeric', 'min:0',],
            'guest_id' => ['required', 'string', 'max:255',],
            'copy' => ['required', 'integer', 'min:0', 'max:255',],
            'guest_name' => ['required', 'string', 'max:255',],
            'guest_phone' => ['required', 'string', 'max:255',],
            'guest_birthday' => ['required', 'date', 'date_format:Y-m-d', 'before:yesterday',],
            'number_of_companions' => ['required', 'integer', 'min:0', 'max:50',],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'checkin.after' => __('Check-in date must be today or after'),
            'guest_birthday.before' => __('Guest birthday is invalid'),
        ];
    }
}