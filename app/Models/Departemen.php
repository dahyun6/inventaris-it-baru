<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_departemen',
    ];

    /**
     * Users belonging to this department.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id');
    }

    /**
     * Assets belonging to this department name.
     */
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'dept', 'nama_departemen');
    }
}
