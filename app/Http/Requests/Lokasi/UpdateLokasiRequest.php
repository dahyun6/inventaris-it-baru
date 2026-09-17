<?php

namespace App\Http\Requests\Lokasi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLokasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lokasiId = $this->route('lokasi')?->id ?? $this->route('lokasi');

        return [
            'nama_lokasi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lokasi_units', 'nama_lokasi')->ignore($lokasiId),
            ],
            'kode_lokasi' => ['nullable', 'string', 'max:50'],
            'keterangan'  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'nama_lokasi.unique'   => 'Nama lokasi tersebut sudah terdaftar.',
            'nama_lokasi.max'      => 'Nama lokasi maksimal 255 karakter.',
        ];
    }
}
