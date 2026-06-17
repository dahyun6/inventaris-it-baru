<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
    'nama_kategori', 
    'kode_prefix' // <-- Tambahkan ini
];

    // Relasi: 1 Kategori bisa dipakai oleh banyak Barang
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}