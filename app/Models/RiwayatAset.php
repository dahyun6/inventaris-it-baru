<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatAset extends Model
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
        'no_surat', 'barang_id', 'user_id', 'diserahkan_oleh', 
        'penerima_nama', 'penerima_dept', 'penerima_jabatan',
        'lokasi', 'keterangan', 'tanggal_serah_terima',
        'status_terima', 'accepted_at', 'accepted_by'
    ];

    protected $casts = [
        'tanggal_serah_terima' => 'date',
        'accepted_at'          => 'datetime',
    ];

    public function barang() {
        return $this->belongsTo(Barang::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function accepter() {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function isAccepted(): bool
    {
        return $this->status_terima === 'accepted';
    }

    public function isPending(): bool
    {
        return $this->status_terima === 'pending';
    }

    /**
     * Get preceding handover log for the same asset.
     */
    public function previousLog(): ?self
    {
        return self::where('barang_id', $this->barang_id)
            ->where('id', '<', $this->id)
            ->orderByDesc('tanggal_serah_terima')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Accessor for origin location (Lokasi Asal).
     */
    public function getLokasiAsalAttribute(?string $value = null): string
    {
        if (!empty($value)) {
            return $value;
        }

        $prev = $this->previousLog();
        return $prev?->lokasi ?: 'Gudang IT';
    }

    /**
     * Accessor for origin person/entity (Pemberi / Dari Pihak A).
     */
    public function getPemberiNamaAttribute(?string $value = null): string
    {
        if (!empty($value)) {
            return $value;
        }

        if (!empty($this->diserahkan_oleh)) {
            return $this->diserahkan_oleh;
        }

        $prev = $this->previousLog();
        if ($prev) {
            return $prev->penerima_nama ?: ($prev->user?->name ?: 'Gudang IT');
        }

        return 'Gudang IT';
    }

    /**
     * Accessor for destination person/entity (Penerima / Ke Pihak B).
     */
    public function getPenerimaDisplayAttribute(?string $value = null): string
    {
        if (!empty($value)) {
            return $value;
        }

        return $this->penerima_nama ?: ($this->user?->name ?: 'Gudang IT');
    }
}