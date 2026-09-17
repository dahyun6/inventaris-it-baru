<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_vendor' => 'required|string|unique:vendors,nama_vendor|max:255',
            'alamat'      => 'nullable|string',
            'telepon'     => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:100',
        ];
    }
}
