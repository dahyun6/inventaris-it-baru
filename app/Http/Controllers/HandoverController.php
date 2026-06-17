<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HandoverController extends Controller
{
    public function index()
    {
        // Mengambil data dari tabel riwayat_asets dan menggabungkannya dengan tabel terkait
        $handovers = DB::table('riwayat_asets')
            ->join('barangs', 'riwayat_asets.barang_id', '=', 'barangs.id')
            ->leftJoin('categories', 'barangs.category_id', '=', 'categories.id')
            ->leftJoin('users', 'riwayat_asets.user_id', '=', 'users.id')
            ->select(
                'riwayat_asets.created_at', // <--- INI YANG BARU DITAMBAHKAN
                'riwayat_asets.tanggal_serah_terima as tanggal', 
                'riwayat_asets.lokasi', 
                'riwayat_asets.keterangan as catatan', 
                'barangs.no_aset_local as kode_aset', 
                'categories.nama_kategori as kategori', 
                'users.name as pengguna_terakhir'
            )
            ->orderByDesc('riwayat_asets.tanggal_serah_terima')
            ->orderByDesc('riwayat_asets.id')
            ->get();

        return view('handover.index', compact('handovers'));
    }
}