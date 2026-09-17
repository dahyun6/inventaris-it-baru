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
        'note', 'status', 'interval_maintenance', 'tgl_maintenance_berikutnya'
    ];

    protected $casts = [
        'buy_date'                   => 'date',
        'tgl_maintenance_berikutnya' => 'date',
        'interval_maintenance'       => 'integer',
    ];

    public function isMaintenanceOverdue(): bool
    {
        return $this->tgl_maintenance_berikutnya && $this->tgl_maintenance_berikutnya->startOfDay()->lt(\Carbon\Carbon::today());
    }

    public function isMaintenanceDueSoon(int $days = 30): bool
    {
        if (!$this->tgl_maintenance_berikutnya) {
            return false;
        }

        $date = $this->tgl_maintenance_berikutnya->startOfDay();
        $today = \Carbon\Carbon::today();

        return $date->gte($today) && $date->lte($today->copy()->addDays($days));
    }

    public function getPreventiveStatus(int $days = 30): string
    {
        if (!$this->tgl_maintenance_berikutnya) {
            return 'none';
        }

        if ($this->isMaintenanceOverdue()) {
            return 'overdue';
        }

        if ($this->isMaintenanceDueSoon($days)) {
            return 'due_soon';
        }

        return 'ok';
    }

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

    public function lokasiUnit()
    {
        return $this->belongsTo(LokasiUnit::class, 'unit_loc', 'nama_lokasi');
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatAset::class)->orderBy('tanggal_serah_terima', 'desc');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class)->orderBy('tanggal_mulai', 'desc');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class)->orderBy('created_at', 'desc');
    }

    public function isAssignedTo(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $userName = strtolower(trim($user->name));
        $currentPengguna = strtolower(trim($this->pengguna ?? ''));

        if ($currentPengguna && str_contains($currentPengguna, $userName)) {
            return true;
        }

        return $this->riwayat()->where(function ($r) use ($user, $userName) {
            $r->where('user_id', $user->id)
              ->orWhereRaw('LOWER(penerima_nama) LIKE ?', ['%' . $userName . '%']);
        })->exists();
    }
}