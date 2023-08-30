<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApartmentRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:30'],
            'type' => ['required', 'integer', 'min:1', 'max:' . count(config('custom.apartments.types'))],
            'description' => ['required', 'integer', 'min:1', 'max:' . count(config('custom.apartments.descriptions'))],
            'price_for_night' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}