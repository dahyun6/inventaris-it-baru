<?php

namespace App\Http\Requests\Departemen;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartemenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_departemen' => ['required', 'string', 'max:255', 'unique:departemens,nama_departemen'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_departemen.required' => 'Nama departemen wajib diisi.',
            'nama_departemen.unique'   => 'Nama departemen tersebut sudah terdaftar.',
            'nama_departemen.max'      => 'Nama departemen maksimal 255 karakter.',
        ];
    }
}
