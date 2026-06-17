<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // <-- Panggil class UUID

class Barang extends Model
{
    // Tambahkan HasUuids di sini
    use HasFactory, HasUuids; 

    protected $fillable = [
        'category_id', 'no_aset_local', 'model', 'type_spec', 
        'serial_number', 'hostname', 'buy_date', 'vendor', 
        'unit_loc', 'dept', 'pengguna', 'position_user', 
        'note', 'status'
    ];

    // Memberi tahu Laravel kolom mana yang dipakai untuk UUID
    public function uniqueIds()
    {
        return ['uuid'];
    }

    // Memaksa URL menggunakan kolom UUID
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatAset::class)->orderBy('tanggal_serah_terima', 'desc');
    }
}