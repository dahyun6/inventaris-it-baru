<?php

namespace App\Services;

use App\Exports\BarangTemplateExport;
use App\Imports\BarangImport;
use App\Models\Barang;
use App\Models\Category;
use App\Models\RiwayatAset;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BarangService
{
    /**
     * Download standard Excel import template.
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new BarangTemplateExport(), 'template_import_aset_it.xlsx');
    }
    /**
     * Get all barangs with category eager loaded (filtered for staff).
     */
    public function getAll(?\App\Models\User $user = null): Collection
    {
        $query = Barang::with('category')->latest();

        if ($user && $user->isStaff()) {
            $userName = $user->name;
            $userId = $user->id;
            $query->where(function ($q) use ($userName, $userId) {
                $q->where('pengguna', 'like', '%' . $userName . '%')
                  ->orWhereHas('riwayat', function ($r) use ($userName, $userId) {
                      $r->where('user_id', $userId)
                        ->orWhere('penerima_nama', 'like', '%' . $userName . '%');
                  });
            });
        }

        return $query->get();
    }

    /**
     * Get form dependencies (categories, vendors & users) for create/edit.
     */
    public function getFormData(): array
    {
        return [
            'categories'  => Category::all(),
            'vendors'     => Vendor::orderBy('nama_vendor')->get(),
            'users'       => User::with(['departemen', 'lokasi'])->orderBy('name')->get(),
            'departemens' => \App\Models\Departemen::orderBy('nama_departemen')->get(),
            'lokasis'     => \App\Models\LokasiUnit::orderBy('nama_lokasi')->get(),
        ];
    }

    /**
     * Get single barang with detailed relationships.
     */
    public function getDetail(Barang $barang): Barang
    {
        return $barang->load([
            'riwayat' => function ($query) {
                $query->orderByDesc('tanggal_serah_terima')
                      ->orderByDesc('id');
            },
            'riwayat.user',
            'maintenances' => function ($query) {
                $query->orderByDesc('tanggal_mulai')
                      ->orderByDesc('id');
            },
            'maintenances.vendor',
            'maintenances.user',
            'tickets' => function ($query) {
                $query->orderByDesc('created_at');
            },
            'tickets.assignedUser',
            'category',
        ]);
    }

    /**
     * Create a new barang with auto asset code generation if not provided.
     */
    public function create(array $data): Barang
    {
        if (empty($data['no_aset_local'])) {
            $data['no_aset_local'] = $this->generateNextAssetCode($data['category_id'] ?? null);
        }

        if (!empty($data['interval_maintenance']) && empty($data['tgl_maintenance_berikutnya'])) {
            $startDate = !empty($data['buy_date']) ? \Carbon\Carbon::parse($data['buy_date']) : now();
            $data['tgl_maintenance_berikutnya'] = $startDate->addMonths((int) $data['interval_maintenance']);
        }

        $barang = Barang::create($data);

        ActivityLogService::log(
            'Aset',
            'CREATE',
            $barang->no_aset_local,
            "Menambahkan aset baru: [{$barang->no_aset_local}] {$barang->model} (SN: {$barang->serial_number})",
            $barang->id
        );

        return $barang;
    }

    /**
     * Update an existing barang.
     */
    public function update(Barang $barang, array $data): Barang
    {
        if (empty($data['no_aset_local'])) {
            $data['no_aset_local'] = 'AST-' . date('Ym') . '-' . strtoupper(Str::random(5));
        }

        if (!empty($data['interval_maintenance']) && empty($data['tgl_maintenance_berikutnya'])) {
            $lastMnt = $barang->maintenances()->where('status', 'Selesai')->first();
            $baseDate = $lastMnt ? \Carbon\Carbon::parse($lastMnt->tanggal_selesai) : now();
            $data['tgl_maintenance_berikutnya'] = $baseDate->addMonths((int) $data['interval_maintenance']);
        }

        $barang->update($data);

        ActivityLogService::log(
            'Aset',
            'UPDATE',
            $barang->no_aset_local,
            "Memperbarui data aset [{$barang->no_aset_local}] {$barang->model} (Status: {$barang->status})",
            $barang->id
        );

        return $barang;
    }

    /**
     * Delete a barang.
     */
    public function delete(Barang $barang): bool
    {
        $code = $barang->no_aset_local;
        $id = $barang->id;
        $res = (bool) $barang->delete();

        if ($res) {
            ActivityLogService::log(
                'Aset',
                'DELETE',
                $code,
                "Menghapus data aset {$code}",
                $id
            );
        }

        return $res;
    }

    /**
     * Record single asset handover and update status.
     */
    public function recordHandover(Barang $barang, array $data): RiwayatAset
    {
        $user = !empty($data['user_id']) ? User::with('departemen')->find($data['user_id']) : null;
        $penerimaNama = $user ? $user->name : null;
        $penerimaDept = $user?->departemen?->nama_departemen;

        $barang->update([
            'status'   => $data['status'],
            'pengguna' => $penerimaNama,
            'dept'     => $penerimaDept,
            'unit_loc' => $data['lokasi'],
        ]);

        $countThisMonth = RiwayatAset::whereNotNull('no_surat')
            ->whereYear('tanggal_serah_terima', date('Y'))
            ->whereMonth('tanggal_serah_terima', date('m'))
            ->distinct('no_surat')
            ->count('no_surat');
        $noSurat = 'STT/' . date('Ym') . '/' . str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);

        $riwayat = RiwayatAset::create([
            'no_surat'             => $noSurat,
            'barang_id'            => $barang->id,
            'user_id'              => $data['user_id'] ?? null,
            'diserahkan_oleh'      => auth()->user()?->name ?? 'Admin IT',
            'penerima_nama'        => $penerimaNama ?? 'Gudang IT',
            'penerima_dept'        => $penerimaDept,
            'lokasi'               => $data['lokasi'],
            'keterangan'           => $data['keterangan'] ?? null,
            'tanggal_serah_terima' => $data['tanggal_serah_terima'],
        ]);

        ActivityLogService::log(
            'Handover',
            'HANDOVER',
            $noSurat,
            "Handover aset {$barang->no_aset_local} kepada " . ($penerimaNama ?? 'Gudang IT') . " ({$noSurat})",
            $riwayat->id
        );

        return $riwayat;
    }

    /**
     * Import barangs from Excel file and return count.
     */
    public function importExcel($file): int
    {
        $import = new BarangImport();
        Excel::import($import, $file);

        ActivityLogService::log(
            'Aset',
            'IMPORT',
            'Excel Import',
            "Import massal {$import->rowCount} data aset dari file Excel",
            null
        );

        return $import->rowCount;
    }

    /**
     * Generate next asset code based on category prefix (for AJAX or creation).
     */
    public function generateNextAssetCode($categoryId): string
    {
        if (!$categoryId) {
            return 'AST-' . date('Ym') . '-' . strtoupper(Str::random(5));
        }

        $category = Category::find($categoryId);

        if (!$category || empty($category->kode_prefix)) {
            return 'AST-' . date('Ym') . '-' . strtoupper(Str::random(5));
        }

        $prefix = $category->kode_prefix;

        $lastBarang = Barang::where('category_id', $category->id)
            ->where('no_aset_local', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBarang) {
            $parts = explode('-', $lastBarang->no_aset_local);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate asset code for AJAX endpoint.
     */
    public function getSuggestedCodeForCategory($categoryId): string
    {
        if (!$categoryId) {
            return '';
        }

        $category = Category::find($categoryId);
        if (!$category || empty($category->kode_prefix)) {
            return '';
        }

        return $this->generateNextAssetCode($categoryId);
    }
}
