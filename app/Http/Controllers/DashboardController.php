<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Category;
use App\Models\RiwayatAset;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Mengambil data metrik (Disesuaikan dengan snake_case di Blade kamu)
        $total_aset = Barang::count();
        $aset_tersedia = Barang::where('status', 'Tersedia')->count();
        $aset_dipinjam = Barang::where('status', 'Dipinjam')->count();
        $aset_rusak = Barang::where('status', 'Rusak')->count();

        // 2. Mengambil 5 kategori teratas beserta jumlah barangnya
        $topCategories = Category::withCount('barangs')
                                ->orderBy('barangs_count', 'desc')
                                ->take(5)
                                ->get();

        // 3. Mengambil riwayat pergerakan aset (Disamakan jadi 4 sesuai limit di Blade kamu)
        $recent_handovers = RiwayatAset::with(['barang', 'user'])
                                      ->latest('tanggal_serah_terima')
                                      ->take(4)
                                      ->get();

        // 4. LOGIKA GRAFIK AKTUAL (Januari - Juni 2026)
        $labels_grafik = [];
        $data_aset_masuk = [];
        $data_aset_diperbaiki = [];

        for ($i = 1; $i <= 6; $i++) {
            $tanggal = Carbon::create(2026, $i, 1);
            $labels_grafik[] = $tanggal->format("M 'y");

            // Hitung total aset masuk berdasarkan bulan di kolom created_at
            $data_aset_masuk[] = Barang::whereYear('created_at', 2026)
                ->whereMonth('created_at', $i)
                ->count();

            // Hitung aset selesai diperbaiki (updated_at bulan ini & status 'Tersedia')
            $data_aset_diperbaiki[] = Barang::whereYear('updated_at', 2026)
                ->whereMonth('updated_at', $i)
                ->where('status', 'Tersedia')
                ->count();
        }

        // Kirim semua variabel ke view dashboard
        return view('dashboard', compact(
            'total_aset', 
            'aset_tersedia', 
            'aset_dipinjam', 
            'aset_rusak', 
            'topCategories', 
            'recent_handovers',
            'labels_grafik',
            'data_aset_masuk',
            'data_aset_diperbaiki'
        ));
    }
}