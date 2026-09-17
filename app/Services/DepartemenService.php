<?php

namespace App\Services;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Collection;

class DepartemenService
{
    /**
     * Get all departments with user count.
     */
    public function getAllWithCount(): Collection
    {
        return Departemen::withCount(['users', 'barangs'])
            ->orderBy('nama_departemen')
            ->get();
    }

    /**
     * Create a new department.
     */
    public function create(array $data): Departemen
    {
        $dept = Departemen::create($data);

        ActivityLogService::log('Departemen', 'CREATE', $dept->nama_departemen, "Menambahkan departemen baru: {$dept->nama_departemen}", $dept->id);

        return $dept;
    }

    /**
     * Update an existing department.
     */
    public function update(Departemen $departemen, array $data): Departemen
    {
        $departemen->update($data);

        ActivityLogService::log('Departemen', 'UPDATE', $departemen->nama_departemen, "Memperbarui data departemen: {$departemen->nama_departemen}", $departemen->id);

        return $departemen;
    }

    /**
     * Delete a department.
     */
    public function delete(Departemen $departemen): bool
    {
        $nama = $departemen->nama_departemen;
        $id = $departemen->id;
        $res = (bool) $departemen->delete();

        if ($res) {
            ActivityLogService::log('Departemen', 'DELETE', $nama, "Menghapus departemen: {$nama}", $id);
        }

        return $res;
    }
}
