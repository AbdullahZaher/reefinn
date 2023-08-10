<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'hotel_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'branch_no' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'commercial_register' => ['required', 'string', 'max:255'],
            'tax_number' => ['required', 'string', 'max:255'],
            'checkout_default_time' => ['required', 'date_format:H:i'],
            'auto_renew_after' => ['required', 'integer', 'min:1', 'max:23'],
            'timezone' => ['required', 'timezone', 'max:255'],
        ];
    }
}