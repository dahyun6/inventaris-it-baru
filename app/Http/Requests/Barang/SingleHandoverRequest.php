<?php

namespace App\Http\Requests\Barang;

use Illuminate\Foundation\Http\FormRequest;

class SingleHandoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'              => 'nullable|exists:users,id',
            'lokasi'               => 'required|string|max:255',
            'status'               => 'required|in:Tersedia,Dipinjam,Rusak',
            'keterangan'           => 'nullable|string',
            'tanggal_serah_terima' => 'required|date',
        ];
    }
}
