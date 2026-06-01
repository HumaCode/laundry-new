<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'name'        => 'required|string|max:255',
            'emoji'       => 'nullable|string|max:10',
            'category'    => 'required|string|in:kiloan,satuan,paket,antar',
            'description' => 'nullable|string',
            'price'       => 'required|integer|min:0',
            'unit'        => 'required|string|max:50',
            'eta'         => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:100',
            'status'      => 'nullable|boolean',
            'express'     => 'nullable|boolean',
            'pickup'      => 'nullable|boolean',
            'target'      => 'nullable|integer|min:0',
            'min_qty'     => 'nullable|string|max:50',
            'features'    => 'nullable|array',
            'tiers'       => 'nullable|array',
        ];
    }
}
