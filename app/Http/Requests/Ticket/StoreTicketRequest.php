<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no_tiket'           => 'required|string|max:100|unique:tickets,no_tiket',
            'user_id'            => 'nullable|exists:users,id',
            'nama_pelapor'       => 'required|string|max:255',
            'email_pelapor'      => 'nullable|email|max:255',
            'departemen_pelapor' => 'nullable|string|max:255',
            'lokasi_pelapor'     => 'nullable|string|max:255',
            'barang_id'          => 'nullable|exists:barangs,id',
            'assigned_to'        => 'nullable|exists:users,id',
            'kategori'           => 'required|string|max:100',
            'prioritas'          => 'required|in:Rendah,Sedang,Tinggi,Kritis',
            'status'             => 'required|in:Open,In Progress,Pending,Resolved,Closed',
            'judul'              => 'required|string|max:255',
            'deskripsi'          => 'required|string',
        ];
    }
}
