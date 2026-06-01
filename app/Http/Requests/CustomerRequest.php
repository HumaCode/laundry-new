<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'email'     => 'nullable|email|max:255',
            'dob'       => 'nullable|date',
            'gender'    => 'nullable|string|in:male,female,Laki-laki,Perempuan',
            'outlet_id' => 'nullable|uuid|exists:outlets,id',
            'tier'      => 'nullable|string|max:50',
            'address'   => 'nullable|string',
            'notes'     => 'nullable|string',
            'is_active' => 'nullable|string|in:0,1',
        ];
    }
}
