<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(Request $request): View|JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($request->user()?->isStaff()) {
            return redirect()->route('barang.index');
        }

        $selectedYear = (int) $request->get('year', date('Y'));
        $selectedMonth = (string) $request->get('month', date('m'));

        if ($request->ajax() || $request->wantsJson()) {
            $trend = $this->dashboardService->getTrendData($selectedYear, $selectedMonth);

            return response()->json([
                'selectedYear'         => $selectedYear,
                'selectedMonth'        => $selectedMonth,
                'labels'               => $trend['labels_grafik'],
                'data_aset_masuk'      => $trend['data_aset_masuk'],
                'data_aset_diperbaiki' => $trend['data_aset_diperbaiki'],
                'total_masuk'          => $trend['total_masuk_periode'],
                'total_diperbaiki'     => $trend['total_diperbaiki_periode'],
            ]);
        }

        $data = $this->dashboardService->getDashboardData(
            $request->has('year') ? $selectedYear : null,
            $request->has('month') ? $selectedMonth : null
        );

        return view('dashboard', $data);
    }
}