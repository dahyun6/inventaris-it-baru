<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Category;
use App\Models\Maintenance;
use App\Models\RiwayatAset;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get summary KPI counts.
     */
    public function getMetrics(): array
    {
        return [
            'total_aset'    => Barang::count(),
            'aset_tersedia' => Barang::where('status', 'Tersedia')->count(),
            'aset_dipinjam' => Barang::where('status', 'Dipinjam')->count(),
            'aset_rusak'    => Barang::where('status', 'Rusak')->count(),
        ];
    }

    /**
     * Get top categories by asset count.
     */
    public function getTopCategories(int $limit = 5): Collection
    {
        return Category::withCount('barangs')
            ->orderByDesc('barangs_count')
            ->take($limit)
            ->get();
    }

    /**
     * Get latest handovers with origin/destination tracking per unit.
     */
    public function getRecentHandovers(int $limit = 5): Collection
    {
        $handovers = RiwayatAset::with(['barang.category', 'user'])
            ->orderByDesc('tanggal_serah_terima')
            ->orderByDesc('id')
            ->take($limit)
            ->get();

        if ($handovers->isEmpty()) {
            return $handovers;
        }

        $barangIds = $handovers->pluck('barang_id')->unique();

        $priorLogs = RiwayatAset::whereIn('barang_id', $barangIds)
            ->where('id', '<', $handovers->max('id'))
            ->orderByDesc('tanggal_serah_terima')
            ->orderByDesc('id')
            ->get()
            ->groupBy('barang_id');

        foreach ($handovers as $log) {
            $prev = $priorLogs->get($log->barang_id, collect())
                ->first(fn($p) => $p->id < $log->id);

            $log->setAttribute('lokasi_asal', $prev?->lokasi ?: 'Gudang IT');
            $log->setAttribute('pemberi_nama', !empty($log->diserahkan_oleh) 
                ? $log->diserahkan_oleh 
                : ($prev?->penerima_nama ?: ($prev?->user?->name ?: 'Gudang IT')));
            $log->setAttribute('penerima_display', $log->penerima_nama ?: ($log->user?->name ?: 'Gudang IT'));
        }

        return $handovers;
    }

    /**
     * Get available years for filtering.
     */
    public function getAvailableYears(): array
    {
        $currentYear = (int) date('Y');
        $driver = DB::connection()->getDriverName();
        $yearExpression = $driver === 'sqlite' ? "strftime('%Y', created_at) as year" : "YEAR(created_at) as year";

        $yearsFromDb = Barang::selectRaw($yearExpression)
            ->whereNotNull('created_at')
            ->distinct()
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        $availableYears = array_values(array_unique(array_merge([$currentYear], $yearsFromDb)));
        rsort($availableYears);

        return $availableYears;
    }

    /**
     * Get localized month dropdown list.
     */
    public function getMonthList(): array
    {
        return [
            'all' => __('Semua Bulan (Sepanjang Tahun)'),
            '01'  => __('Januari'),
            '02'  => __('Februari'),
            '03'  => __('Maret'),
            '04'  => __('April'),
            '05'  => __('Mei'),
            '06'  => __('Juni'),
            '07'  => __('Juli'),
            '08'  => __('Agustus'),
            '09'  => __('September'),
            '10'  => __('Oktober'),
            '11'  => __('November'),
            '12'  => __('Desember'),
        ];
    }

    /**
     * Get trend chart data for a given year and month.
     */
    public function getTrendData(int $selectedYear, string $selectedMonth): array
    {
        $isIndo = app()->getLocale() === 'id';
        $shortMonths = $isIndo ? [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ] : [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $labels_grafik = [];
        $data_aset_masuk = [];
        $data_aset_diperbaiki = [];
        $driver = DB::connection()->getDriverName();

        if ($selectedMonth === 'all') {
            $monthExpr = $driver === 'sqlite' ? "cast(strftime('%m', created_at) as integer) as m" : "MONTH(created_at) as m";
            $monthMntExpr = $driver === 'sqlite' ? "cast(strftime('%m', COALESCE(tanggal_selesai, created_at)) as integer) as m" : "MONTH(COALESCE(tanggal_selesai, created_at)) as m";

            $masukByMonth = Barang::selectRaw("{$monthExpr}, COUNT(*) as total")
                ->whereYear('created_at', $selectedYear)
                ->groupBy('m')
                ->pluck('total', 'm')
                ->toArray();

            $hasMntCompleted = Maintenance::where('status', 'Selesai')->whereYear('tanggal_selesai', $selectedYear)->exists();

            if ($hasMntCompleted) {
                $diperbaikiByMonth = Maintenance::where('status', 'Selesai')
                    ->whereYear('tanggal_selesai', $selectedYear)
                    ->selectRaw("{$monthMntExpr}, COUNT(*) as total")
                    ->groupBy('m')
                    ->pluck('total', 'm')
                    ->toArray();
            } else {
                $monthUpdateExpr = $driver === 'sqlite' ? "cast(strftime('%m', updated_at) as integer) as m" : "MONTH(updated_at) as m";
                $diperbaikiByMonth = Barang::where('status', 'Tersedia')
                    ->whereYear('updated_at', $selectedYear)
                    ->selectRaw("{$monthUpdateExpr}, COUNT(*) as total")
                    ->groupBy('m')
                    ->pluck('total', 'm')
                    ->toArray();
            }

            for ($m = 1; $m <= 12; $m++) {
                $labels_grafik[] = $shortMonths[$m];
                $data_aset_masuk[] = (int) ($masukByMonth[$m] ?? 0);
                $data_aset_diperbaiki[] = (int) ($diperbaikiByMonth[$m] ?? 0);
            }
        } else {
            $mNum = (int) $selectedMonth;
            $monthCarbon = Carbon::create($selectedYear, $mNum, 1);
            $daysInMonth = $monthCarbon->daysInMonth;
            $monthShortName = $shortMonths[$mNum] ?? date('M', mktime(0, 0, 0, $mNum, 10));

            $dayExpr = $driver === 'sqlite' ? "cast(strftime('%d', created_at) as integer) as d" : "DAY(created_at) as d";

            $masukByDay = Barang::selectRaw("{$dayExpr}, COUNT(*) as total")
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $mNum)
                ->groupBy('d')
                ->pluck('total', 'd')
                ->toArray();

            $hasMntCompletedDay = Maintenance::where('status', 'Selesai')
                ->whereYear('tanggal_selesai', $selectedYear)
                ->whereMonth('tanggal_selesai', $mNum)
                ->exists();

            if ($hasMntCompletedDay) {
                $dayMntExpr = $driver === 'sqlite' ? "cast(strftime('%d', tanggal_selesai) as integer) as d" : "DAY(tanggal_selesai) as d";
                $diperbaikiByDay = Maintenance::where('status', 'Selesai')
                    ->whereYear('tanggal_selesai', $selectedYear)
                    ->whereMonth('tanggal_selesai', $mNum)
                    ->selectRaw("{$dayMntExpr}, COUNT(*) as total")
                    ->groupBy('d')
                    ->pluck('total', 'd')
                    ->toArray();
            } else {
                $dayUpdateExpr = $driver === 'sqlite' ? "cast(strftime('%d', updated_at) as integer) as d" : "DAY(updated_at) as d";
                $diperbaikiByDay = Barang::where('status', 'Tersedia')
                    ->whereYear('updated_at', $selectedYear)
                    ->whereMonth('updated_at', $mNum)
                    ->selectRaw("{$dayUpdateExpr}, COUNT(*) as total")
                    ->groupBy('d')
                    ->pluck('total', 'd')
                    ->toArray();
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $labels_grafik[] = $day . ' ' . $monthShortName;
                $data_aset_masuk[] = (int) ($masukByDay[$day] ?? 0);
                $data_aset_diperbaiki[] = (int) ($diperbaikiByDay[$day] ?? 0);
            }
        }

        return [
            'labels_grafik'            => $labels_grafik,
            'data_aset_masuk'          => $data_aset_masuk,
            'data_aset_diperbaiki'     => $data_aset_diperbaiki,
            'total_masuk_periode'      => array_sum($data_aset_masuk),
            'total_diperbaiki_periode' => array_sum($data_aset_diperbaiki),
        ];
    }

    /**
     * Compile complete dashboard payload.
     */
    public function getDashboardData(?int $requestedYear = null, ?string $requestedMonth = null): array
    {
        $metrics = $this->getMetrics();
        $topCategories = $this->getTopCategories();
        $recent_handovers = $this->getRecentHandovers();
        $availableYears = $this->getAvailableYears();
        $monthList = $this->getMonthList();

        $currentYear = (int) date('Y');
        $selectedYear = $requestedYear ?: $currentYear;
        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $currentYear;
        }

        $selectedMonth = $requestedMonth ?: date('m');
        if ($selectedMonth !== 'all' && !array_key_exists($selectedMonth, $monthList)) {
            $selectedMonth = date('m');
        }

        $trend = $this->getTrendData($selectedYear, $selectedMonth);

        $upcomingPreventives = Barang::with(['category', 'user'])
            ->whereNotNull('tgl_maintenance_berikutnya')
            ->where('tgl_maintenance_berikutnya', '<=', Carbon::now()->addDays(30)->endOfDay())
            ->orderBy('tgl_maintenance_berikutnya', 'asc')
            ->take(5)
            ->get();

        $preventiveOverdueCount = Barang::whereNotNull('tgl_maintenance_berikutnya')
            ->where('tgl_maintenance_berikutnya', '<', Carbon::today())
            ->count();

        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->take(5)
            ->get();

        return array_merge($metrics, [
            'topCategories'            => $topCategories,
            'recent_handovers'         => $recent_handovers,
            'upcoming_preventives'     => $upcomingPreventives,
            'preventive_overdue_count' => $preventiveOverdueCount,
            'recent_activities'        => $recentActivities,
            'availableYears'           => $availableYears,
            'selectedYear'             => $selectedYear,
            'monthList'                => $monthList,
            'selectedMonth'            => $selectedMonth,
            'labels_grafik'            => $trend['labels_grafik'],
            'data_aset_masuk'          => $trend['data_aset_masuk'],
            'data_aset_diperbaiki'     => $trend['data_aset_diperbaiki'],
            'total_masuk_periode'      => $trend['total_masuk_periode'],
            'total_diperbaiki_periode' => $trend['total_diperbaiki_periode'],
        ]);
    }
}
