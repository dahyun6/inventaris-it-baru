<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\RiwayatAset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HandoverController extends Controller
{
    public function index()
    {
        $handovers = DB::table('riwayat_asets')
            ->join('barangs', 'riwayat_asets.barang_id', '=', 'barangs.id')
            ->leftJoin('categories', 'barangs.category_id', '=', 'categories.id')
            ->leftJoin('users', 'riwayat_asets.user_id', '=', 'users.id')
            ->select(
                'riwayat_asets.id',
                'riwayat_asets.no_surat',
                'riwayat_asets.created_at',
                'riwayat_asets.tanggal_serah_terima as tanggal', 
                'riwayat_asets.lokasi', 
                'riwayat_asets.keterangan as catatan', 
                'riwayat_asets.diserahkan_oleh',
                DB::raw("COALESCE(riwayat_asets.penerima_nama, users.name, 'Gudang IT') as pengguna_terakhir"),
                'riwayat_asets.penerima_dept',
                'barangs.no_aset_local as kode_aset', 
                'barangs.model',
                'barangs.serial_number',
                'categories.nama_kategori as kategori'
            )
            ->orderByDesc('riwayat_asets.tanggal_serah_terima')
            ->orderByDesc('riwayat_asets.id')
            ->get();

        return view('handover.index', compact('handovers'));
    }

    public function create()
    {
        // Auto-generate nomor surat tanda terima format: STT/YYYYMM/0001
        $countThisMonth = RiwayatAset::whereNotNull('no_surat')
            ->whereYear('tanggal_serah_terima', date('Y'))
            ->whereMonth('tanggal_serah_terima', date('m'))
            ->distinct('no_surat')
            ->count('no_surat');

        $autoNoSurat = 'STT/' . date('Ym') . '/' . str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);
        $users = User::orderBy('name')->get();
        $barangs = Barang::with('category')->orderBy('no_aset_local')->get();

        return view('handover.create', compact('autoNoSurat', 'users', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_surat' => 'required|string|max:100',
            'tanggal_serah_terima' => 'required|date',
            'diserahkan_oleh' => 'required|string|max:255',
            'penerima_nama' => 'required|string|max:255',
            'penerima_dept' => 'nullable|string|max:255',
            'penerima_jabatan' => 'nullable|string|max:255',
            'lokasi' => 'required|string|max:255',
            'status' => 'required|in:Dipinjam,Tersedia,Rusak',
            'barang_ids' => 'required|array|min:1',
            'barang_ids.*' => 'required|exists:barangs,id',
            'keterangan' => 'nullable|string',
        ]);

        $userId = $request->user_id;
        if (!$userId) {
            $matchedUser = User::where('name', $request->penerima_nama)->first();
            $userId = $matchedUser ? $matchedUser->id : null;
        }

        DB::transaction(function () use ($request, $userId) {
            foreach ($request->barang_ids as $barangId) {
                RiwayatAset::create([
                    'no_surat' => $request->no_surat,
                    'barang_id' => $barangId,
                    'user_id' => $userId,
                    'diserahkan_oleh' => $request->diserahkan_oleh,
                    'penerima_nama' => $request->penerima_nama,
                    'penerima_dept' => $request->penerima_dept,
                    'penerima_jabatan' => $request->penerima_jabatan,
                    'lokasi' => $request->lokasi,
                    'keterangan' => $request->keterangan,
                    'tanggal_serah_terima' => $request->tanggal_serah_terima,
                ]);

                Barang::where('id', $barangId)->update([
                    'status' => $request->status,
                    'pengguna' => $request->penerima_nama,
                    'dept' => $request->penerima_dept,
                    'unit_loc' => $request->lokasi,
                    'position_user' => $request->penerima_jabatan,
                ]);
            }
        });

        return redirect()->route('handover.receipt', ['no_surat' => urlencode($request->no_surat)])
            ->with('success', 'Surat Tanda Terima Serah Terima Aset (' . $request->no_surat . ') berhasil diterbitkan!');
    }

    public function receipt(Request $request, $no_surat = null)
    {
        $raw = $no_surat ?? $request->query('no_surat') ?? $request->query('id');
        $decodedNoSurat = urldecode((string)$raw);

        // Cari berdasarkan nomor surat (baik decoded maupun raw)
        $items = RiwayatAset::where('no_surat', $decodedNoSurat)
            ->orWhere('no_surat', $raw)
            ->with(['barang.category', 'user'])
            ->get();

        // Fallback jika dibuka berdasarkan record ID numeric single
        if ($items->isEmpty() && is_numeric($decodedNoSurat)) {
            $single = RiwayatAset::with(['barang.category', 'user'])->find($decodedNoSurat);
            if ($single) {
                $items = collect([$single]);
            }
        }

        if ($items->isEmpty()) {
            return redirect()->route('handover.history')->with('error', 'Surat Tanda Terima (' . ($decodedNoSurat ?: 'N/A') . ') tidak ditemukan.');
        }

        $first = $items->first();
        return view('handover.receipt', compact('items', 'first', 'decodedNoSurat'));
    }
}