<?php

namespace App\Http\Controllers;

use App\Http\Requests\Maintenance\CompleteMaintenanceRequest;
use App\Http\Requests\Maintenance\StoreMaintenanceRequest;
use App\Http\Requests\Maintenance\UpdateMaintenanceRequest;
use App\Models\Maintenance;
use App\Services\MaintenanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function __construct(
        protected MaintenanceService $maintenanceService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $maintenances = $this->maintenanceService->getAll($status);
        $metrics = $this->maintenanceService->getMetrics();
        $upcomingPreventives = $this->maintenanceService->getUpcomingPreventive(30);

        return view('maintenance.index', compact('maintenances', 'metrics', 'status', 'upcomingPreventives'));
    }

    public function create(Request $request): View
    {
        $barangId = $request->query('barang_id');
        $formData = $this->maintenanceService->getFormData($barangId ? (int) $barangId : null);

        return view('maintenance.create', [
            'autoNo'         => $formData['autoNo'],
            'barangs'        => $formData['barangs'],
            'vendors'        => $formData['vendors'],
            'selectedBarang' => $formData['selectedBarang'],
        ]);
    }

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $maintenance = $this->maintenanceService->create($request->validated(), Auth::id());

        return redirect()->route('maintenance.show', $maintenance->id)
            ->with('success', 'Catatan maintenance aset (' . $maintenance->no_maintenance . ') berhasil dibuat!');
    }

    public function show(Maintenance $maintenance): View
    {
        $maintenance->load(['barang.category', 'user', 'vendor']);

        return view('maintenance.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance): View
    {
        $maintenance->load(['barang.category', 'user', 'vendor']);
        $formData = $this->maintenanceService->getFormData();

        return view('maintenance.edit', [
            'maintenance' => $maintenance,
            'barangs'     => $formData['barangs'],
            'vendors'     => $formData['vendors'],
        ]);
    }

    public function update(UpdateMaintenanceRequest $request, Maintenance $maintenance): RedirectResponse
    {
        $this->maintenanceService->update($maintenance, $request->validated());

        return redirect()->route('maintenance.show', $maintenance->id)
            ->with('success', 'Data maintenance (' . $maintenance->no_maintenance . ') berhasil diperbarui!');
    }

    public function complete(CompleteMaintenanceRequest $request, Maintenance $maintenance): RedirectResponse
    {
        $this->maintenanceService->complete($maintenance, $request->validated());

        return redirect()->route('maintenance.show', $maintenance->id)
            ->with('success', 'Maintenance (' . $maintenance->no_maintenance . ') telah berhasil diselesaikan!');
    }

    public function destroy(Maintenance $maintenance): RedirectResponse
    {
        $no = $maintenance->no_maintenance;
        $this->maintenanceService->delete($maintenance);

        return redirect()->route('maintenance.index')
            ->with('success', 'Data maintenance (' . $no . ') berhasil dihapus!');
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $status = $request->query('status');
        $year = $request->query('year');
        $month = $request->query('month');

        return $this->maintenanceService->exportExcel($status, $year, $month);
    }

    public function printReport(Request $request): View
    {
        $status = $request->query('status');
        $maintenances = $this->maintenanceService->getAll($status);
        $metrics = $this->maintenanceService->getMetrics();

        return view('maintenance.report-print', compact('maintenances', 'metrics', 'status'));
    }

    public function print(Maintenance $maintenance): View
    {
        $maintenance->load(['barang.category', 'user', 'vendor']);

        return view('maintenance.print', compact('maintenance'));
    }
}
