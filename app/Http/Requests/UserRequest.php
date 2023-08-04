<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => $this->isMethod('patch') ? ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)] : ['required', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->isMethod('patch') ? ['nullable', Password::defaults(), 'confirmed'] : ['required', Password::defaults(), 'confirmed'],
            'max_discount' => ['required', 'numeric', 'min:0', 'max:100.00',],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:50'],
        ];
    }
}