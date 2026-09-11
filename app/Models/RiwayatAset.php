<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatAset extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_surat', 'barang_id', 'user_id', 'diserahkan_oleh', 
        'penerima_nama', 'penerima_dept', 'penerima_jabatan',
        'lokasi', 'keterangan', 'tanggal_serah_terima'
    ];

    public function barang() {
        return $this->belongsTo(Barang::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}