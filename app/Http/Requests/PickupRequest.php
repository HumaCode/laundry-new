<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupRequest extends FormRequest
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
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_id'    => 'nullable|exists:users,id',
            'outlet_id'      => 'nullable|exists:outlets,id',
            'order_code'     => 'nullable|string|max:100',
            'address_from'   => 'required|string',
            'address_to'     => 'required|string',
            'service_type'   => 'nullable|string|max:255',
            'employee_id'    => 'nullable|exists:employees,id',
            'distance'       => 'nullable|numeric|min:0',
            'eta'            => 'nullable|string|max:100',
            'fee'            => 'nullable|integer|min:0',
            'scheduled_at'   => 'required|date',
            'weight'         => 'nullable|string|max:50',
            'notes'          => 'nullable|string',
            'status'         => 'nullable|string|in:menunggu,jemput,proses,antar,selesai,batal',
            'avatar_color'   => 'nullable|string|max:50',
        ];
    }
}
