<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Barang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin
        User::create([
            'name' => 'Admin Inventaris',
            'email' => 'admin@kantor.com',
            'password' => bcrypt('rahasia12345'),
        ]);

        // 2. Buat Kategori
        $cat1 = Category::create(['nama_kategori' => 'Laptop']);
        $cat2 = Category::create(['nama_kategori' => 'Printer']);
        $cat3 = Category::create(['nama_kategori' => 'Network']);

        // 3. Buat Data Barang (Menggunakan kolom baru: model, no_aset_local, dll)
        Barang::create([
            'category_id' => $cat1->id,
            'no_aset_local' => 'AST-2026-001',
            'model' => 'Lenovo ThinkPad T14',
            'type_spec' => 'Core i7, 16GB RAM, 512GB SSD',
            'serial_number' => 'SN-THNK-12345',
            'hostname' => 'LT-BUDI-01',
            'unit_loc' => 'Ruang Server',
            'dept' => 'IT Department',
            'pengguna' => 'Budi Santoso',
            'status' => 'Dipinjam'
        ]);

        Barang::create([
            'category_id' => $cat3->id,
            'no_aset_local' => 'AST-2026-002',
            'model' => 'Mikrotik Router RB941',
            'serial_number' => 'SN-MKRT-9988',
            'unit_loc' => 'Lantai 2',
            'status' => 'Tersedia'
        ]);
    }
}