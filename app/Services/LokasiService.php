<?php

namespace App\Services;

use App\Models\LokasiUnit;
use Illuminate\Database\Eloquent\Collection;

class LokasiService
{
    /**
     * Get all locations with user and asset counts.
     */
    public function getAllWithCount(): Collection
    {
        return LokasiUnit::withCount(['users', 'barangs'])
            ->orderBy('nama_lokasi')
            ->get();
    }

    /**
     * Create a new location.
     */
    public function create(array $data): LokasiUnit
    {
        $lok = LokasiUnit::create($data);

        ActivityLogService::log('Lokasi', 'CREATE', $lok->nama_lokasi, "Menambahkan master lokasi baru: {$lok->nama_lokasi}", $lok->id);

        return $lok;
    }

    /**
     * Update an existing location.
     */
    public function update(LokasiUnit $lokasi, array $data): LokasiUnit
    {
        $lokasi->update($data);

        ActivityLogService::log('Lokasi', 'UPDATE', $lokasi->nama_lokasi, "Memperbarui data lokasi: {$lokasi->nama_lokasi}", $lokasi->id);

        return $lokasi;
    }

    /**
     * Delete a location.
     */
    public function delete(LokasiUnit $lokasi): bool
    {
        $nama = $lokasi->nama_lokasi;
        $id = $lokasi->id;
        $res = (bool) $lokasi->delete();

        if ($res) {
            ActivityLogService::log('Lokasi', 'DELETE', $nama, "Menghapus lokasi: {$nama}", $id);
        }

        return $res;
    }
}
