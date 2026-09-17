<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Record a new activity log entry.
     */
    public static function log(
        string $module,
        string $action,
        ?string $subjectName = null,
        ?string $description = null,
        mixed $subjectId = null
    ): ActivityLog {
        $user = Auth::user();

        return ActivityLog::create([
            'user_id'      => $user?->id,
            'user_name'    => $user?->name ?? 'Sistem IT',
            'module'       => $module,
            'action'       => strtoupper($action),
            'subject_id'   => $subjectId ? (string) $subjectId : null,
            'subject_name' => $subjectName,
            'description'  => $description,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
        ]);
    }

    /**
     * Get paginated and filtered activity logs.
     */
    public function getFilteredLogs(?string $module = null, ?string $action = null, ?string $search = null, int $perPage = 25): LengthAwarePaginator
    {
        $query = ActivityLog::with('user')->latest();

        if (!empty($module) && $module !== 'all') {
            $query->where('module', $module);
        }

        if (!empty($action) && $action !== 'all') {
            $query->where('action', $action);
        }

        if (!empty($search)) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get recent activities for dashboard widget.
     */
    public function getRecent(int $limit = 5): Collection
    {
        return ActivityLog::with('user')->latest()->take($limit)->get();
    }

    /**
     * Get unique modules list for filters.
     */
    public function getAvailableModules(): array
    {
        return [
            'Aset'        => __('Master Aset'),
            'Kategori'    => __('Kategori'),
            'Departemen'  => __('Departemen'),
            'Lokasi'      => __('Lokasi Unit'),
            'Vendor'      => __('Vendor'),
            'Maintenance' => __('Maintenance'),
            'Helpdesk'    => __('IT Helpdesk'),
            'Handover'    => __('Handover'),
            'User'        => __('User Management'),
        ];
    }
}
