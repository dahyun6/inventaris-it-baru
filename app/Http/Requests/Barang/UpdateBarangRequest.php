<?php

namespace App\Http\Requests\Barang;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $barang = $this->route('barang');
        $barangId = $barang instanceof \App\Models\Barang ? $barang->id : $barang;

        return [
            'category_id'   => 'required|exists:categories,id',
            'no_aset_local' => 'nullable|string|max:100|unique:barangs,no_aset_local,' . $barangId,
            'model'         => 'required|string|max:255',
            'type_spec'     => 'nullable|string',
            'serial_number' => 'nullable|string|max:100|unique:barangs,serial_number,' . $barangId,
            'hostname'      => 'nullable|string|max:150',
            'buy_date'      => 'nullable|date',
            'vendor'        => 'nullable|string|max:255',
            'unit_loc'      => 'nullable|string|max:255',
            'dept'          => 'nullable|string|max:255',
            'pengguna'      => 'nullable|string|max:255',
            'position_user' => 'nullable|string|max:255',
            'note'                        => 'nullable|string',
            'status'                      => 'required|in:Tersedia,Dipinjam,Rusak',
            'interval_maintenance'        => 'nullable|integer|min:0|max:120',
            'tgl_maintenance_berikutnya'  => 'nullable|date',
        ];
    }
}
