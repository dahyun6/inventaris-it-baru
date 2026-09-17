<?php

namespace App\Http\Requests\Handover;

use Illuminate\Foundation\Http\FormRequest;

class StoreHandoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no_surat'             => 'required|string|max:100',
            'tanggal_serah_terima' => 'required|date',
            'diserahkan_oleh'      => 'required|string|max:255',
            'penerima_nama'        => 'required|string|max:255',
            'penerima_dept'        => 'nullable|string|max:255',
            'penerima_jabatan'     => 'nullable|string|max:255',
            'lokasi'               => 'required|string|max:255',
            'status'               => 'required|in:Dipinjam,Tersedia,Rusak',
            'barang_ids'           => 'required|array|min:1',
            'barang_ids.*'         => 'required|exists:barangs,id',
            'user_id'              => 'nullable|exists:users,id',
            'keterangan'           => 'nullable|string',
        ];
    }
}
