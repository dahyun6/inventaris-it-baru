<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'role_id',
        'departemen_id',
        'lokasi_id',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'role_id'           => 'integer',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiUnit::class, 'lokasi_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_id === 1 || $this->role?->name === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role_id, [1, 2]) 
            || in_array($this->role?->name, ['super_admin', 'admin']) 
            || (bool) $this->is_admin;
    }

    public function isStaff(): bool
    {
        return !$this->isAdmin();
    }

    public function getRoleDisplayName(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Super Admin';
        }
        if ($this->isAdmin()) {
            return 'Admin IT';
        }
        return 'Staff Pengguna';
    }
}
