<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'email'     => 'nullable|email|max:255',
            'outlet_id' => 'required|uuid|exists:outlets,id',
            'role'      => 'required|string|max:100',
            'address'   => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'joined_at' => 'nullable|date',
        ];
    }
}
