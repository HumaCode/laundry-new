<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qty' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'invoice' => 'nullable|string|max:100',
            'price' => 'nullable|integer|min:0',
            'date' => 'required|date',
        ];
    }
}
