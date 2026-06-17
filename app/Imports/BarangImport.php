<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Category;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    private $seenSn = []; 
    private $seenAsetLocal = [];
    
    // Variabel publik untuk menghitung jumlah data yang sukses masuk
    public $rowCount = 0; 

    public function model(array $row)
    {
        if (empty($row['model'])) {
            return null;
        }

        $nama_kategori = !empty($row['jenis']) ? trim($row['jenis']) : 'Lainnya';
        $category = Category::firstOrCreate(['nama_kategori' => $nama_kategori]);

        $buyDate = null;
        if (!empty($row['buy_date'])) {
            // 1. Cek dulu apakah formatnya angka (Serial Date bawaan Excel)
            if (is_numeric($row['buy_date'])) {
                $buyDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['buy_date'])->format('Y-m-d');
            } 
            // 2. Jika bukan angka (misal user mengetik manual "2023-12-25" sebagai teks)
            else {
                try {
                    $buyDate = Carbon::parse($row['buy_date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $buyDate = null; // Biarkan null jika format teksnya sangat berantakan
                }
            }
        }

        $status = 'Tersedia';
        if (!empty($row['pengguna']) && strtolower(trim($row['pengguna'])) !== 'gudang') {
            $status = 'Dipinjam';
        }

        $serial_number = !empty($row['serial_number']) ? trim($row['serial_number']) : null;
        if ($serial_number) {
            $exists_in_db = Barang::where('serial_number', $serial_number)->exists();
            $exists_in_file = in_array($serial_number, $this->seenSn);

            if ($exists_in_db || $exists_in_file) {
                $serial_number = $serial_number . '-' . strtoupper(Str::random(4));
            }
            $this->seenSn[] = $serial_number;
        }

        $no_aset_local = !empty($row['no_aset_local']) ? trim($row['no_aset_local']) : ('AST-' . strtoupper(Str::random(6)));
        $exists_db_aset = Barang::where('no_aset_local', $no_aset_local)->exists();
        $exists_file_aset = in_array($no_aset_local, $this->seenAsetLocal);

        if ($exists_db_aset || $exists_file_aset) {
            $no_aset_local = $no_aset_local . '-' . strtoupper(Str::random(3));
        }
        $this->seenAsetLocal[] = $no_aset_local;

        // Tambah hitungan setiap kali data berhasil disiapkan
        $this->rowCount++;

        return new Barang([
            'category_id'   => $category->id,
            'no_aset_local' => $no_aset_local,
            'model'         => $row['model'],
            'type_spec'     => $row['type_spec'] ?? null,
            'serial_number' => $serial_number,
            'hostname'      => $row['hostname'] ?? null,
            'buy_date'      => $buyDate,
            'vendor'        => $row['vendor'] ?? null,
            'unit_loc'      => $row['unit_loc'] ?? null,
            'dept'          => $row['dept'] ?? null,
            'pengguna'      => $row['pengguna'] ?? null,
            'position_user' => $row['position_user'] ?? null,
            'note'          => $row['note'] ?? null,
            'status'        => $status,
        ]);
    }
}