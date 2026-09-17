<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('maintenance')?->id ?? $this->maintenance;

        return [
            'no_maintenance'         => 'required|string|max:100|unique:maintenances,no_maintenance,' . $id,
            'barang_id'              => 'required|exists:barangs,id',
            'jenis_maintenance'      => 'required|string|max:100',
            'tanggal_mulai'          => 'required|date',
            'tanggal_selesai'        => 'nullable|date|after_or_equal:tanggal_mulai',
            'biaya'                  => 'nullable|numeric|min:0',
            'pelaksana'              => 'required|string|in:Internal IT,Vendor Eksternal',
            'vendor_id'              => 'nullable|required_if:pelaksana,Vendor Eksternal|exists:vendors,id',
            'nama_teknisi'           => 'nullable|string|max:255',
            'status'                 => 'required|in:Dalam Proses,Selesai,Dibatalkan',
            'deskripsi_kendala'      => 'required|string',
            'tindakan_perbaikan'     => 'nullable|string',
            'status_aset_setelahnya' => 'nullable|string|in:Tersedia,Dipinjam,Rusak',
            'update_status_aset'     => 'nullable|boolean',
        ];
    }
}
