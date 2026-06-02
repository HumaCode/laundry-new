<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class LaporanFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Auth checks can be handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dateFrom' => ['nullable', 'date_format:Y-m-d'],
            'dateTo' => ['nullable', 'date_format:Y-m-d'],
            'outletSelect' => ['nullable', 'string', 'exists:outlets,id'],
        ];
    }
}
