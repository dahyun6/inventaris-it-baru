<?php

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;

class VendorService
{
    /**
     * Get all vendors with barangs count.
     */
    public function getAllWithCount(): Collection
    {
        return Vendor::withCount('barangs')->orderBy('nama_vendor')->get();
    }

    /**
     * Create a new vendor.
     */
    public function create(array $data): Vendor
    {
        $vendor = Vendor::create($data);

        ActivityLogService::log('Vendor', 'CREATE', $vendor->nama_vendor, "Menambahkan vendor/mitra baru: {$vendor->nama_vendor}", $vendor->id);

        return $vendor;
    }

    /**
     * Update an existing vendor.
     */
    public function update(Vendor $vendor, array $data): Vendor
    {
        $vendor->update($data);

        ActivityLogService::log('Vendor', 'UPDATE', $vendor->nama_vendor, "Memperbarui data vendor: {$vendor->nama_vendor}", $vendor->id);

        return $vendor;
    }

    /**
     * Delete a vendor.
     */
    public function delete(Vendor $vendor): bool
    {
        $nama = $vendor->nama_vendor;
        $id = $vendor->id;
        $res = (bool) $vendor->delete();

        if ($res) {
            ActivityLogService::log('Vendor', 'DELETE', $nama, "Menghapus data vendor: {$nama}", $id);
        }

        return $res;
    }
}
