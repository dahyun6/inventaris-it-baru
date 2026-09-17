<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Get all users with role and department eager loaded.
     */
    public function getAll(): Collection
    {
        return User::with(['role', 'departemen', 'lokasi'])->latest()->get();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        $roleId = isset($data['role_id']) ? (int) $data['role_id'] : 3;
        $departemenId = !empty($data['departemen_id']) ? (int) $data['departemen_id'] : null;
        $lokasiId = !empty($data['lokasi_id']) ? (int) $data['lokasi_id'] : null;

        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'role_id'       => $roleId,
            'departemen_id' => $departemenId,
            'lokasi_id'     => $lokasiId,
            'is_admin'      => in_array($roleId, [1, 2]),
        ]);

        ActivityLogService::log('User', 'CREATE', $user->name, "Menambahkan akun user baru: {$user->name} ({$user->email})", $user->id);

        return $user;
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $data): User
    {
        $updateData = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        if (array_key_exists('role_id', $data)) {
            $roleId = !empty($data['role_id']) ? (int) $data['role_id'] : 3;
            $updateData['role_id'] = $roleId;
            $updateData['is_admin'] = in_array($roleId, [1, 2]);
        }

        if (array_key_exists('departemen_id', $data)) {
            $updateData['departemen_id'] = !empty($data['departemen_id']) ? (int) $data['departemen_id'] : null;
        }

        if (array_key_exists('lokasi_id', $data)) {
            $updateData['lokasi_id'] = !empty($data['lokasi_id']) ? (int) $data['lokasi_id'] : null;
        }

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        ActivityLogService::log('User', 'UPDATE', $user->name, "Memperbarui profil user {$user->name} ({$user->email})", $user->id);

        return $user;
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        $nama = $user->name;
        $id = $user->id;
        $res = (bool) $user->delete();

        if ($res) {
            ActivityLogService::log('User', 'DELETE', $nama, "Menghapus akun user {$nama}", $id);
        }

        return $res;
    }
}
