<?php

namespace App\Http\Controllers;

use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Barang;
use App\Models\User;
use App\Models\Category;
use App\Models\RiwayatAset;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // <-- Wajib untuk membuat string acak (Auto-Generate SN)

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $barangs = Barang::with('category')->latest()->get(); 
        $categories = Category::all(); 
        
        return view('barang.index', compact('barangs', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id', // Sebagai kolom 'jenis'
            'no_aset_local' => 'nullable|string|max:100|unique:barangs,no_aset_local',
            'model'         => 'required|string|max:255',
            'type_spec'     => 'nullable|string',
            'serial_number' => 'nullable|string|max:100|unique:barangs,serial_number',
            'hostname'      => 'nullable|string|max:150',
            'buy_date'      => 'nullable|date',
            'vendor'        => 'nullable|string|max:255',
            'unit_loc'      => 'nullable|string|max:255',
            'dept'          => 'nullable|string|max:255',
            'pengguna'      => 'nullable|string|max:255',
            'position_user' => 'nullable|string|max:255',
            'note'          => 'nullable|string',
            'status'        => 'required|in:Tersedia,Dipinjam,Rusak'
        ]);

        // Auto-Generate No Aset Local jika dikosongkan (Sangat berguna untuk aset tanpa SN pabrik)
        // Ganti blok if (empty($validated['no_aset_local'])) lama menjadi ini:
        if (empty($validated['no_aset_local'])) {
            $category = Category::find($validated['category_id']);
            if ($category && !empty($category->kode_prefix)) {
                $prefix = $category->kode_prefix;
                $lastBarang = Barang::where('category_id', $category->id)->where('no_aset_local', 'like', $prefix . '-%')->orderBy('id', 'desc')->first();
                $nextNumber = $lastBarang ? ((int) end(explode('-', $lastBarang->no_aset_local))) + 1 : 1;
                $validated['no_aset_local'] = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            } else {
                // Fallback jika tidak ada prefix, gunakan sistem lama
                $validated['no_aset_local'] = 'AST-' . date('Ym') . '-' . strtoupper(Str::random(5));
            }
        }

        Barang::create($validated);
        return redirect()->route('barang.index')->with('success', 'Aset berhasil ditambahkan!');
    }

    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'no_aset_local' => 'nullable|string|max:100|unique:barangs,no_aset_local,' . $barang->id,
            'model'         => 'required|string|max:255',
            'type_spec'     => 'nullable|string',
            'serial_number' => 'nullable|string|max:100|unique:barangs,serial_number,' . $barang->id,
            'hostname'      => 'nullable|string|max:150',
            'buy_date'      => 'nullable|date',
            'vendor'        => 'nullable|string|max:255',
            'unit_loc'      => 'nullable|string|max:255',
            'dept'          => 'nullable|string|max:255',
            'pengguna'      => 'nullable|string|max:255',
            'position_user' => 'nullable|string|max:255',
            'note'          => 'nullable|string',
            'status'        => 'required|in:Tersedia,Dipinjam,Rusak'
        ]);

        if (empty($validated['no_aset_local'])) {
            $validated['no_aset_local'] = 'AST-' . date('Ym') . '-' . strtoupper(Str::random(5));
        }

        $barang->update($validated);
        return redirect()->route('barang.index')->with('success', 'Aset berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Aset berhasil dihapus!');
    }

    public function show(Barang $barang)
{
    // Mengurutkan riwayat berdasarkan tanggal terbaru, lalu ID terbesar (input terakhir)
    $barang->load([
        'riwayat' => function($query) {
            $query->orderByDesc('tanggal_serah_terima')
                  ->orderByDesc('id');
        }, 
        'riwayat.user', 
        'category'
    ]);

    return view('barang.show', compact('barang'));
}

    public function handover(Barang $barang)
    {
        $users = User::all(); 
        return view('barang.handover', compact('barang', 'users'));
    }

    public function storeHandover(Request $request, Barang $barang)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'lokasi' => 'required|string|max:255',
            'status' => 'required|in:Tersedia,Dipinjam,Rusak',
            'keterangan' => 'nullable|string',
            'tanggal_serah_terima' => 'required|date',
        ]);

        $barang->update(['status' => $request->status]);

        RiwayatAset::create([
            'barang_id' => $barang->id,
            'user_id' => $request->user_id,
            'lokasi' => $request->lokasi,
            'keterangan' => $request->keterangan,
            'tanggal_serah_terima' => $request->tanggal_serah_terima,
        ]);

        // Redirect sudah menggunakan UUID agar tidak error 404
        return redirect()->route('barang.show', $barang->uuid)->with('success', 'Proses Handover berhasil dicatat!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            // Panggil class import ke dalam variabel
            $import = new BarangImport();
            Excel::import($import, $request->file('file_excel'));
            
            // Tampilkan jumlah baris yang sukses diimport
            return redirect()->route('barang.index')
                ->with('success', 'Fantastis! Berhasil menambahkan ' . $import->rowCount . ' baris data dari Excel!');
                
        } catch (\Exception $e) {
            return redirect()->route('barang.index')->withErrors(['Terjadi kesalahan format Excel: ' . $e->getMessage()]);
        }
    }

    // Fungsi baru untuk dipanggil oleh JavaScript (AJAX)
    public function generateAssetCode(Request $request)
    {
        $categoryId = $request->category_id;
        $category = Category::find($categoryId);

        // Jika kategori tidak dipilih atau tidak punya kode prefix, kembalikan kosong
        if (!$category || empty($category->kode_prefix)) {
            return response()->json(['kode' => '']);
        }

        $prefix = $category->kode_prefix;

        // Cari barang terakhir di kategori ini yang memiliki prefix tersebut
        $lastBarang = Barang::where('category_id', $categoryId)
            ->where('no_aset_local', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBarang) {
            // Jika sudah ada, pecah teksnya (misal: L2730-001) ambil angka 001-nya lalu tambah 1
            $parts = explode('-', $lastBarang->no_aset_local);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            // Jika belum ada barang sama sekali di kategori ini, mulai dari 1
            $nextNumber = 1;
        }

        // Format angka menjadi 3 digit (001, 002, 010, dst) dengan pemisah strip '-'
        $newCode = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return response()->json(['kode' => $newCode]);
    }
}