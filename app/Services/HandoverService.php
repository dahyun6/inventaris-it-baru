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
    public function getAllHistory(?User $user = null): Collection
    {
        $query = DB::table('riwayat_asets')
            ->join('barangs', 'riwayat_asets.barang_id', '=', 'barangs.id')
            ->leftJoin('categories', 'barangs.category_id', '=', 'categories.id')
            ->leftJoin('users', 'riwayat_asets.user_id', '=', 'users.id')
            ->select(
                'riwayat_asets.id',
                'riwayat_asets.uuid as handover_uuid',
                'riwayat_asets.barang_id',
                'riwayat_asets.no_surat',
                'riwayat_asets.created_at',
                'riwayat_asets.tanggal_serah_terima as tanggal',
                'riwayat_asets.lokasi',
                'riwayat_asets.keterangan as catatan',
                'riwayat_asets.diserahkan_oleh',
                'riwayat_asets.status_terima',
                'riwayat_asets.accepted_at',
                'riwayat_asets.accepted_by',
                DB::raw("COALESCE(riwayat_asets.penerima_nama, users.name, 'Gudang IT') as pengguna_terakhir"),
                'riwayat_asets.penerima_dept',
                'barangs.no_aset_local as kode_aset',
                'barangs.model',
                'barangs.serial_number',
                'barangs.uuid as barang_uuid',
                'categories.nama_kategori as kategori'
            );

        if ($user && $user->isStaff()) {
            $userName = strtolower(trim($user->name));
            $query->where(function ($q) use ($user, $userName) {
                $q->where('riwayat_asets.user_id', $user->id)
                  ->orWhereRaw('LOWER(riwayat_asets.penerima_nama) LIKE ?', ['%' . $userName . '%']);
            });
        }

        $records = $query->orderByDesc('riwayat_asets.tanggal_serah_terima')
            ->orderByDesc('riwayat_asets.id')
            ->get();

        if ($records->isEmpty()) {
            return $records;
        }

        $barangIds = $records->pluck('barang_id')->unique();
        $allPrior = DB::table('riwayat_asets')
            ->whereIn('barang_id', $barangIds)
            ->select('id', 'barang_id', 'lokasi', 'diserahkan_oleh', 'penerima_nama', 'tanggal_serah_terima')
            ->orderByDesc('tanggal_serah_terima')
            ->orderByDesc('id')
            ->get()
            ->groupBy('barang_id');

        foreach ($records as $row) {
            $priorList = $allPrior->get($row->barang_id, collect());
            $prev = $priorList->first(fn($p) => $p->id < $row->id);

            $row->lokasi_asal = $prev?->lokasi ?: 'Gudang IT';
            $row->pemberi_nama = !empty($row->diserahkan_oleh) ? $row->diserahkan_oleh : ($prev?->penerima_nama ?: 'Gudang IT');
        }

        return $records;
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
    public function processHandover(array $data): array
    {
        $userId = $data['user_id'] ?? null;
        if (!$userId && !empty($data['penerima_nama'])) {
            $matchedUser = User::where('name', $data['penerima_nama'])->first();
            $userId = $matchedUser ? $matchedUser->id : null;
        }

        $createdRecords = [];

        DB::transaction(function () use ($data, $userId, &$createdRecords) {
            foreach ($data['barang_ids'] as $barangId) {
                $record = RiwayatAset::create([
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
                    'status_terima'        => 'pending',
                    'accepted_at'          => null,
                    'accepted_by'          => null,
                ]);

                $createdRecords[] = $record;

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

        $firstUuid = !empty($createdRecords) ? $createdRecords[0]->uuid : $data['no_surat'];

        return [
            'no_surat' => $data['no_surat'],
            'uuid'     => $firstUuid,
        ];
    }

    /**
     * Accept a handover by letter number or ID for a user.
     */
    public function acceptHandover(string $rawNoSurat, User $user): int
    {
        $decoded = urldecode($rawNoSurat);
        $items = $this->getReceiptItems($decoded);

        if ($items->isEmpty()) {
            throw new \InvalidArgumentException('Dokumen tanda terima tidak ditemukan.');
        }

        if ($user->isStaff()) {
            $userName = strtolower(trim($user->name));
            $isAuthorized = $items->contains(function ($item) use ($user, $userName) {
                $penerima = strtolower(trim($item->penerima_nama ?? ''));
                return $item->user_id === $user->id 
                    || ($penerima && str_contains($penerima, $userName))
                    || ($item->barang && $item->barang->isAssignedTo($user));
            });

            if (!$isAuthorized) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Anda tidak memiliki izin untuk mengonfirmasi tanda terima ini.');
            }
        }

        $affected = RiwayatAset::whereIn('id', $items->pluck('id'))
            ->update([
                'status_terima' => 'accepted',
                'accepted_at'   => now(),
                'accepted_by'   => $user->id,
            ]);

        $firstNoSurat = $items->first()->no_surat ?? ('ID #' . $items->first()->id);

        ActivityLogService::log(
            'Handover',
            'ACCEPT',
            $firstNoSurat,
            "Konfirmasi penerimaan aset pada dokumen {$firstNoSurat} (" . $items->count() . " unit perangkat telah di-accept oleh {$user->name})",
            $items->first()->barang_id
        );

        return $affected;
    }

    /**
     * Get receipt items by UUID, letter number, or numeric ID.
     */
    public function getReceiptItems(?string $rawIdentifier): EloquentCollection
    {
        if (!$rawIdentifier) {
            return new EloquentCollection();
        }

        $decoded = urldecode($rawIdentifier);

        // 1. Primary: Lookup by UUID
        $byUuid = RiwayatAset::where('uuid', $decoded)->orWhere('uuid', $rawIdentifier)->first();
        if ($byUuid) {
            if (!empty($byUuid->no_surat)) {
                return RiwayatAset::where('no_surat', $byUuid->no_surat)
                    ->with(['barang.category', 'user', 'accepter'])
                    ->get();
            }
            return new EloquentCollection([$byUuid->load(['barang.category', 'user', 'accepter'])]);
        }

        // 2. Fallback: Lookup by no_surat
        $items = RiwayatAset::where('no_surat', $decoded)
            ->orWhere('no_surat', $rawIdentifier)
            ->with(['barang.category', 'user', 'accepter'])
            ->get();

        // 3. Fallback: Lookup by numeric ID
        if ($items->isEmpty() && is_numeric($decoded)) {
            $single = RiwayatAset::with(['barang.category', 'user', 'accepter'])->find($decoded);
            if ($single) {
                if (!empty($single->no_surat)) {
                    return RiwayatAset::where('no_surat', $single->no_surat)
                        ->with(['barang.category', 'user', 'accepter'])
                        ->get();
                }
                $items = new EloquentCollection([$single]);
            }
        }

        return $items;
    }
}
