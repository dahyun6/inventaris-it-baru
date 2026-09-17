<?php

namespace App\Http\Requests\Departemen;

use App\Models\Departemen;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartemenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departemen = $this->route('departemen');
        $departemenId = $departemen instanceof Departemen ? $departemen->id : $departemen;

        return [
            'nama_departemen' => [
                'required',
                'string',
                'max:255',
                'unique:departemens,nama_departemen,' . $departemenId,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_departemen.required' => 'Nama departemen wajib diisi.',
            'nama_departemen.unique'   => 'Nama departemen tersebut sudah digunakan.',
            'nama_departemen.max'      => 'Nama departemen maksimal 255 karakter.',
        ];
    }
}
