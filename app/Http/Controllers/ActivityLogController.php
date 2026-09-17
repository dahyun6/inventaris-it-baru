<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function index(Request $request): View
    {
        $module = $request->query('module');
        $action = $request->query('action');
        $search = $request->query('q');

        $logs = $this->activityLogService->getFilteredLogs($module, $action, $search, 25);
        $availableModules = $this->activityLogService->getAvailableModules();

        return view('admin.activity_log.index', compact('logs', 'availableModules', 'module', 'action', 'search'));
    }
}
