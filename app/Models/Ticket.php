<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
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
        'no_tiket',
        'user_id',
        'nama_pelapor',
        'email_pelapor',
        'departemen_pelapor',
        'lokasi_pelapor',
        'barang_id',
        'assigned_to',
        'kategori',
        'prioritas',
        'status',
        'judul',
        'deskripsi',
        'solusi',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class)->orderBy('created_at', 'asc');
    }

    /**
     * Check if a ticket belongs to or was reported by the given user.
     */
    public function isOwnedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->user_id && (int) $this->user_id === (int) $user->id) {
            return true;
        }

        if (!empty($this->email_pelapor) && strcasecmp(trim($this->email_pelapor), trim($user->email)) === 0) {
            return true;
        }

        if (!empty($this->nama_pelapor)) {
            $ticketName = strtolower(trim($this->nama_pelapor));
            $userName = strtolower(trim($user->name));
            if ($ticketName === $userName || str_contains($ticketName, $userName) || str_contains($userName, $ticketName)) {
                return true;
            }
        }

        return false;
    }
}
