<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        // Mengambil data user terbaru, 10 data per halaman
        $users = User::latest()->get();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        // Mengarahkan ke halaman form tambah user
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Simpan ke database
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password wajib di-hash
        ]);

        // 3. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    // Fungsi untuk memproses update data user
    // Fungsi untuk memproses update data user
    public function update(Request $request, $id)
    {
        // 1. Validasi input (Tambahkan validasi password nullable)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // 'nullable' berarti boleh dikosongkan
        ]);

        // 2. Cari data user berdasarkan ID
        $user = User::findOrFail($id);

        // 3. Siapkan data dasar yang akan diupdate (Nama dan Email)
        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // 4. Cek apakah admin mengisi kolom password. Jika ya, enkripsi dan tambahkan!
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // 5. Eksekusi update
        $user->update($dataToUpdate);

        // 6. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    // Fungsi untuk menghapus data user
    public function destroy($id)
    {
        // Cari data user berdasarkan ID lalu hapus
        $user = User::findOrFail($id);
        $user->delete();

        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('users.index')->with('success', 'Data user berhasil dihapus!');
    }
}