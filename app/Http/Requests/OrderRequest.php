<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'customer_id'    => ($isUpdate ? 'sometimes|' : 'required|') . 'ulid|exists:users,id',
            'outlet_id'      => ($isUpdate ? 'sometimes|' : 'required|') . 'uuid|exists:outlets,id',
            'service_type'   => ($isUpdate ? 'sometimes|' : 'required|') . 'string|max:255',
            'weight'         => ($isUpdate ? 'sometimes|' : 'required|') . 'numeric|min:0.01',
            'price_per_unit' => ($isUpdate ? 'sometimes|' : 'required|') . 'integer|min:0',
            'order_status'   => 'nullable|string|in:Baru,Proses,Selesai,Diambil',
            'payment_status' => 'nullable|string|in:Belum,Lunas',
            'payment_method' => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ];
    }
}
