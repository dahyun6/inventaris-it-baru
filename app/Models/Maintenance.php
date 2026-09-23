<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory, HasUuids;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $fillable = [
        'uuid',
        'no_maintenance',
        'barang_id',
        'user_id',
        'vendor_id',
        'jenis_maintenance',
        'tanggal_mulai',
        'tanggal_selesai',
        'biaya',
        'pelaksana',
        'nama_teknisi',
        'status',
        'deskripsi_kendala',
        'tindakan_perbaikan',
        'status_aset_setelahnya',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'biaya'           => 'decimal:2',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
