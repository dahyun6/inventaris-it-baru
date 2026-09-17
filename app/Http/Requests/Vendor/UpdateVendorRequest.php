<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vendor = $this->route('vendor');
        $vendorId = $vendor instanceof \App\Models\Vendor ? $vendor->id : $vendor;

        return [
            'nama_vendor' => 'required|string|max:255|unique:vendors,nama_vendor,' . $vendorId,
            'alamat'      => 'nullable|string',
            'telepon'     => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:100',
        ];
    }
}
