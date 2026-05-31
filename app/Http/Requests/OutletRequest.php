<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutletRequest extends FormRequest
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
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'email'         => 'nullable|email|max:255',
            'city'          => 'required|string|max:100',
            'manager'       => 'nullable|string|max:100',
            'address'       => 'nullable|string',
            'is_active'     => 'nullable|boolean',
            'payment_type'  => 'nullable|in:pay_first,pay_later,dp_first',
            'dp_percentage' => 'nullable|integer|min:0|max:100',
        ];
    }
}
