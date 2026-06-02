<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:inventories,code',
            'brand' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'emoji' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:10',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'outlet_id' => 'required|exists:outlets,id',
            'desc' => 'nullable|string',
        ];
    }
}
