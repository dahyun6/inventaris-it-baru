<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Category;
use App\Models\RiwayatAset;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mengambil data metrik KPI
        $total_aset = Barang::count();
        $aset_tersedia = Barang::where('status', 'Tersedia')->count();
        $aset_dipinjam = Barang::where('status', 'Dipinjam')->count();
        $aset_rusak = Barang::where('status', 'Rusak')->count();

        // 2. Mengambil 5 kategori teratas beserta jumlah barangnya
        $topCategories = Category::withCount('barangs')
                                ->orderBy('barangs_count', 'desc')
                                ->take(5)
                                ->get();

        // 3. Mengambil riwayat pergerakan aset
        $recent_handovers = RiwayatAset::with(['barang', 'user'])
                                      ->latest('tanggal_serah_terima')
                                      ->take(4)
                                      ->get();

        // 4. Daftar pilihan tahun & bulan untuk filter trend
        $currentYear = (int) date('Y');
        $yearsFromDb = Barang::selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->distinct()
            ->pluck('year')
            ->map(fn($y) => (int)$y)
            ->toArray();

        $availableYears = array_values(array_unique(array_merge([$currentYear], $yearsFromDb)));
        rsort($availableYears);

        $selectedYear = (int) $request->get('year', $currentYear);
        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $currentYear;
        }

        $monthList = [
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

        $selectedMonth = $request->get('month', date('m'));
        if ($selectedMonth !== 'all' && !array_key_exists($selectedMonth, $monthList)) {
            $selectedMonth = date('m');
        }

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

        if ($selectedMonth === 'all') {
            // Aggregasi 12 bulan untuk tahun yang dipilih
            $masukByMonth = Barang::selectRaw('MONTH(created_at) as m, COUNT(*) as total')
                ->whereYear('created_at', $selectedYear)
                ->groupBy('m')
                ->pluck('total', 'm')
                ->toArray();

            $diperbaikiByMonth = Barang::where('status', 'Tersedia')
                ->whereYear('updated_at', $selectedYear)
                ->selectRaw('MONTH(updated_at) as m, COUNT(*) as total')
                ->groupBy('m')
                ->pluck('total', 'm')
                ->toArray();

            for ($m = 1; $m <= 12; $m++) {
                $labels_grafik[] = $shortMonths[$m];
                $data_aset_masuk[] = (int) ($masukByMonth[$m] ?? 0);
                $data_aset_diperbaiki[] = (int) ($diperbaikiByMonth[$m] ?? 0);
            }
        } else {
            // Aggregasi harian untuk bulan & tahun yang dipilih
            $mNum = (int) $selectedMonth;
            $monthCarbon = Carbon::create($selectedYear, $mNum, 1);
            $daysInMonth = $monthCarbon->daysInMonth;
            $monthShortName = $shortMonths[$mNum];

            $masukByDay = Barang::selectRaw('DAY(created_at) as d, COUNT(*) as total')
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $mNum)
                ->groupBy('d')
                ->pluck('total', 'd')
                ->toArray();

            $diperbaikiByDay = Barang::where('status', 'Tersedia')
                ->whereYear('updated_at', $selectedYear)
                ->whereMonth('updated_at', $mNum)
                ->selectRaw('DAY(updated_at) as d, COUNT(*) as total')
                ->groupBy('d')
                ->pluck('total', 'd')
                ->toArray();

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $labels_grafik[] = $day . ' ' . $monthShortName;
                $data_aset_masuk[] = (int) ($masukByDay[$day] ?? 0);
                $data_aset_diperbaiki[] = (int) ($diperbaikiByDay[$day] ?? 0);
            }
        }

        $total_masuk_periode = array_sum($data_aset_masuk);
        $total_diperbaiki_periode = array_sum($data_aset_diperbaiki);

        // Jika request AJAX (misal saat user ganti dropdown tanpa refresh full page)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth,
                'labels' => $labels_grafik,
                'data_aset_masuk' => $data_aset_masuk,
                'data_aset_diperbaiki' => $data_aset_diperbaiki,
                'total_masuk' => $total_masuk_periode,
                'total_diperbaiki' => $total_diperbaiki_periode,
            ]);
        }

        // Kirim semua variabel ke view dashboard
        return view('dashboard', compact(
            'total_aset', 
            'aset_tersedia', 
            'aset_dipinjam', 
            'aset_rusak', 
            'topCategories', 
            'recent_handovers',
            'availableYears',
            'selectedYear',
            'monthList',
            'selectedMonth',
            'labels_grafik',
            'data_aset_masuk',
            'data_aset_diperbaiki',
            'total_masuk_periode',
            'total_diperbaiki_periode'
        ));
    }
}