<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class CompleteMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_selesai'        => 'required|date',
            'biaya'                  => 'nullable|numeric|min:0',
            'tindakan_perbaikan'     => 'required|string',
            'status_aset_setelahnya' => 'required|in:Tersedia,Dipinjam,Rusak',
            'nama_teknisi'           => 'nullable|string|max:255',
        ];
    }
}
