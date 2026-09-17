<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Maintenance;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    /**
     * Get all maintenance records with relations.
     */
    public function getAll(?string $status = null): Collection
    {
        $query = Maintenance::with(['barang.category', 'user', 'vendor'])
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Get KPI metrics for maintenance summary.
     */
    public function getMetrics(): array
    {
        return [
            'total'        => Maintenance::count(),
            'dalam_proses' => Maintenance::where('status', 'Dalam Proses')->count(),
            'selesai'      => Maintenance::where('status', 'Selesai')->count(),
            'dibatalkan'   => Maintenance::where('status', 'Dibatalkan')->count(),
            'total_biaya'  => Maintenance::sum('biaya'),
        ];
    }

    /**
     * Generate automatic maintenance code (MNT-YYYYMM-0001).
     */
    public function generateNoMaintenance(): string
    {
        $prefix = 'MNT-' . date('Ym') . '-';
        $countThisMonth = Maintenance::where('no_maintenance', 'like', $prefix . '%')->count();

        return $prefix . str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get form dependencies for creating/editing maintenance.
     */
    public function getFormData(?int $barangId = null): array
    {
        return [
            'autoNo'         => $this->generateNoMaintenance(),
            'barangs'        => Barang::with('category')->orderBy('no_aset_local')->get(),
            'vendors'        => Vendor::orderBy('nama_vendor')->get(),
            'selectedBarang' => $barangId ? Barang::with('category')->find($barangId) : null,
        ];
    }

    /**
     * Store new maintenance and optionally update asset status.
     */
    public function create(array $data, ?int $userId = null): Maintenance
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['user_id'] = $userId;

            if ($data['pelaksana'] === 'Internal IT') {
                $data['vendor_id'] = null;
            }

            $maintenance = Maintenance::create($data);
            $barang = Barang::find($data['barang_id']);

            // Update status barang & jadwal preventive berikutnya
            if ($barang) {
                $barangUpdate = [];
                if ($data['status'] === 'Dalam Proses') {
                    $barangUpdate['status'] = 'Rusak';
                } elseif ($data['status'] === 'Selesai' && !empty($data['status_aset_setelahnya'])) {
                    $barangUpdate['status'] = $data['status_aset_setelahnya'];
                    if ($barang->interval_maintenance) {
                        $completionDate = !empty($data['tanggal_selesai']) ? \Carbon\Carbon::parse($data['tanggal_selesai']) : now();
                        $barangUpdate['tgl_maintenance_berikutnya'] = $completionDate->addMonths($barang->interval_maintenance);
                    }
                }
                if (!empty($barangUpdate)) {
                    $barang->update($barangUpdate);
                }
            }

            ActivityLogService::log(
                'Maintenance',
                'CREATE',
                $maintenance->no_maintenance,
                "Membuka tiket maintenance {$maintenance->no_maintenance} untuk aset {$barang?->no_aset_local} ({$maintenance->jenis_maintenance})",
                $maintenance->id
            );

            return $maintenance;
        });
    }

    /**
     * Update existing maintenance and asset status.
     */
    public function update(Maintenance $maintenance, array $data): Maintenance
    {
        return DB::transaction(function () use ($maintenance, $data) {
            if ($data['pelaksana'] === 'Internal IT') {
                $data['vendor_id'] = null;
            }

            $maintenance->update($data);
            $barang = Barang::find($maintenance->barang_id);

            if ($barang) {
                $barangUpdate = [];
                if ($data['status'] === 'Dalam Proses') {
                    $barangUpdate['status'] = 'Rusak';
                } elseif ($data['status'] === 'Selesai' && !empty($data['status_aset_setelahnya'])) {
                    $barangUpdate['status'] = $data['status_aset_setelahnya'];
                    if ($barang->interval_maintenance && !empty($data['tanggal_selesai'])) {
                        $barangUpdate['tgl_maintenance_berikutnya'] = \Carbon\Carbon::parse($data['tanggal_selesai'])->addMonths($barang->interval_maintenance);
                    }
                }
                if (!empty($barangUpdate)) {
                    $barang->update($barangUpdate);
                }
            }

            ActivityLogService::log(
                'Maintenance',
                'UPDATE',
                $maintenance->no_maintenance,
                "Memperbarui data maintenance {$maintenance->no_maintenance} (Status: {$maintenance->status})",
                $maintenance->id
            );

            return $maintenance;
        });
    }

    /**
     * Mark a maintenance as complete.
     */
    public function complete(Maintenance $maintenance, array $data): Maintenance
    {
        return DB::transaction(function () use ($maintenance, $data) {
            $maintenance->update([
                'status'                 => 'Selesai',
                'tanggal_selesai'        => $data['tanggal_selesai'],
                'biaya'                  => $data['biaya'] ?? $maintenance->biaya,
                'tindakan_perbaikan'     => $data['tindakan_perbaikan'],
                'status_aset_setelahnya' => $data['status_aset_setelahnya'],
                'nama_teknisi'           => $data['nama_teknisi'] ?? $maintenance->nama_teknisi,
            ]);

            $barang = Barang::find($maintenance->barang_id);
            if ($barang) {
                $barangUpdate = [
                    'status' => $data['status_aset_setelahnya'],
                ];
                if ($barang->interval_maintenance && !empty($data['tanggal_selesai'])) {
                    $barangUpdate['tgl_maintenance_berikutnya'] = \Carbon\Carbon::parse($data['tanggal_selesai'])->addMonths($barang->interval_maintenance);
                }
                $barang->update($barangUpdate);
            }

            ActivityLogService::log(
                'Maintenance',
                'COMPLETE',
                $maintenance->no_maintenance,
                "Menuntaskan maintenance {$maintenance->no_maintenance} (Biaya: Rp " . number_format($maintenance->biaya ?? 0, 0, ',', '.') . ")",
                $maintenance->id
            );

            return $maintenance;
        });
    }

    /**
     * Delete maintenance record.
     */
    public function delete(Maintenance $maintenance): bool
    {
        $no = $maintenance->no_maintenance;
        $id = $maintenance->id;
        $res = (bool) $maintenance->delete();

        if ($res) {
            ActivityLogService::log(
                'Maintenance',
                'DELETE',
                $no,
                "Menghapus data maintenance {$no}",
                $id
            );
        }

        return $res;
    }

    /**
     * Get upcoming / overdue preventive maintenance assets.
     */
    public function getUpcomingPreventive(int $days = 30): Collection
    {
        return Barang::with(['category', 'lokasiUnit'])
            ->whereNotNull('tgl_maintenance_berikutnya')
            ->whereDate('tgl_maintenance_berikutnya', '<=', now()->addDays($days))
            ->orderBy('tgl_maintenance_berikutnya')
            ->get();
    }

    /**
     * Export maintenance report to Excel.
     */
    public function exportExcel(?string $status = null, ?string $year = null, ?string $month = null): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'rekap_maintenance_it_' . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MaintenanceExport($status, $year, $month),
            $filename
        );
    }
}
