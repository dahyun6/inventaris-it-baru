<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiUnit extends Model
{
    use HasFactory;

    protected $table = 'lokasi_units';

    protected $fillable = [
        'nama_lokasi',
        'kode_lokasi',
        'keterangan',
    ];

    /**
     * Users assigned to this location.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'lokasi_id');
    }

    /**
     * Assets / units located at this location name.
     */
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'unit_loc', 'nama_lokasi');
    }
}
