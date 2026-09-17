<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'module',
        'action',
        'subject_id',
        'subject_name',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionBadgeClass(): string
    {
        return match (strtoupper((string)$this->action)) {
            'CREATE' => 'badge-phoenix badge-phoenix-success',
            'UPDATE' => 'badge-phoenix badge-phoenix-primary',
            'DELETE' => 'badge-phoenix badge-phoenix-danger',
            'HANDOVER', 'COMPLETE' => 'badge-phoenix badge-phoenix-info',
            default => 'badge-phoenix badge-phoenix-secondary',
        };
    }

    public function getModuleIcon(): string
    {
        return match ($this->module) {
            'Aset' => 'fas fa-laptop',
            'Maintenance' => 'fas fa-screwdriver-wrench',
            'Helpdesk' => 'fas fa-headset',
            'Handover' => 'fas fa-file-invoice',
            'User' => 'fas fa-user-gear',
            'Kategori' => 'fas fa-tags',
            'Departemen' => 'fas fa-building',
            'Lokasi' => 'fas fa-location-dot',
            'Vendor' => 'fas fa-truck-field',
            default => 'fas fa-folder',
        };
    }
}
