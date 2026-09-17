<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\RiwayatAset;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HandoverService
{
    /**
     * Get all handover records with joins.
     */
    public function getAllHistory(): Collection
    {
        return DB::table('riwayat_asets')
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
    }

    /**
     * Get form dependencies for creating a new handover.
     */
    public function getCreateFormData(): array
    {
        return [
            'autoNoSurat' => $this->generateNoSurat(),
            'users'       => User::with(['departemen', 'lokasi'])->orderBy('name')->get(),
            'barangs'     => Barang::with('category')->orderBy('no_aset_local')->get(),
            'lokasis'     => \App\Models\LokasiUnit::orderBy('nama_lokasi')->get(),
        ];
    }

    /**
     * Generate automatic handover letter number (STT/YYYYMM/0001).
     */
    public function generateNoSurat(): string
    {
        $countThisMonth = RiwayatAset::whereNotNull('no_surat')
            ->whereYear('tanggal_serah_terima', date('Y'))
            ->whereMonth('tanggal_serah_terima', date('m'))
            ->distinct('no_surat')
            ->count('no_surat');

        return 'STT/' . date('Ym') . '/' . str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Process multiple asset handover in a transaction.
     */
    public function processHandover(array $data): string
    {
        $userId = $data['user_id'] ?? null;
        if (!$userId && !empty($data['penerima_nama'])) {
            $matchedUser = User::where('name', $data['penerima_nama'])->first();
            $userId = $matchedUser ? $matchedUser->id : null;
        }

        DB::transaction(function () use ($data, $userId) {
            foreach ($data['barang_ids'] as $barangId) {
                RiwayatAset::create([
                    'no_surat'             => $data['no_surat'],
                    'barang_id'            => $barangId,
                    'user_id'              => $userId,
                    'diserahkan_oleh'      => $data['diserahkan_oleh'],
                    'penerima_nama'        => $data['penerima_nama'],
                    'penerima_dept'        => $data['penerima_dept'] ?? null,
                    'penerima_jabatan'     => $data['penerima_jabatan'] ?? null,
                    'lokasi'               => $data['lokasi'],
                    'keterangan'           => $data['keterangan'] ?? null,
                    'tanggal_serah_terima' => $data['tanggal_serah_terima'],
                ]);

                Barang::where('id', $barangId)->update([
                    'status'        => $data['status'],
                    'pengguna'      => $data['penerima_nama'],
                    'dept'          => $data['penerima_dept'] ?? null,
                    'unit_loc'      => $data['lokasi'],
                    'position_user' => $data['penerima_jabatan'] ?? null,
                ]);
            }
        });

        ActivityLogService::log(
            'Handover',
            'HANDOVER',
            $data['no_surat'],
            "Menerbitkan Surat Tanda Terima {$data['no_surat']} (" . count($data['barang_ids']) . " unit perangkat diserahkan kepada {$data['penerima_nama']})",
            null
        );

        return $data['no_surat'];
    }

    /**
     * Get receipt items by letter number or ID.
     */
    public function getReceiptItems(?string $rawNoSurat): EloquentCollection
    {
        if (!$rawNoSurat) {
            return new EloquentCollection();
        }

        $decodedNoSurat = urldecode($rawNoSurat);

        $items = RiwayatAset::where('no_surat', $decodedNoSurat)
            ->orWhere('no_surat', $rawNoSurat)
            ->with(['barang.category', 'user'])
            ->get();

        if ($items->isEmpty() && is_numeric($decodedNoSurat)) {
            $single = RiwayatAset::with(['barang.category', 'user'])->find($decodedNoSurat);
            if ($single) {
                $items = new EloquentCollection([$single]);
            }
        }

        return $items;
    }
}
